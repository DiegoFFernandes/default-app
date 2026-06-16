<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class SolicitacaoCompraExport implements FromArray, WithStyles
{
    protected int   $itemHeaderRow;
    protected int   $itemStartRow;
    protected int   $itemEndRow;
    protected int   $totalRow;
    protected int   $proposalHeaderRow;
    protected int   $signatureRow;
    protected array $infoRows = [];

    public function __construct(
        protected \stdClass $solicitacao,
        protected array     $itens
    ) {}

    public function array(): array
    {
        $sol   = $this->solicitacao;
        $itens = $this->itens;
        $rows  = [];

        // Cabeçalho
        $rows[] = [$sol->NM_EMPRESA,          '', '', '', '', '', ''];
        $rows[] = ['SOLICITAÇÃO DE COMPRA  Nº ' . str_pad($sol->CD_SOLICITACAO, 6, '0', STR_PAD_LEFT), '', '', '', '', '', ''];
        $rows[] = ['Emitido em: ' . now()->format('d/m/Y'), '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', ''];

        // Info: empresa / data
        $rows[] = ['Empresa:', $sol->NM_EMPRESA, '', 'Data:', Carbon::parse($sol->DT_SOLICITACAO)->format('d/m/Y'), '', ''];
        $this->infoRows['empresa'] = count($rows);

        // Info: justificativa (B:G merged)
        $rows[] = ['Justificativa:', $sol->DS_JUSTIFICATIVA, '', '', '', '', ''];
        $this->infoRows['justificativa'] = count($rows);

        // Info: observação (opcional, B:G merged)
        if ($sol->DS_OBSERVACAO) {
            $rows[] = ['Observação:', $sol->DS_OBSERVACAO, '', '', '', '', ''];
            $this->infoRows['observacao'] = count($rows);
        }

        $rows[] = ['', '', '', '', '', '', ''];

        // Cabeçalho dos itens
        $this->itemHeaderRow = count($rows) + 1;
        $rows[] = ['Nº', 'Produto / Descrição', 'Qtd.', 'Un.', 'Observação', 'Valor Unit. (R$)', 'Valor Total (R$)'];

        // Itens
        $this->itemStartRow = count($rows) + 1;
        foreach ($itens as $idx => $item) {
            $rows[] = [
                $idx + 1,
                $item->DS_ITEM,
                (float) $item->QT_ITEM,
                $item->DS_UNIDADE,
                $item->DS_OBSERVACAO ?? '',
                '',   // Valor Unit — fornecedor preenche
                '',   // Valor Total — fornecedor preenche
            ];
        }
        $this->itemEndRow = count($rows);

        // Linha de total — A:E = label, F:G = fornecedor preenche
        $rows[] = ['VALOR TOTAL DA PROPOSTA', '', '', '', '', '', ''];
        $this->totalRow = count($rows);

        $rows[] = ['', '', '', '', '', '', ''];

        // Seção do fornecedor
        $rows[] = ['DADOS DA PROPOSTA — A SER PREENCHIDO PELO FORNECEDOR', '', '', '', '', '', ''];
        $this->proposalHeaderRow = count($rows);

        $rows[] = ['Condição de Pagamento:', '', '', 'Prazo de Entrega:', '', '', ''];
        $rows[] = ['Validade da Proposta:', '', '', 'Frete:', '', '', ''];
        $rows[] = ['Observações do Fornecedor:', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', ''];

        // Assinatura
        $rows[] = ['Fornecedor — Assinatura e Carimbo', '', '', '', 'Data: ___/___/______', '', ''];
        $this->signatureRow = count($rows);

        return $rows;
    }

    public function styles(Worksheet $sheet): void
    {
        $lastCol  = 'G';
        $fillUnlock = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFDE7']]];

        // ---- Linha 1: empresa ----
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1A1A1A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ---- Linha 2: título ----
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'C0392B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // ---- Linha 3: emitido em ----
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '888888']],
        ]);

        // ---- Info: empresa / data ----
        $r = $this->infoRows['empresa'];
        $sheet->getStyle("A{$r}")->getFont()->setBold(true);
        $sheet->getStyle("D{$r}")->getFont()->setBold(true);
        $sheet->mergeCells("B{$r}:C{$r}");
        $sheet->mergeCells("E{$r}:{$lastCol}{$r}");
        $sheet->getRowDimension($r)->setRowHeight(18);

        // ---- Info: justificativa ----
        $r = $this->infoRows['justificativa'];
        $sheet->getStyle("A{$r}")->getFont()->setBold(true);
        $sheet->mergeCells("B{$r}:{$lastCol}{$r}");
        $sheet->getRowDimension($r)->setRowHeight(18);

        // ---- Info: observação ----
        if (isset($this->infoRows['observacao'])) {
            $r = $this->infoRows['observacao'];
            $sheet->getStyle("A{$r}")->getFont()->setBold(true);
            $sheet->mergeCells("B{$r}:{$lastCol}{$r}");
            $sheet->getRowDimension($r)->setRowHeight(18);
        }

        // ---- Cabeçalho dos itens ----
        $h = $this->itemHeaderRow;
        $sheet->getStyle("A{$h}:{$lastCol}{$h}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->getRowDimension($h)->setRowHeight(20);

        // ---- Itens ----
        if ($this->itemStartRow <= $this->itemEndRow) {
            $sheet->getStyle("A{$this->itemStartRow}:{$lastCol}{$this->itemEndRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
            $sheet->getStyle("A{$this->itemStartRow}:A{$this->itemEndRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$this->itemStartRow}:D{$this->itemEndRow}")
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            for ($row = $this->itemStartRow; $row <= $this->itemEndRow; $row++) {
                if (($row - $this->itemStartRow) % 2 !== 0) {
                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F8F8']],
                    ]);
                }
                $sheet->getRowDimension($row)->setRowHeight(16);
            }
        }

        // ---- Linha de total ----
        $t = $this->totalRow;
        $sheet->mergeCells("A{$t}:E{$t}");
        $sheet->getStyle("A{$t}:E{$t}")->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECF0F1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);
        $sheet->getStyle("F{$t}:G{$t}")->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECF0F1']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);
        $sheet->getRowDimension($t)->setRowHeight(18);

        // ---- Seção do fornecedor ----
        $p = $this->proposalHeaderRow;
        $sheet->mergeCells("A{$p}:{$lastCol}{$p}");
        $sheet->getStyle("A{$p}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($p)->setRowHeight(20);

        // Condição / Prazo
        $p1 = $p + 1;
        $sheet->getStyle("A{$p1}")->getFont()->setBold(true);
        $sheet->getStyle("D{$p1}")->getFont()->setBold(true);
        $sheet->mergeCells("B{$p1}:C{$p1}");
        $sheet->mergeCells("E{$p1}:{$lastCol}{$p1}");
        $sheet->getStyle("B{$p1}:C{$p1}")->applyFromArray([
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '333333']]],
        ]);
        $sheet->getStyle("E{$p1}:{$lastCol}{$p1}")->applyFromArray([
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '333333']]],
        ]);
        $sheet->getRowDimension($p1)->setRowHeight(22);

        // Validade / Frete
        $p2 = $p + 2;
        $sheet->getStyle("A{$p2}")->getFont()->setBold(true);
        $sheet->getStyle("D{$p2}")->getFont()->setBold(true);
        $sheet->mergeCells("B{$p2}:C{$p2}");
        $sheet->mergeCells("E{$p2}:{$lastCol}{$p2}");
        $sheet->getStyle("B{$p2}:C{$p2}")->applyFromArray([
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '333333']]],
        ]);
        $sheet->getStyle("E{$p2}:{$lastCol}{$p2}")->applyFromArray([
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '333333']]],
        ]);
        $sheet->getRowDimension($p2)->setRowHeight(22);

        // Observações (label + área livre)
        $p3 = $p + 3;
        $sheet->getStyle("A{$p3}")->getFont()->setBold(true);
        $sheet->mergeCells("B{$p3}:{$lastCol}{$p3}");
        $sheet->getRowDimension($p3)->setRowHeight(18);

        $obsStart = $p + 4;
        $obsEnd   = $p + 7;
        $sheet->mergeCells("A{$obsStart}:{$lastCol}{$obsEnd}");
        $sheet->getStyle("A{$obsStart}:{$lastCol}{$obsEnd}")->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '333333']]],
        ]);
        for ($row = $obsStart; $row <= $obsEnd; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(28);
        }

        // ---- Assinatura ----
        $sig = $this->signatureRow;
        $sheet->mergeCells("A{$sig}:D{$sig}");
        $sheet->mergeCells("E{$sig}:{$lastCol}{$sig}");
        $sheet->getStyle("A{$sig}:D{$sig}")->applyFromArray([
            'font'      => ['size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '333333']]],
        ]);
        $sheet->getStyle("E{$sig}:{$lastCol}{$sig}")->applyFromArray([
            'font'      => ['size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '333333']]],
        ]);
        $sheet->getRowDimension($sig)->setRowHeight(28);

        // ---- Larguras das colunas ----
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(42);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(32);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);

        // ---- Fonte padrão ----
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        // ==== PROTEÇÃO DA PLANILHA ====
        // Por padrão todas as células ficam travadas ao ativar proteção.
        // Desbloqueamos apenas os campos que o fornecedor deve preencher.

        // Valor Unit. e Valor Total de cada item
        if ($this->itemStartRow <= $this->itemEndRow) {
            $sheet->getStyle("F{$this->itemStartRow}:G{$this->itemEndRow}")
                ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
            $sheet->getStyle("F{$this->itemStartRow}:G{$this->itemEndRow}")
                ->applyFromArray($fillUnlock);
        }

        // F e G da linha de total geral
        $sheet->getStyle("F{$this->totalRow}:G{$this->totalRow}")
            ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle("F{$this->totalRow}:G{$this->totalRow}")
            ->applyFromArray($fillUnlock);

        // Condição de Pagamento e Prazo de Entrega
        $sheet->getStyle("B{$p1}:C{$p1}")
            ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle("B{$p1}:C{$p1}")->applyFromArray($fillUnlock);

        $sheet->getStyle("E{$p1}:{$lastCol}{$p1}")
            ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle("E{$p1}:{$lastCol}{$p1}")->applyFromArray($fillUnlock);

        // Validade da Proposta e Frete
        $sheet->getStyle("B{$p2}:C{$p2}")
            ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle("B{$p2}:C{$p2}")->applyFromArray($fillUnlock);

        $sheet->getStyle("E{$p2}:{$lastCol}{$p2}")
            ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle("E{$p2}:{$lastCol}{$p2}")->applyFromArray($fillUnlock);

        // Área de observações do fornecedor
        $sheet->getStyle("A{$obsStart}:{$lastCol}{$obsEnd}")
            ->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle("A{$obsStart}:{$lastCol}{$obsEnd}")->applyFromArray($fillUnlock);

        // Ativa a proteção sem senha
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setInsertRows(false);
        $sheet->getProtection()->setDeleteRows(false);
        $sheet->getProtection()->setSort(false);
        $sheet->getProtection()->setSelectLockedCells(false);
    }
}
