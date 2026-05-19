<?php

namespace App\Models;

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
                    'stLote' => 'A',
                    'hrInicioLote' => $hrInicioLote,
                    'dtProducao' => $dtProducao
                ]
            );

            return [
                'nr_lote' => $result->ID
            ];
        });
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
            WHERE MLP.IDEMPRESA IN ($cdEmpresa)
            AND MLP.STLOTE IN ('A', 'P')
        ";

        return DB::connection('firebird')->select(
            $query
        );
    }

    public function atualizaOrdensLotePneusLotePCP(int $cdEmpresa, int $nrLoteNovo, int $nrLoteAntigo, int $nrOrdemProducao){
        
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
}
