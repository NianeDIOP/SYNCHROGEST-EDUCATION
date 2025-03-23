<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CategoryFinanceExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $transactions;
    protected $categorie;

    public function __construct($transactions, $categorie)
    {
        $this->transactions = $transactions;
        $this->categorie = $categorie;
    }

    public function collection()
    {
        return $this->transactions->map(function ($transaction) {
            return [
                'Date' => $transaction->date->format('d/m/Y'),
                'Type' => ucfirst($transaction->type),
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
            'Description',
            'Référence',
            'Montant',
            'Utilisateur'
        ];
    }

    public function title(): string
    {
        return 'Catégorie: ' . $this->categorie->nom;
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