<?php

namespace App\Exports;

use App\Models\Transaction;
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
                'date' => $transaction->date->format('d/m/Y'),
                'type' => ucfirst($transaction->type),
                'categorie' => $transaction->categorie->nom,
                'description' => $transaction->description,
                'reference' => $transaction->reference ?: 'N/A',
                'montant' => number_format($transaction->montant, 0, ',', ' '),
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
            'Montant (FCFA)'
        ];
    }

    public function title(): string
    {
        $debut = Carbon::parse($this->period['debut'])->format('d/m/Y');
        $fin = Carbon::parse($this->period['fin'])->format('d/m/Y');
        return "Rapport du {$debut} au {$fin}";
    }

    public function styles(Worksheet $sheet)
    {
        // Calculs des totaux
        $totalRecettes = $this->transactions->where('type', 'recette')->sum('montant');
        $totalDepenses = $this->transactions->where('type', 'depense')->sum('montant');
        $solde = $totalRecettes - $totalDepenses;

        // Ajout du résumé au début du document
        $sheet->insertNewRowBefore(1, 6);
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'RAPPORT FINANCIER - ' . $this->title());
        $sheet->setCellValue('A2', 'Total Recettes:');
        $sheet->setCellValue('B2', number_format($totalRecettes, 0, ',', ' ') . ' FCFA');
        $sheet->setCellValue('A3', 'Total Dépenses:');
        $sheet->setCellValue('B3', number_format($totalDepenses, 0, ',', ' ') . ' FCFA');
        $sheet->setCellValue('A4', 'Solde:');
        $sheet->setCellValue('B4', number_format($solde, 0, ',', ' ') . ' FCFA');
        $sheet->setCellValue('A6', 'Liste des transactions:');

        // Styles des entêtes
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            6 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']]],
        ];
    }
}