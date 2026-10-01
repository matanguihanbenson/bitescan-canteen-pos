<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScholarReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->transactions;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Date',
            'Time',
            'Student ID',
            'Scholar Name',
            'Type',
            'Amount',
            'Previous Balance',
            'New Balance',
            'Description',
            'Transaction Number',
            'Processed By'
        ];
    }

    /**
     * @param mixed $transaction
     * @return array
     */
    public function map($transaction): array
    {
        return [
            $transaction->transaction_date->format('Y-m-d'),
            $transaction->created_at->format('H:i:s'),
            $transaction->scholar ? $transaction->scholar->student_id : 'N/A',
            $transaction->scholar ? $transaction->scholar->first_name . ' ' . $transaction->scholar->last_name : 'Unknown',
            ucfirst($transaction->transaction_type),
            $transaction->amount,
            $transaction->previous_balance,
            $transaction->new_balance,
            $transaction->description ?? 'No description',
            $transaction->transaction_number ?? '-',
            $transaction->user ? $transaction->user->firstname . ' ' . $transaction->user->lastname : 'System'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Scholar Balance History';
    }
}