<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DisparoEnvio extends Model
{
    public function search(array $filtros)
    {
        $where = ['CAST(DE.DT_REGISTRO AS DATE) BETWEEN :inicio AND :fim'];
        $bindings = [
            'inicio' => $filtros['inicio_data'],
            'fim'    => $filtros['fim_data'],
        ];

        if (!empty($filtros['cd_contexto'])) {
            $where[] = 'DE.CD_CONTEXTO = :cd_contexto';
            $bindings['cd_contexto'] = $filtros['cd_contexto'];
        }

        if (!empty($filtros['st_envio'])) {
            $where[] = 'DE.ST_ENVIO = :st_envio';
            $bindings['st_envio'] = $filtros['st_envio'];
        }

        if (!empty($filtros['nm_pessoa'])) {
            $where[] = 'DE.NM_PESSOA CONTAINING :nm_pessoa';
            $bindings['nm_pessoa'] = Helper::ToIso($filtros['nm_pessoa']);
        }

        if (!empty($filtros['cd_pessoa'])) {
            $where[] = 'DE.CD_PESSOA = :cd_pessoa';
            $bindings['cd_pessoa'] = $filtros['cd_pessoa'];
        }

        $query = '
            SELECT
                DE.CD_ENVIO,
                DE.CD_CONTEXTO,
                DC.DS_CONTEXTO,
                DE.CD_EMPRESA,
                DE.NR_LANCAMENTO,
                DE.CD_SERIE,
                DE.TP_NOTA,
                DE.CD_PESSOA,
                DE.NM_PESSOA,
                DE.DS_EMAILDEST,
                DE.DS_ASSUNTO,
                DE.ST_ENVIO,
                DE.DS_MOTIVO,
                DE.NR_TENTATIVAS,
                DE.DT_REGISTRO,
                DE.DT_ENVIO
            FROM DISPARO_ENVIO DE
            INNER JOIN DISPARO_CONTEXTO DC ON (DC.CD_CONTEXTO = DE.CD_CONTEXTO)
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY DE.DT_REGISTRO DESC
        ';

        return Helper::ConvertFormatText(DB::connection('firebird')->select($query, $bindings));
    }

    public function find(int $id)
    {
        $row = DB::connection('firebird')->selectOne('
            SELECT
                CD_ENVIO, CD_CONTEXTO, CD_EMPRESA, NR_LANCAMENTO, CD_SERIE, TP_NOTA,
                CD_PESSOA, NM_PESSOA, DS_EMAILDEST, DS_EMAILCOPIA, DS_EMAILREM, DS_ASSUNTO,
                ST_ENVIO, DS_MOTIVO, NR_TENTATIVAS, DT_REGISTRO, DT_ENVIO
            FROM DISPARO_ENVIO
            WHERE CD_ENVIO = :id
        ', ['id' => $id]);

        return $row ? Helper::ConvertFormatText([$row])[0] : null;
    }

    /**
     * Cria a linha de envio pendente ('A'). Retorna null se ja existir para essa
     * nota+contexto (protegido pela constraint UK_DISPARO_ENVIO_NOTA).
     *
     * Sem DS_EMAILDEST nao ha para quem enviar - em vez de ficar preso em 'A'
     * esperando um e-mail que nunca vai aparecer, ja nasce 'E' com o motivo
     * (DS_EMAILCOPIA sozinho nao conta - copia e opcional, so o destinatario
     * e obrigatorio).
     */
    public function criarPendente(array $dados): ?int
    {
        $existe = DB::connection('firebird')->selectOne('
            SELECT CD_ENVIO FROM DISPARO_ENVIO
            WHERE CD_CONTEXTO = :cd_contexto AND CD_EMPRESA = :cd_empresa
                AND NR_LANCAMENTO = :nr_lancamento AND CD_SERIE = :cd_serie AND TP_NOTA = :tp_nota
        ', [
            'cd_contexto'   => $dados['cd_contexto'],
            'cd_empresa'    => $dados['cd_empresa'],
            'nr_lancamento' => $dados['nr_lancamento'],
            'cd_serie'      => $dados['cd_serie'],
            'tp_nota'       => $dados['tp_nota'],
        ]);

        if ($existe) {
            return null;
        }

        $id = DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_DISPARO_ENVIO, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;

        $semEmail = empty($dados['ds_emaildest']);

        try {
            DB::connection('firebird')->statement('
                INSERT INTO DISPARO_ENVIO (
                    CD_ENVIO, CD_CONTEXTO, CD_EMPRESA, NR_LANCAMENTO, CD_SERIE, TP_NOTA,
                    CD_PESSOA, NM_PESSOA, DS_EMAILDEST, DS_EMAILCOPIA, DS_EMAILREM, DS_ASSUNTO,
                    ST_ENVIO, DS_MOTIVO, DT_ENVIO
                ) VALUES (
                    :id, :cd_contexto, :cd_empresa, :nr_lancamento, :cd_serie, :tp_nota,
                    :cd_pessoa, :nm_pessoa, :ds_emaildest, :ds_emailcopia, :ds_emailrem, :ds_assunto,
                    :st_envio, :ds_motivo, :dt_envio
                )
            ', [
                'id'            => $id,
                'cd_contexto'   => $dados['cd_contexto'],
                'cd_empresa'    => $dados['cd_empresa'],
                'nr_lancamento' => $dados['nr_lancamento'],
                'cd_serie'      => $dados['cd_serie'],
                'tp_nota'       => $dados['tp_nota'],
                'cd_pessoa'     => $dados['cd_pessoa'],
                'nm_pessoa'     => Helper::ToIso($dados['nm_pessoa']),
                'ds_emaildest'  => $dados['ds_emaildest'],
                'ds_emailcopia' => $dados['ds_emailcopia'] ?? null,
                'ds_emailrem'   => $dados['ds_emailrem'],
                'ds_assunto'    => Helper::ToIso($dados['ds_assunto']),
                'st_envio'      => $semEmail ? 'F' : 'A',
                'ds_motivo'     => $semEmail ? Helper::ToIso('Não possui email cadastrado') : null,
                'dt_envio'      => $semEmail ? now() : null,
            ]);
        } catch (\Throwable $e) {
            // Corrida com outra execucao que ja criou esse envio - a constraint UK_DISPARO_ENVIO_NOTA protegeu.
            return null;
        }

        return $id;
    }

    public function pendentes(int $cdContexto): array
    {
        return Helper::ConvertFormatText(DB::connection('firebird')->select('
            SELECT CD_ENVIO FROM DISPARO_ENVIO WHERE CD_CONTEXTO = :cd_contexto AND ST_ENVIO = \'A\'
        ', ['cd_contexto' => $cdContexto]));
    }

    /**
     * Marca um envio com falha ('F') para nova tentativa, com o contador de
     * tentativas zerado - senao, com NR_TENTATIVAS ja no limite do contexto,
     * uma unica falha nova mandaria de volta pra 'F' sem passar pelo retry.
     */
    public function reenviar(int $id): void
    {
        DB::connection('firebird')->statement("
            UPDATE DISPARO_ENVIO SET ST_ENVIO = 'A', NR_TENTATIVAS = 0, DS_MOTIVO = NULL WHERE CD_ENVIO = :id
        ", ['id' => $id]);
    }

    /**
     * Corrige o e-mail de destino de um envio (ex.: erro de digitacao que
     * causou a falha). Nao muda ST_ENVIO - use reenviar() para reativar.
     */
    public function atualizarEmailDestino(int $id, string $email): void
    {
        DB::connection('firebird')->statement('
            UPDATE DISPARO_ENVIO SET DS_EMAILDEST = :email WHERE CD_ENVIO = :id
        ', ['email' => $email, 'id' => $id]);
    }

    public function marcarEnviado(int $id): void
    {
        DB::connection('firebird')->statement('
            UPDATE DISPARO_ENVIO SET ST_ENVIO = \'E\', DT_ENVIO = CURRENT_TIMESTAMP WHERE CD_ENVIO = :id
        ', ['id' => $id]);
    }

    /**
     * Marca como enviado com ressalva (ST_ENVIO='V') - quando o destinatário
     * principal e/ou alguma cópia tinha domínio inválido, mas pelo menos um
     * destinatário de verdade recebeu o e-mail. Não é falha total (não entra no
     * ciclo de retry) nem sucesso completo - o motivo fica salvo para o usuário
     * decidir se corrige e reenvia.
     */
    public function marcarEnviadoComFalha(int $id, string $motivo): void
    {
        DB::connection('firebird')->statement("
            UPDATE DISPARO_ENVIO
            SET ST_ENVIO = 'V', DT_ENVIO = CURRENT_TIMESTAMP, DS_MOTIVO = :motivo
            WHERE CD_ENVIO = :id
        ", [
            'motivo' => Helper::ToIso(mb_substr($motivo, 0, 500)),
            'id'     => $id,
        ]);
    }

    /**
     * Registra uma falha de envio. Se ainda nao atingiu o limite de tentativas do
     * contexto, mantem ST_ENVIO = 'A' para nova tentativa; senao marca 'F' (definitiva).
     */
    public function registrarFalha(int $id, string $motivo, int $maxTentativas): string
    {
        $envio = $this->find($id);
        $tentativas = $envio->NR_TENTATIVAS + 1;
        $statusFinal = $tentativas >= $maxTentativas ? 'F' : 'A';

        DB::connection('firebird')->statement('
            UPDATE DISPARO_ENVIO
            SET NR_TENTATIVAS = :tentativas, ST_ENVIO = :status, DS_MOTIVO = :motivo
            WHERE CD_ENVIO = :id
        ', [
            'tentativas' => $tentativas,
            'status'     => $statusFinal,
            'motivo'     => Helper::ToIso(mb_substr($motivo, 0, 500)),
            'id'         => $id,
        ]);

        return $statusFinal;
    }

    /**
     * Registra, apenas para auditoria, o que foi anexado ao e-mail (o PDF em si nao e
     * persistido em disco - e gerado em memoria no envio e descartado logo em seguida).
     */
    public function salvarAnexo(int $cdEnvio, string $titulo, string $caminho): void
    {
        $id = DB::connection('firebird')
            ->selectOne('SELECT GEN_ID(GEN_DISPARO_ENVIO_ANEXO, 1) AS NEW_ID FROM RDB$DATABASE')
            ->NEW_ID;

        DB::connection('firebird')->statement('
            INSERT INTO DISPARO_ENVIO_ANEXO (CD_ANEXO, CD_ENVIO, DS_TITULO, DS_CAMINHOANEXO)
            VALUES (:id, :cd_envio, :titulo, :caminho)
        ', [
            'id'       => $id,
            'cd_envio' => $cdEnvio,
            'titulo'   => Helper::ToIso($titulo),
            'caminho'  => $caminho,
        ]);
    }

    public function anexos(int $cdEnvio): array
    {
        return Helper::ConvertFormatText(DB::connection('firebird')->select('
            SELECT DS_TITULO, DS_CAMINHOANEXO FROM DISPARO_ENVIO_ANEXO WHERE CD_ENVIO = :id
        ', ['id' => $cdEnvio]));
    }
}
