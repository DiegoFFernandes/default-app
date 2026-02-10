<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RequisicaoBorrachariaExcel implements FromArray, WithHeadings, WithStyles
{
    protected $datas;
    protected $hierarquia;

    public function __construct($datas, $hierarquia)
    {
        $this->datas = $datas;
        $this->hierarquia = $hierarquia;
    }

    /**
     * @return array
     */

    public function array(): array
    {
        $linhas = [];

        foreach ($this->hierarquia as $gerente) {
            $linhas[] = [
                $gerente['nome'],
                null,
                null,
                null,
                null,
                $gerente['qtd_item'],
                $gerente['vl_comissao']
            ];
            foreach ($gerente['supervisores'] as $supervisor) {
                $linhas[] = [
                    null,
                    $supervisor['nome'],
                    null,
                    null,
                    null,
                    $supervisor['qtd_item'],
                    $supervisor['vl_comissao']
                ];
                foreach ($supervisor['vendedores'] as $vendedor) {
                    $linhas[] = [
                        null,
                        null,
                        $vendedor['nome'],
                        null,
                        null,
                        $vendedor['qtd_item'],
                        $vendedor['vl_comissao']
                    ];
                    foreach ($vendedor['borracheiros'] as $borracheiro) {
                        $linhas[] = [
                            null,
                            null,
                            null,
                            $borracheiro['nome'],
                            null,
                            $borracheiro['qtd_item'],
                            $borracheiro['vl_comissao']
                        ];
                        foreach ($borracheiro['clientes'] as $cliente) {
                            $linhas[] = [
                                null,
                                null,
                                null,
                                null,
                                $cliente['PESSOA'],
                                $cliente['QTD_ITEM'],
                                $cliente['VL_COMISSAO']
                            ];
                        }
                    }
                }
            }
        }

        return $linhas;
    }

    public function headings(): array
    {
        return [
            'Gerente',
            'Supervisor',
            'Vendedor',
            'Borracheiro',
            'Pessoa',
            'Qtd. Itens',
            'Valor Comissão'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $ultimaLinha = $sheet->getHighestRow();

        // HEADER
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Altura da linha do header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Começa em 2 porque 1 é o cabeçalho
        for ($row = 2; $row <= $ultimaLinha; $row++) {

            // GERENTE → coluna A
            if ($sheet->getCell("A{$row}")->getValue()) {
                $sheet->getStyle("A{$row}:G{$row}")
                    ->getFont()->setBold(true);
            }

            // SUPERVISOR → coluna B
            elseif ($sheet->getCell("B{$row}")->getValue()) {
                $sheet->getStyle("B{$row}:G{$row}")
                    ->getFont()->setBold(true);
            }

            // VENDEDOR → coluna C
            elseif ($sheet->getCell("C{$row}")->getValue()) {
                $sheet->getStyle("C{$row}:G{$row}")
                    ->getFont()->setBold(true);
            }

            // BORRACHEIRO → coluna D
            elseif ($sheet->getCell("D{$row}")->getValue()) {
                $sheet->getStyle("D{$row}:G{$row}")
                    ->getFont()->setBold(true);
            }
        }

        // Quantidade centralizada
        $sheet->getStyle("F2:F{$ultimaLinha}")
            ->getAlignment()->setHorizontal('center');

        // Moeda à direita
        $sheet->getStyle("G2:G{$ultimaLinha}")
            ->getAlignment()->setHorizontal('right');

        $sheet->getStyle("G2:G{$ultimaLinha}")
            ->getNumberFormat()
            ->setFormatCode(' #,##0.00');

        // LARGURA FIXA (hierarquia)
        $sheet->getColumnDimension('A')->setWidth(12); // Gerente
        $sheet->getColumnDimension('B')->setWidth(12); // Supervisor
        $sheet->getColumnDimension('C')->setWidth(12); // Vendedor
        $sheet->getColumnDimension('D')->setWidth(12); // Borracheiro

        foreach (range('E', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
