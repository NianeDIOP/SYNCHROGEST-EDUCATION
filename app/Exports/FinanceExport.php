<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FinanceExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $transactions;
    protected $type;
    protected $titre;

    public function __construct($transactions, $type, $titre)
    {
        $this->transactions = $transactions;
        $this->type = $type;
        $this->titre = $titre;
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
                'Année scolaire' => $transaction->annee_scolaire,
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
            'Année scolaire',
            'Utilisateur'
        ];
    }

    public function title(): string
    {
        return $this->titre;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2']
                ]
            ]
        ];
    }
}