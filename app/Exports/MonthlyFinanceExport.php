<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class MonthlyFinanceExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $transactions;
    protected $period;

    public function __construct($transactions, $period)
    {
        $this->transactions = $transactions;
        $this->period = $period;
    }

    public function collection()
    {
        return $this->transactions->map(function ($transaction) {
            return [
                'Date' => $transaction->date->format('d/m/Y'),
                'Type' => ucfirst($transaction->type),
                'Catégorie' => $transaction->categorie->nom,
                'Description' => $transaction->description,
                'Référence' => $transaction->reference ?: 'N/A',
                'Montant' => number_format($transaction->montant, 0, ',', ' ') . ' FCFA',
                'Utilisateur' => $transaction->user->name
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Catégorie',
            'Description',
            'Référence',
            'Montant',
            'Utilisateur'
        ];
    }

    public function title(): string
    {
        $debut = Carbon::parse($this->period['debut'])->format('d/m/Y');
        $fin = Carbon::parse($this->period['fin'])->format('d/m/Y');
        return 'Rapport du ' . $debut . ' au ' . $fin;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2']
                ]
            ]
        ];
    }
}