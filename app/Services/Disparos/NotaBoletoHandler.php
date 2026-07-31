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
            $this->criarPendenteDeNota($contexto, $nota);
        }
    }

    public function criarPendenteAvulso(object $contexto, int $nrLancamento, int $cdPessoa): ?int
    {
        $data = $this->notaModel->getListNotaCliente($nrLancamento, (string) $cdPessoa);

        return $this->criarPendenteDeNota($contexto, $data[0]);
    }

    private function criarPendenteDeNota(object $contexto, object $nota): ?int
    {
        return $this->envioModel->criarPendente([
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

    /**
     * Monta o e-mail completo (corpo + todos os PDFs ja gerados) - usado pelo
     * envio de verdade (EnviaDisparoAutomaticoJob), que precisa anexar tudo.
     */
    public function montarEmail(object $envio): array
    {
        $itens = $this->estruturaAnexos($envio);

        return [
            'corpo'  => $this->montarCorpo($envio, $itens),
            'anexos' => array_map(fn($item) => $this->gerarAnexoPdf($item), $itens),
        ];
    }

    /**
     * Mesma coisa que montarEmail(), mas sem gerar nenhum PDF - so os titulos/
     * nomes dos anexos, pra listar na tela de preview sem custar Chromium.
     */
    public function montarPreview(object $envio): array
    {
        $itens = $this->estruturaAnexos($envio);

        return [
            'corpo'  => $this->montarCorpo($envio, $itens),
            'anexos' => array_map(fn($item) => [
                'titulo' => $item['titulo'],
                'nome'   => $item['nome'],
            ], $itens),
        ];
    }

    /**
     * Gera o PDF/HTML de UM anexo especifico, sob demanda (clique no botao do
     * preview) - nao toca nos outros anexos.
     */
    public function gerarAnexo(object $envio, int $indice): array
    {
        $itens = $this->estruturaAnexos($envio);

        if (!isset($itens[$indice])) {
            throw new \OutOfRangeException("Anexo {$indice} não encontrado.");
        }

        return $this->gerarAnexoPdf($itens[$indice]);
    }

    /**
     * Monta a "receita" de cada anexo (nota + boletos em aberto) com os dados
     * ja buscados no banco, mas sem renderizar HTML nem gerar PDF - e o que
     * montarEmail()/montarPreview()/gerarAnexo() compartilham, pra so buscar
     * os dados uma vez e decidir depois o que efetivamente vira PDF.
     */
    private function estruturaAnexos(object $envio): array
    {
        $cdPessoa = (string) $envio->CD_PESSOA;

        $data = $this->notaModel->getListNotaCliente($envio->NR_LANCAMENTO, $cdPessoa);
        $itens = [
            ['tipo' => 'nota', 'titulo' => 'Nota Fiscal', 'nome' => 'nota.pdf', 'dados' => $data],
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
                'titulo' => "Boleto {$numero}",
                'nome'   => "boleto_{$numero}.pdf",
                'dados'  => $boletoData[0],
            ];
        }

        return $itens;
    }

    /**
     * Renderiza o HTML do item (nota ou boleto) e gera o PDF via Chromium -
     * unico ponto que efetivamente custa uma invocacao do Chrome.
     */
    private function gerarAnexoPdf(array $item): array
    {
        if ($item['tipo'] === 'nota') {
            $data = $item['dados'];
            $layout = (new NotaLayoutData())->build($data);
            $html = view(NotaLayoutData::viewName($data[0]->CD_EMPRESA), $layout)->render();
        } else {
            $boleto = $item['dados'];
            $codigo_barras = Helper::codigoBarrasHtml($boleto->DS_CODIGOBARRA);
            $html = view('admin.layouts.layout-boleto-atz', compact('codigo_barras', 'boleto'))->render();
        }

        return [
            'titulo'   => $item['titulo'],
            'nome'     => $item['nome'],
            // Chromium headless: renderizacao identica ao preview HTML.
            'conteudo' => $this->chromePdf->fromHtml($html),
            'html'     => $html,
        ];
    }

    private function montarCorpo(object $envio, array $itens): string
    {
        $data = $itens[0]['dados'];

        return view('emails.disparo-automatico.nota-boleto', [
            'nmPessoa'  => $envio->NM_PESSOA,
            'nrNota'    => $data[0]->NR_NOTA,
            // count($itens) > 1: se sobrou algum boleto de verdade alem da nota
            // (item 0) - corrige um caso em que o texto dizia "e o(s) boleto(s)"
            // mesmo quando nenhum boleto tinha dado gerado.
            'temBoleto' => count($itens) > 1,
            // Contato da empresa que emitiu a nota (nao da Cambe fixo) - mesma
            // fonte usada no cabecalho da nota (NM_EMPRESA/NR_FONEEMPRESA/DS_EMAILEMPRESA).
            'nmEmpresa'    => $data[0]->NM_EMPRESA,
            'foneEmpresa'  => $data[0]->NR_FONEEMPRESA,
            'emailEmpresa' => $data[0]->DS_EMAILEMPRESA,
        ])->render();
    }
}
