<?php

namespace App\Exports;

use App\Models\Transaction;
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
                'date' => $transaction->date->format('d/m/Y'),
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
            'Description',
            'Référence',
            'Montant (FCFA)'
        ];
    }

    public function title(): string
    {
        return "Catégorie: {$this->categorie->nom}";
    }

    public function styles(Worksheet $sheet)
    {
        // Calcul du total
        $total = $this->transactions->sum('montant');

        // Ajout du résumé au début du document
        $sheet->insertNewRowBefore(1, 4);
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'RAPPORT PAR CATÉGORIE - ' . strtoupper($this->categorie->nom));
        $sheet->setCellValue('A2', 'Type de catégorie:');
        $sheet->setCellValue('B2', ucfirst($this->categorie->type));
        $sheet->setCellValue('A3', 'Total:');
        $sheet->setCellValue('B3', number_format($total, 0, ',', ' ') . ' FCFA');
        $sheet->setCellValue('A4', 'Liste des transactions:');

        // Styles des entêtes
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']]],
        ];
    }
}