<?php

namespace App\Models;

use Carbon\Carbon;
use Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LotePcpRecap extends Model
{
    use HasFactory;

    public function verificaPneusLotePcpRecapAberto($nrLote)
    {
        $query = "
           SELECT
                COUNT(OPR.STORDEM) OP_ABERTA
            FROM LOTEPCPORDEMPRODUCAORECAP LPP
            INNER JOIN ORDEMPRODUCAORECAP OPR ON (OPR.ID = LPP.IDORDEMPRODUCAO)
            WHERE LPP.IDMONTAGEMLOTEPCP = :nrLote
            AND OPR.STORDEM = 'A'            
            GROUP BY OPR.STORDEM
        ";

        return DB::connection('firebird')->select(
            $query,
            ['nrLote' => $nrLote]
        )[0]->OP_ABERTA ?? 0;
    }

    public function fecharLotePcpRecap(int $nrLote, int $cdEmpresa)
    {
        $query = "
            UPDATE MONTAGEMLOTEPCPRECAP
            SET STLOTE = 'F'
            WHERE ID = :nrLote
            AND IDEMPRESA = :cdEmpresa
        ";

        return DB::connection('firebird')->update(
            $query,
            [
                'nrLote' => $nrLote,
                'cdEmpresa' => $cdEmpresa
            ]
        );
    }

    public function getControleLotePcpRecap($cdEmpresa)
    {
        $query = "
            SELECT
                *
            FROM CONTROLELOTEPCPRECAP CLP
            WHERE CLP.IDEMPRESA IN ($cdEmpresa)
        ";

        return DB::connection('firebird')->select(
            $query
        );
    }

    public function salvarLotePcpRecap(array $data)
    {
        return DB::transaction(function () use ($data) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            //buscar o ultimo ID valido para o lote
            $ultimoId = DB::connection('firebird')->table('MONTAGEMLOTEPCPRECAP')
                ->where('IDEMPRESA', $data['empresa'])
                ->max('ID');

            //buscar a sequencia do lote do dia para o lote_pcp e empresa selecionada
            $sequenciaLote = DB::connection('firebird')->table('MONTAGEMLOTEPCPRECAP')
                ->where('IDEMPRESA', $data['empresa'])
                ->where('DTPRODUCAO', date('d.m.Y', strtotime($data['data_producao'])))
                ->max('NRLOTESEQDIA');

            $hrInicioLote = DB::connection('firebird')->table('CONTROLELOTEPCPRECAP')
                ->where('IDEMPRESA', $data['empresa'])
                ->where('ID', $data['lote_pcp'])
                ->select('HRINICIO')->first()->HRINICIO ?? '06:00:00';

            $dtProducao = date('Y-m-d', strtotime($data['data_producao']));

            $query = "
            INSERT INTO MONTAGEMLOTEPCPRECAP (ID, IDEMPRESA, IDCONTROLELOTEPCPRECAP, IDEXECUTORETAPA, NRLOTESEQDIA, STLOTE,
                                  HRINICIOLOTE, DTPRODUCAO, DTREGISTRO)
            VALUES (:nrLote, :cdEmpresa, :idControleLote, :idExecutorEtapa, :nrLoteSeqDia, :stLote, :hrInicioLote, :dtProducao, CURRENT_TIMESTAMP)

            RETURNING ID
        ";

            $result = DB::connection('firebird')->selectOne(
                $query,
                [
                    'cdEmpresa' => $data['empresa'],
                    'nrLote' => $ultimoId + 1,
                    'idControleLote' => $data['lote_pcp'],
                    'idExecutorEtapa' => $data['responsavel'],
                    'nrLoteSeqDia' => $sequenciaLote + 1,
                    'stLote' => 'P',
                    'hrInicioLote' => $hrInicioLote,
                    'dtProducao' => $dtProducao
                ]
            );

            // Após inserir o lote, precisamos inserir as etapas relacionadas a esse lote na tabela MONTAGEMLOTEPCPETAPARECAP
            $this->insertMontagemLotePcpEtapaRecap($result->ID, $data['lote_pcp'], $data['empresa'], $data['data_producao']);

            return [
                'nr_lote' => $result->ID
            ];
        });
    }

    public function insertMontagemLotePcpEtapaRecap($nrLote, $nrControlePCP, $cdEmpresa, $dtProducao)
    {
        return DB::transaction(function () use ($nrLote, $nrControlePCP, $cdEmpresa, $dtProducao) {

            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            // Buscar as etapas na ordem correta
            $querySelectEtapas = "
                SELECT
                    CL.ID,
                    CL.IDEMPRESA,
                    CL.IDCONTROLELOTEPCP,
                    CL.IDETAPA,
                    CL.NRORDEM,
                    CL.HRFIM
                FROM CONTROLELOTEPCPETAPARECAP CL
                WHERE
                    CL.IDCONTROLELOTEPCP = :nrControlePCP
                    AND CL.IDEMPRESA = :cdEmpresa
                ORDER BY CL.NRORDEM ASC
            ";

            $etapas = DB::connection('firebird')->select(
                $querySelectEtapas,
                [
                    'nrControlePCP' => $nrControlePCP,
                    'cdEmpresa' => $cdEmpresa
                ]
            );

            // Extrair somente os horários (12:00, 20:00, 01:00, etc.)
            $horarios = array_map(function ($etapa) {
                return $etapa->HRFIM;
            }, $etapas);

            // Gerar as datas completas para cada etapa
            // Retorna um array de objetos Carbon na mesma ordem das etapas
            $datasFim = $this->gerarEtapasProducao($dtProducao, $horarios);

            //buscar o ultimo ID valido para o lote
            $ultimoId = DB::connection('firebird')->table('MONTAGEMLOTEPCPETAPARECAP')
                ->where('IDEMPRESA', $cdEmpresa)
                ->max('ID') + 1;


            foreach ($etapas as $index => $etapa) {

                // Data/hora calculada para esta etapa
                $dtFim = $datasFim[$index];

                $queryInsertEtapa = "
               INSERT INTO MONTAGEMLOTEPCPETAPARECAP (
                    ID,
                    IDEMPRESA,
                    IDMONTAGEMLOTEPCPRECAP,
                    IDETAPA,
                    HRFIM,
                    DTFIM,
                    DTREGISTRO)
                VALUES (
                        :Id,
                        :cdEmpresa,
                        :nrLote,
                        :idEtapa,
                        :hrFim,
                        :dtFim,
                        CURRENT_TIMESTAMP);
            ";

                DB::connection('firebird')->select(
                    $queryInsertEtapa,
                    [
                        'Id' => $ultimoId++,
                        'cdEmpresa' => $cdEmpresa,
                        'nrLote' => $nrLote,
                        'idEtapa' => $etapa->IDETAPA,
                        'hrFim' => $etapa->HRFIM,
                        'dtFim' => $dtFim->format('d.m.Y'),
                    ]
                );
            }
        });
    }

    public function gerarEtapasProducao(string $dataInicio, array $horarios): array
    {
        $dataAtual = Carbon::createFromFormat('Y-m-d', $dataInicio);

        $resultado = [];
        $horarioAnterior = null;

        foreach ($horarios as $horario) {
            [$hora, $minuto] = explode(':', $horario);

            // Converte para minutos para facilitar comparação
            $minutosAtual = ((int)$hora * 60) + (int)$minuto;

            // Se o horário atual for menor ou igual ao anterior,
            // significa que passou da meia-noite e deve avançar um dia.
            if ($horarioAnterior !== null && $minutosAtual <= $horarioAnterior) {
                $dataAtual->addDay();
            }

            // Monta a data/hora da etapa
            $dataEtapa = $dataAtual->copy()->setTime((int)$hora, (int)$minuto);

            $resultado[] = $dataEtapa;

            // Guarda para a próxima comparação
            $horarioAnterior = $minutosAtual;
        }

        return $resultado;
    }

    public function getListLotePcpRecapEmProducao($cdEmpresa)
    {
        $query = "
            SELECT
                MLP.ID AS NR_LOTE,
                'LOTE ' || MLP.ID || ' - ' ||
                    LPAD(EXTRACT(DAY FROM MLP.DTPRODUCAO), 2, '0') || '/' ||
                    LPAD(EXTRACT(MONTH FROM MLP.DTPRODUCAO), 2, '0') || '/' ||
                    EXTRACT(YEAR FROM MLP.DTPRODUCAO) AS DSLOTEPCP,
                MLP.IDEMPRESA,
                MLP.IDCONTROLELOTEPCPRECAP,
                MLP.IDEXECUTORETAPA,
                MLP.NRLOTESEQDIA,
                MLP.STLOTE,
                MLP.HRINICIOLOTE,
                MLP.DTPRODUCAO,
                MLP.DTREGISTRO
            FROM MONTAGEMLOTEPCPRECAP MLP
            WHERE MLP.IDEMPRESA IN (:cdEmpresa)
            AND MLP.STLOTE IN ('A', 'P')
        ";

        return DB::connection('firebird')->select(
            $query,
            ['cdEmpresa' => $cdEmpresa]
        );
    }

    public function atualizaOrdensLotePneusLotePCP(int $cdEmpresa, int $nrLoteNovo, int $nrLoteAntigo, int $nrOrdemProducao)
    {

        return DB::transaction(function () use ($cdEmpresa, $nrLoteNovo, $nrLoteAntigo, $nrOrdemProducao) {
            DB::connection('firebird')->select("EXECUTE PROCEDURE GERA_SESSAO");

            $query = "                    
                    UPDATE LOTEPCPORDEMPRODUCAORECAP 
                        SET IDMONTAGEMLOTEPCP = :nrLoteNovo, 
                        DTREGISTRO = CURRENT_TIMESTAMP
                    WHERE
                        IDMONTAGEMLOTEPCP = :nrLoteAntigo
                        AND IDEMPRESA = :cdEmpresa
                        AND IDORDEMPRODUCAO = :nrOrdemProducao;
                ";

            DB::connection('firebird')->update(
                $query,
                [
                    'cdEmpresa' => $cdEmpresa,
                    'nrLoteNovo' => $nrLoteNovo,
                    'nrLoteAntigo' => $nrLoteAntigo,
                    'nrOrdemProducao' => $nrOrdemProducao
                ]
            );
        });
    }

    public function listaPneusLoteSemPCP(int $cdEmpresa, $pedidopneu = null, $ordemProducao = null)
    {
        $query = "
            SELECT
                PP.IDEMPRESA,
                PP.DTEMISSAO,
                PESSOA.CD_PESSOA,
                PESSOA.NM_PESSOA,
                PP.ID NR_PEDIDO,
                OPR.ID NR_ORDEM,
                OPR.ID || ' - ' || IPP.NRSEQUENCIA || '/' ||(SELECT
                                                                MAX(IPP2.NRSEQUENCIA)
                                                            FROM ITEMPEDIDOPNEU IPP2
                                                            WHERE IPP2.IDPEDIDOPNEU = IPP.IDPEDIDOPNEU) AS ID,
                SP.ID||'-'||SP.DSSERVICO DS_ITEM
            FROM ORDEMPRODUCAORECAP OPR
            LEFT JOIN LOTEPCPORDEMPRODUCAORECAP PCP ON (PCP.IDORDEMPRODUCAO = OPR.ID)
            INNER JOIN ITEMPEDIDOPNEU IPP ON (IPP.ID = OPR.IDITEMPEDIDOPNEU)
            INNER JOIN SERVICOPNEU SP ON (SP.ID = IPP.IDSERVICOPNEU)
            INNER JOIN PEDIDOPNEU PP ON (PP.ID = IPP.IDPEDIDOPNEU)
            INNER JOIN PESSOA ON (PESSOA.CD_PESSOA = PP.IDPESSOA)
            LEFT JOIN EXAMEINICIAL EI ON (EI.IDORDEMPRODUCAORECAP = OPR.ID)
            WHERE IPP.STCANCELADO = 'N'
                AND IPP.STGARANTIA = 'N'
                AND PP.IDEMPRESA IN (:cdEmpresa)
                AND OPR.STORDEM = 'A'
                AND PESSOA.ST_ATIVA = 'S'
                AND EI.ID IS NULL
                AND SP.STATIVO = 'S'
                " . ($pedidopneu ? " AND PP.ID IN (" . implode(',', $pedidopneu) . ") " : "") . "
                " . ($ordemProducao ? " AND OPR.ID IN (" . implode(',', $ordemProducao) . ") " : "") . "
                AND PCP.IDMONTAGEMLOTEPCP IS NULL  
                
                ORDER BY PP.DTEMISSAO DESC, OPR.ID ASC, IPP.NRSEQUENCIA ASC
        ";

        $data = DB::connection('firebird')->select(
            $query,
            ['cdEmpresa' => $cdEmpresa]
        );

        return Helper::ConvertFormatText($data);
    }

    public function salvarPneusLotePCP(int $cdEmpresa, int $nrLoteNovo, array $idsOrdens)
    {
        return DB::transaction(function () use ($cdEmpresa, $nrLoteNovo, $idsOrdens) {

            DB::connection('firebird')
                ->select("EXECUTE PROCEDURE GERA_SESSAO");

            foreach ($idsOrdens as $idOrdem) {
                DB::connection('firebird')
                    ->table('LOTEPCPORDEMPRODUCAORECAP')
                    ->insert([
                        'IDMONTAGEMLOTEPCP' => $nrLoteNovo,
                        'IDEMPRESA' => $cdEmpresa,
                        'IDORDEMPRODUCAO' => $idOrdem,
                        'DTREGISTRO' => now(),
                    ]);
            }
        });
    }
}
