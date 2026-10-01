<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    public function collection()
    {
        return Product::with('category')->get();
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->product_code,
            $product->product_name,
            $product->product_description,
            $product->category->name ?? '-',
            $product->status === 1 ? 'Active' : 'Deactivated',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Code',
            'Name',
            'Description',
            'Category',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'E9ECEF']
            ]
        ]);

        $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ]);

        return $sheet;
    }
}
