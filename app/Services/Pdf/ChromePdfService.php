<?php

namespace App\Services\Pdf;

use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Gera PDF a partir de HTML usando Chromium (Chrome/Edge) em modo headless.
 *
 * Diferente do wkhtmltopdf, o renderizador é o mesmo do navegador, então o PDF
 * sai idêntico ao preview HTML. Como o Chrome só sabe gravar o PDF em arquivo
 * (não escreve em stdout), usamos arquivos temporários que são removidos logo
 * em seguida - nada fica persistido em disco.
 */
class ChromePdfService
{
    public function fromHtml(string $html): string
    {
        $dir = storage_path('app/tmp-pdf');

        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException("Não foi possível criar o diretório temporário: {$dir}");
        }

        $id         = uniqid('nota_', true);
        $htmlPath   = $dir . DIRECTORY_SEPARATOR . $id . '.html';
        $pdfPath    = $dir . DIRECTORY_SEPARATOR . $id . '.pdf';
        // Profile único por chamada: o Chrome cria um SingletonLock dentro do
        // user-data-dir, então dois processos apontando para o MESMO profile
        // ao mesmo tempo travam/falham. Um profile por invocação isola a
        // geração concorrente (2 workers, preview durante um job, etc.).
        $profileDir = $dir . DIRECTORY_SEPARATOR . 'profile-' . $id;

        file_put_contents($htmlPath, $html);

        try {
            $process = new Process($this->comando($htmlPath, $pdfPath, $profileDir));
            $process->setTimeout(config('chromepdf.timeout'));
            $process->run();

            // O Chrome pode retornar exit code != 0 mesmo tendo gerado o PDF,
            // então a existência do arquivo é o critério confiável de sucesso.
            if (!is_file($pdfPath) || filesize($pdfPath) === 0) {
                throw new RuntimeException(
                    'Chrome headless não gerou o PDF. Saída: ' . trim($process->getErrorOutput() ?: $process->getOutput())
                );
            }

            return file_get_contents($pdfPath);
        } finally {
            @unlink($htmlPath);
            @unlink($pdfPath);
            $this->removerDiretorio($profileDir);
        }
    }

    /**
     * Remove recursivamente o profile temporário do Chrome (arquivos + subpastas).
     */
    private function removerDiretorio(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $itens = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($itens as $item) {
            $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }

        @rmdir($dir);
    }

    private function comando(string $htmlPath, string $pdfPath, string $profileDir): array
    {
        $args = [
            config('chromepdf.binary'),
            '--headless=new',
            '--disable-gpu',
            '--no-sandbox',
            '--no-first-run',
            '--no-default-browser-check',
            '--disable-extensions',
            // O usuário do Apache normalmente não acessa o perfil padrão do Chrome.
            '--user-data-dir=' . $profileDir,
            // Remove o cabeçalho/rodapé padrão do Chrome (URL, data, nº da página).
            '--no-pdf-header-footer',
            '--virtual-time-budget=' . config('chromepdf.virtual_time_budget'),
            '--print-to-pdf=' . $pdfPath,
        ];

        if (config('chromepdf.ignore_certificate_errors')) {
            $args[] = '--ignore-certificate-errors';
        }

        $args[] = 'file:///' . str_replace('\\', '/', $htmlPath);

        return $args;
    }
}
