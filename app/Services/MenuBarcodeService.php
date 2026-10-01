<?php

namespace App\Services;

use TCPDF;
use App\Models\Menu;

class MenuBarcodeService
{
    private const PAGE_WIDTH = 210;
    private const PAGE_HEIGHT = 297;
    private const MARGIN = 10;
    private const CELL_SPACING = 2;
    private const ROWS = 9;
    private const COLS = 4;
    
    private const CELL_WIDTH = 45;
    private const CELL_HEIGHT = 29;
    
    private const BARCODE_WIDTH = 40;   
    private const BARCODE_HEIGHT = 20;  
    private const X_OFFSET = 3;         
    private const Y_OFFSET = 5;         
    
    private const FONT_SIZE = 8;

    public function generateBarcodePDF(string $code, int $quantity): string
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Menu Barcode');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(self::MARGIN, self::MARGIN, self::MARGIN);
        $pdf->SetAutoPageBreak(true, self::MARGIN);

        $barcodeCount = 0;
        $barcodesPerPage = self::ROWS * self::COLS;
        $totalPages = ceil($quantity / $barcodesPerPage);

        for ($page = 0; $page < $totalPages && $barcodeCount < $quantity; $page++) {
            $pdf->AddPage();

            for ($row = 0; $row < self::ROWS && $barcodeCount < $quantity; $row++) {
                for ($col = 0; $col < self::COLS && $barcodeCount < $quantity; $col++) {
                    $cellX = self::MARGIN + ($col * (self::CELL_WIDTH + self::CELL_SPACING));
                    $cellY = self::MARGIN + ($row * (self::CELL_HEIGHT + self::CELL_SPACING));
                    
                    $pdf->Rect($cellX, $cellY, self::CELL_WIDTH, self::CELL_HEIGHT, 'D');
                    
                    $style = [
                        'position' => '',
                        'align' => 'C',
                        'stretch' => false,
                        'fitwidth' => true,
                        'cellfitalign' => '',
                        'border' => false,
                        'padding' => 0,
                        'fgcolor' => [0,0,0],
                        'bgcolor' => false,
                        'text' => true,
                        'font' => 'helvetica',
                        'fontsize' => self::FONT_SIZE,
                        'stretchtext' => 0
                    ];

                    $pdf->write1DBarcode(
                        $code,
                        'C128',
                        $cellX + self::X_OFFSET,
                        $cellY + self::Y_OFFSET,
                        self::BARCODE_WIDTH,
                        self::BARCODE_HEIGHT,
                        0.4,
                        $style
                    );
                    
                    $barcodeCount++;
                }
            }
        }
        
        return $pdf->Output('barcodes.pdf', 'I');
    }
}
