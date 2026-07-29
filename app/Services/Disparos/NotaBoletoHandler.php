<?php

namespace App\Services\Disparos;

use App\Models\BoletoCliente;
use App\Models\DisparoEnvio;
use App\Models\NotaCliente;
use App\Services\Nota\NotaLayoutData;
use App\Services\Pdf\ChromePdfService;
use Helper;
use Illuminate\Support\Carbon;

class NotaBoletoHandler implements DisparoHandlerInterface
{
    public function __construct(
        private NotaCliente $notaModel,
        private BoletoCliente $boletoModel,
        private DisparoEnvio $envioModel,
        private ChromePdfService $chromePdf,
    ) {}

    public function gerarPendentes(object $contexto): void
    {
        // Marca d'água: só notas registradas depois da última execução.
        // Na primeira execução (sem última) usa a data de início do contexto.
        $dtMin = Carbon::parse($contexto->DT_ULTIMAEXECUCAO ?: $contexto->DT_INICIOENVIO)
            ->format('Y-m-d H:i:s');

        $notas = $this->notaModel->getListNotaCliente(
            null,
            null,
            null,
            $dtMin
        );

        foreach ($notas as $nota) {
            // Sem e-mail cadastrado, criarPendente() ja registra a linha como
            // 'E'/"Não possui email cadastrado" em vez de pular - fica visivel
            // na tela em vez de sumir silenciosamente.
            $this->envioModel->criarPendente([
                'cd_contexto'   => $contexto->CD_CONTEXTO,
                'cd_empresa'    => $nota->CD_EMPRESA,
                'nr_lancamento' => $nota->NR_LANCAMENTO,
                'cd_serie'      => $nota->CD_SERIE,
                'tp_nota'       => $nota->TP_NOTA,
                'cd_pessoa'     => $nota->CD_PESSOA,
                'nm_pessoa'     => $nota->NM_PESSOA,
                'ds_emaildest'  => $nota->DS_EMAIL,
                'ds_emailcopia' => $nota->DS_EMAILCOPIA,
                'ds_emailrem'   => $nota->DS_EMAILEMPRESA,
                'ds_assunto'    => 'Nota Fiscal ' . $nota->NR_NOTA . ' - ' . $nota->NM_EMPRESA,
            ]);
        }
    }

    public function montarEmail(object $envio): array
    {
        $cdPessoa = (string) $envio->CD_PESSOA;
        $anexos = [];

        // --- PDF da Nota ---
        $data = $this->notaModel->getListNotaCliente($envio->NR_LANCAMENTO, $cdPessoa);
        $layout = (new NotaLayoutData())->build($data);
        $htmlNota = view(NotaLayoutData::viewName($data[0]->CD_EMPRESA), $layout)->render();
        // Chromium headless: renderizacao identica ao preview HTML.
        $pdfNota = $this->chromePdf->fromHtml($htmlNota);
        $anexos[] = ['titulo' => 'Nota Fiscal', 'nome' => 'nota.pdf', 'conteudo' => $pdfNota, 'html' => $htmlNota];

        // --- PDF(s) do Boleto (parcelas em aberto vinculadas a essa nota) ---
        $parcelas = array_values(array_filter(
            $this->boletoModel->BoletoResumo($cdPessoa),
            fn($b) => (int) $b->NR_LANCTONOTA === (int) $envio->NR_LANCAMENTO
                && (int) $b->CD_EMPRESA === (int) $envio->CD_EMPRESA
        ));

        foreach ($parcelas as $i => $parcela) {
            $boletoData = $this->boletoModel->Boleto($parcela->NR_LANCAMENTO, $parcela->CD_EMPRESA, $parcela->NR_PARC, $cdPessoa);

            if (empty($boletoData)) {
                continue;
            }

            $boleto = $boletoData[0];
            $codigo_barras = Helper::codigoBarrasHtml($boleto->DS_CODIGOBARRA);
            $htmlBoleto = view('admin.layouts.layout-boleto-atz', compact('codigo_barras', 'boleto'))->render();
            $pdfBoleto = $this->chromePdf->fromHtml($htmlBoleto);

            $numero = $i + 1;
            $anexos[] = ['titulo' => "Boleto {$numero}", 'nome' => "boleto_{$numero}.pdf", 'conteudo' => $pdfBoleto, 'html' => $htmlBoleto];
        }

        return [
            'corpo'  => view('emails.disparo-automatico.nota-boleto', [
                'nmPessoa'      => $envio->NM_PESSOA,
                'nrNota'        => $data[0]->NR_NOTA,
                'temBoleto'     => count($parcelas) > 0,
                // Contato da empresa que emitiu a nota (nao da Cambe fixo) - mesma
                // fonte usada no cabecalho da nota (NM_EMPRESA/NR_FONEEMPRESA/DS_EMAILEMPRESA).
                'nmEmpresa'     => $data[0]->NM_EMPRESA,
                'foneEmpresa'   => $data[0]->NR_FONEEMPRESA,
                'emailEmpresa'  => $data[0]->DS_EMAILEMPRESA,
            ])->render(),
            'anexos' => $anexos,
        ];
    }
}
