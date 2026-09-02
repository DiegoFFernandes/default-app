<?php

namespace App\Models;

use Carbon\Carbon;
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
                TP_CANAL,
                HR_EXECUCAO,
                NR_TENTATIVAS,
                NR_INTERVALOHORAS,
                NR_LIMITEDIARIO,
                HR_JANELAINICIO,
                HR_JANELAFIM,
                ST_ATIVO,
                DT_INICIOENVIO,
                DT_ULTIMAEXECUCAO,
                DT_PROXIMAEXECUCAO
            FROM DISPARO_CONTEXTO
            ORDER BY DS_CONTEXTO
        "));
    }

    public function find(int $id)
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT CD_CONTEXTO, DS_CONTEXTO, CD_HANDLER, TP_CANAL, HR_EXECUCAO, NR_TENTATIVAS, NR_INTERVALOHORAS,
                   NR_LIMITEDIARIO, HR_JANELAINICIO, HR_JANELAFIM,
                   ST_ATIVO, DT_INICIOENVIO, DT_ULTIMAEXECUCAO, DT_PROXIMAEXECUCAO
            FROM DISPARO_CONTEXTO
            WHERE CD_CONTEXTO = :id
        ", ['id' => $id]);

        return $row ? Helper::ConvertFormatText([$row])[0] : null;
    }

    /**
     * Contextos ativos que ja estao no horario (DT_PROXIMAEXECUCAO <= agora, ou
     * nunca executaram ainda). Cada contexto tem seu proprio intervalo
     * (NR_INTERVALOHORAS) - por isso o filtro de "esta na hora" e por linha,
     * nao um horario global do schedule.
     *
     * $force ignora o DT_PROXIMAEXECUCAO (continua respeitando ST_ATIVO) - usado
     * pelo --force do comando manual, pra nao precisar esperar o intervalo
     * configurado quando alguem quer forcar o envio na hora.
     *
     * DT_AGORA é o "agora" do servidor Firebird capturado atomicamente com o SELECT;
     * vira a próxima marca d'água (via marcarExecutado) para não haver buraco nem
     * sobreposição entre execuções.
     */
    public function ativosParaExecucao(bool $force = false): array
    {
        $filtroHorario = $force ? '' : "AND (DT_PROXIMAEXECUCAO IS NULL OR DT_PROXIMAEXECUCAO <= CURRENT_TIMESTAMP)";

        return Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT CD_CONTEXTO, DS_CONTEXTO, CD_HANDLER, NR_TENTATIVAS, NR_INTERVALOHORAS, HR_EXECUCAO,
                   DT_INICIOENVIO, DT_ULTIMAEXECUCAO, CURRENT_TIMESTAMP AS DT_AGORA
            FROM DISPARO_CONTEXTO
            WHERE ST_ATIVO = 'S'
                AND TP_CANAL = 'E'
                {$filtroHorario}
        "));
    }

    /**
     * Contextos WhatsApp ativos - sem DT_PROXIMAEXECUCAO (esse canal nao usa
     * NR_INTERVALOHORAS/HR_EXECUCAO): o comando de WhatsApp roda com um tick
     * proprio e mais frequente, e decide envio a envio (ver
     * ExecutarDisparosWhatsApp) usando NR_LIMITEDIARIO/HR_JANELA* daqui.
     */
    public function ativosWpp(): array
    {
        return Helper::ConvertFormatText(DB::connection('firebird')->select("
            SELECT CD_CONTEXTO, DS_CONTEXTO, CD_HANDLER, NR_TENTATIVAS, NR_LIMITEDIARIO,
                   HR_JANELAINICIO, HR_JANELAFIM, DT_INICIOENVIO, DT_ULTIMAEXECUCAO,
                   CURRENT_TIMESTAMP AS DT_AGORA
            FROM DISPARO_CONTEXTO
            WHERE ST_ATIVO = 'S'
                AND TP_CANAL = 'W'
        "));
    }

    /**
     * Avança so a marca d'agua (usada por gerarPendentes()) sem mexer em
     * DT_PROXIMAEXECUCAO - o canal WhatsApp nao usa esse agendamento, cada
     * tick do comando so precisa saber "notas registradas depois de quando".
     */
    public function marcarUltimaExecucao(int $id, string $dtAgora): void
    {
        // A marca d'agua avança so até "agora - carência" (config
        // disparo-automatico.carencia_minutos), nao até "agora" puro - senao
        // uma nota registrada a menos tempo que a carência ficaria pra trás
        // da marca d'água e nunca mais seria reconsiderada pelo
        // gerarPendentes() (ver NotaBoletoWppHandler).
        $dtMarcaDagua = Carbon::parse($dtAgora)
            ->subMinutes((int) config('disparo-automatico.carencia_minutos'))
            ->format('Y-m-d H:i:s');

        DB::connection('firebird')->statement("
            UPDATE DISPARO_CONTEXTO SET DT_ULTIMAEXECUCAO = :dt WHERE CD_CONTEXTO = :id
        ", ['dt' => $dtMarcaDagua, 'id' => $id]);
    }

    /**
     * Avança a marca d'água e recalcula DT_PROXIMAEXECUCAO. Recebe o mesmo
     * DT_AGORA lido em ativosParaExecucao() para que notas registradas durante
     * o processamento (DT_REGISTRO > DT_AGORA) fiquem para a próxima janela,
     * sem serem perdidas.
     *
     * Intervalo < 24h: rolante (DT_AGORA + N horas) - roda a cada N horas
     * corridas, sem se importar com o horario do dia.
     * Intervalo >= 24h: ancorado no HR_EXECUCAO - sempre no mesmo horario do
     * dia, sem "andar" com o tempo (ex.: 24h = uma vez por dia, as 08:00).
     */
    public function marcarExecutado(int $id, string $dtAgora, int $intervaloHoras, ?string $horaExecucao): void
    {
        $dtProxima = ($intervaloHoras >= 24 && $horaExecucao)
            ? $this->proximaExecucaoAncorada($dtAgora, $horaExecucao, $intervaloHoras)
            : Carbon::parse($dtAgora)->addHours($intervaloHoras);

        // A marca d'agua (usada como limite inferior por gerarPendentes())
        // avança so até "agora - carência", nao até "agora" puro - mesmo
        // motivo do marcarUltimaExecucao() do WhatsApp. DT_PROXIMAEXECUCAO
        // (acima) continua calculado a partir do "agora" real - e sobre
        // quando o AGENDAMENTO roda de novo, nao sobre a carência da nota.
        $dtMarcaDagua = Carbon::parse($dtAgora)
            ->subMinutes((int) config('disparo-automatico.carencia_minutos'))
            ->format('Y-m-d H:i:s');

        DB::connection('firebird')->statement("
            UPDATE DISPARO_CONTEXTO SET DT_ULTIMAEXECUCAO = :dt, DT_PROXIMAEXECUCAO = :dt_proxima WHERE CD_CONTEXTO = :id
        ", [
            'dt'         => $dtMarcaDagua,
            'dt_proxima' => $dtProxima->format('Y-m-d H:i:s'),
            'id'         => $id,
        ]);
    }

    /**
     * Proxima ocorrencia do HR_EXECUCAO (hoje, se ainda nao passou; senao avança
     * de N em N horas ate cair no futuro) - mantem o horario do dia fixo em vez
     * de derivar de quando a execucao efetivamente aconteceu.
     */
    private function proximaExecucaoAncorada(string $dtAgora, string $horaExecucao, int $intervaloHoras): Carbon
    {
        $agora = Carbon::parse($dtAgora);
        $proxima = Carbon::parse($agora->format('Y-m-d') . ' ' . $horaExecucao);

        while ($proxima->lessThanOrEqualTo($agora)) {
            $proxima->addHours($intervaloHoras);
        }

        return $proxima;
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

    /**
     * Primeiro contexto cadastrado para um handler - usado quando uma nota ainda
     * nao tem nenhuma linha em DISPARO_ENVIO (status 'P') e o usuario aciona o
     * envio manualmente, sem ter um CD_CONTEXTO pronto para reaproveitar.
     */
    public function porHandler(string $cdHandler): ?object
    {
        $row = DB::connection('firebird')->selectOne("
            SELECT CD_CONTEXTO, DS_CONTEXTO, CD_HANDLER, HR_EXECUCAO, NR_TENTATIVAS, ST_ATIVO, DT_INICIOENVIO, DT_ULTIMAEXECUCAO
            FROM DISPARO_CONTEXTO
            WHERE CD_HANDLER = :cd_handler
            ORDER BY CD_CONTEXTO
        ", ['cd_handler' => $cdHandler]);

        return $row ? Helper::ConvertFormatText([$row])[0] : null;
    }

    /**
     * Existe pelo menos um contexto ativo? Usado para avisar o usuario quando
     * o disparo automatico proprio nem foi configurado ainda (so o Junsoft).
     */
    public function existeAtivo(): bool
    {
        return (bool) DB::connection('firebird')->selectOne("
            SELECT FIRST 1 CD_CONTEXTO FROM DISPARO_CONTEXTO WHERE ST_ATIVO = 'S'
        ");
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

    public function updateHorario(int $id, string $horario, int $intervaloHoras): void
    {
        DB::connection('firebird')->statement("
            UPDATE DISPARO_CONTEXTO SET HR_EXECUCAO = :horario, NR_INTERVALOHORAS = :intervalo WHERE CD_CONTEXTO = :id
        ", ['horario' => $horario, 'intervalo' => $intervaloHoras, 'id' => $id]);
    }

    public function updateWhatsApp(int $id, int $limiteDiario, string $janelaInicio, string $janelaFim): void
    {
        DB::connection('firebird')->statement("
            UPDATE DISPARO_CONTEXTO
            SET NR_LIMITEDIARIO = :limite, HR_JANELAINICIO = :inicio, HR_JANELAFIM = :fim
            WHERE CD_CONTEXTO = :id
        ", ['limite' => $limiteDiario, 'inicio' => $janelaInicio, 'fim' => $janelaFim, 'id' => $id]);
    }
}
