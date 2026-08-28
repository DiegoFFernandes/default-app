<?php

namespace App\Services\Disparos;

use App\Models\BoletoCliente;
use App\Models\NotaCliente;
use App\Services\Nota\NotaLayoutData;
use App\Services\Nota\NotaLayoutDataNacional;
use App\Services\Pdf\ChromePdfService;
use Helper;

/**
 * Monta e renderiza os anexos (nota + boletos em aberto) de um envio de
 * Disparo Automático - compartilhado entre os handlers de e-mail e WhatsApp,
 * que só diferem em como o conteúdo é entregue, não em como ele é montado.
 */
class NotaBoletoAnexos
{
    public function __construct(
        private NotaCliente $notaModel,
        private BoletoCliente $boletoModel,
        private ChromePdfService $chromePdf,
    ) {}

    /**
     * Monta a "receita" de cada anexo (nota + boletos em aberto) com os dados
     * ja buscados no banco, mas sem renderizar HTML nem gerar PDF - quem chama
     * decide depois o que efetivamente vira PDF (gerarAnexoPdf).
     */
    public function estruturaAnexos(object $envio): array
    {
        $cdPessoa = (string) $envio->CD_PESSOA;

        $data = NotaLayoutData::isNacional($envio->CD_EMPRESA)
            ? $this->notaModel->getListNotaClienteNacional($envio->NR_LANCAMENTO, $cdPessoa)
            : $this->notaModel->getListNotaCliente($envio->NR_LANCAMENTO, $cdPessoa);
        // Numero da nota no nome/titulo de todos os anexos (nota + boletos) -
        // ajuda o cliente a identificar o arquivo certo numa busca no WhatsApp.
        $nrNota = $data[0]->NR_NOTA;

        $itens = [
            [
                'tipo' => 'nota',
                'titulo' => "Nota Fiscal {$nrNota}",
                'nome' => "Nota_{$nrNota}.pdf",
                'dados' => $data
            ],
        ];

        $parcelas = array_values(array_filter(
            $this->boletoModel->BoletoResumo($cdPessoa),
            fn($b) => (int) $b->NR_LANCTONOTA === (int) $envio->NR_LANCAMENTO
                && (int) $b->CD_EMPRESA === (int) $envio->CD_EMPRESA
        ));

        $numero = 0;

        foreach ($parcelas as $parcela) {
            $boletoData = $this->boletoModel->Boleto($parcela->NR_LANCAMENTO, $parcela->CD_EMPRESA, $parcela->NR_PARC, $cdPessoa);

            if (empty($boletoData)) {
                continue;
            }

            $numero++;
            $itens[] = [
                'tipo'   => 'boleto',
                'titulo' => "Boleto {$nrNota} - {$numero}º Vencimento em {$boletoData[0]->DT_VENC}",
                'nome'   => "Boleto_{$nrNota}_{$numero}.pdf",
                'dados'  => $boletoData[0],
            ];
        }

        return $itens;
    }

    /**
     * Renderiza o HTML do item (nota ou boleto) e gera o PDF via Chromium -
     * unico ponto que efetivamente custa uma invocacao do Chrome.
     */
    public function gerarAnexoPdf(array $item): array
    {
        $html = $this->renderizarItemHtml($item);

        return [
            'titulo'   => $item['titulo'],
            'nome'     => $item['nome'],
            // Chromium headless: renderizacao identica ao preview HTML.
            'conteudo' => $this->chromePdf->fromHtml($html),
            'html'     => $html,
        ];
    }

    /**
     * Junta o HTML de varios boletos (separados por quebra de pagina) e gera
     * um unico PDF - usado pelo canal WhatsApp, que so aceita 1 anexo por
     * template na API oficial. E-mail continua mandando cada boleto avulso
     * (via gerarAnexoPdf por item), esse metodo nao mexe naquele fluxo.
     */
    public function gerarAnexoPdfBoletos(array $itensBoletos, string $nrNota): array
    {
        $html = implode(
            '<div class="page-break"></div>',
            array_map(fn($item) => $this->renderizarItemHtml($item), $itensBoletos)
        );

        return [
            'titulo'   => 'Boleto(s) ' . $nrNota,
            'nome'     => "Boletos_{$nrNota}.pdf",
            'conteudo' => $this->chromePdf->fromHtml($html),
            'html'     => $html,
        ];
    }

    private function renderizarItemHtml(array $item): string
    {
        if ($item['tipo'] === 'nota') {
            $data = $item['dados'];
            $cdEmpresa = $data[0]->CD_EMPRESA;
            $layout = NotaLayoutData::isNacional($cdEmpresa)
                ? (new NotaLayoutDataNacional())->build($data)
                : (new NotaLayoutData())->build($data);

            return view(NotaLayoutData::viewName($cdEmpresa), $layout)->render();
        }

        $boleto = $item['dados'];
        $codigo_barras = Helper::codigoBarrasHtml($boleto->DS_CODIGOBARRA);

        return view('admin.layouts.layout-boleto-atz', compact('codigo_barras', 'boleto'))->render();
    }
}
