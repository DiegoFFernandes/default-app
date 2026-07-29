<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DisparoContexto extends Model
{
    public function getAll()
    {
        return Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT
                CD_CONTEXTO,
                DS_CONTEXTO,
                CD_HANDLER,
                HR_EXECUCAO,
                NR_TENTATIVAS,
                ST_ATIVO,
                DT_INICIOENVIO,
                DT_ULTIMAEXECUCAO
            FROM DISPARO_CONTEXTO
            ORDER BY DS_CONTEXTO
        "));
    }

    public function find(int $id)
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT CD_CONTEXTO, DS_CONTEXTO, CD_HANDLER, HR_EXECUCAO, NR_TENTATIVAS, ST_ATIVO, DT_INICIOENVIO, DT_ULTIMAEXECUCAO
            FROM DISPARO_CONTEXTO
            WHERE CD_CONTEXTO = :id
        ", ['id' => $id]);

        return $row ? Helper::ConvertFormatText([$row])[0] : null;
    }

    /**
     * Todos os contextos ativos. Modelo de marca d'água (executa a cada hora):
     * a seleção do que enviar é feita pela janela DT_ULTIMAEXECUCAO → DT_AGORA,
     * não por horário fixo do dia.
     *
     * DT_AGORA é o "agora" do servidor Firebird capturado atomicamente com o SELECT;
     * vira a próxima marca d'água (via marcarExecutado) para não haver buraco nem
     * sobreposição entre execuções.
     */
    public function ativosParaExecucao(): array
    {
        return Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT CD_CONTEXTO, DS_CONTEXTO, CD_HANDLER, NR_TENTATIVAS,
                   DT_INICIOENVIO, DT_ULTIMAEXECUCAO, CURRENT_TIMESTAMP AS DT_AGORA
            FROM DISPARO_CONTEXTO
            WHERE ST_ATIVO = 'S'
        "));
    }

    /**
     * Avança a marca d'água. Recebe o mesmo DT_AGORA lido em ativosParaExecucao()
     * para que notas registradas durante o processamento (DT_REGISTRO > DT_AGORA)
     * fiquem para a próxima janela, sem serem perdidas.
     */
    public function marcarExecutado(int $id, string $dtAgora): void
    {
        DB::connection('firebird')->statement("
            UPDATE DISPARO_CONTEXTO SET DT_ULTIMAEXECUCAO = :dt WHERE CD_CONTEXTO = :id
        ", ['dt' => $dtAgora, 'id' => $id]);
    }

    /**
     * Menor DT_INICIOENVIO entre os contextos (ou de um contexto específico) -
     * usado para travar buscas de notas emitidas antes dessa data, já que para
     * trás nada foi enviado (tudo apareceria como "Pendente" sem sentido).
     */
    public function dataInicioMaisAntiga(?int $cdContexto = null): ?string
    {
        $row = DB::connection('firebird')->selectOne('
            SELECT MIN(DT_INICIOENVIO) AS DT_INICIO
            FROM DISPARO_CONTEXTO
            ' . ($cdContexto ? 'WHERE CD_CONTEXTO = :cd_contexto' : ''),
            $cdContexto ? ['cd_contexto' => $cdContexto] : []
        );

        return $row->DT_INICIO ?? null;
    }

    public function toggleAtivo(int $id): string
    {
        $atual = $this->find($id);
        $novoStatus = $atual->ST_ATIVO === 'S' ? 'N' : 'S';

        DB::connection('firebird')->statement("
            UPDATE DISPARO_CONTEXTO SET ST_ATIVO = :st WHERE CD_CONTEXTO = :id
        ", ['st' => $novoStatus, 'id' => $id]);

        return $novoStatus;
    }

    public function updateHorario(int $id, string $horario): void
    {
        DB::connection('firebird')->statement("
            UPDATE DISPARO_CONTEXTO SET HR_EXECUCAO = :horario WHERE CD_CONTEXTO = :id
        ", ['horario' => $horario, 'id' => $id]);
    }
}
