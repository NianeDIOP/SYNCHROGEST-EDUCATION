<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $articles;
    protected $titre;
    protected $parametres;

    public function __construct($articles, $titre, $parametres)
    {
        $this->articles = $articles;
        $this->titre = $titre;
        $this->parametres = $parametres;
    }

    public function collection()
    {
        return $this->articles->map(function ($article) {
            return [
                'code' => $article->code,
                'designation' => $article->designation,
                'categorie' => $article->categorie->nom,
                'quantite' => $article->quantite_stock,
                'unite' => $article->unite_mesure,
                'seuil' => $article->seuil_alerte,
                'prix' => $article->prix_unitaire,
                'valeur' => $article->quantite_stock * $article->prix_unitaire,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Code',
            'Désignation',
            'Catégorie',
            'Quantité',
            'Unité',
            'Seuil d\'alerte',
            'Prix unitaire (FCFA)',
            'Valeur stock (FCFA)',
        ];
    }

    public function title(): string
    {
        return 'État du stock';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}