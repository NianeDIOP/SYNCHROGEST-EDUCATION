<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InscriptionExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $inscriptions;
    protected $type;
    protected $titre;

    public function __construct($inscriptions, $type, $titre)
    {
        $this->inscriptions = $inscriptions;
        $this->type = $type;
        $this->titre = $titre;
    }

    public function collection()
    {
        return $this->inscriptions->map(function ($inscription) {
            return [
                'INE' => $inscription->eleve->ine,
                'Nom' => $inscription->eleve->nom,
                'Prénom' => $inscription->eleve->prenom,
                'Sexe' => $inscription->eleve->sexe,
                'Date de naissance' => $inscription->eleve->date_naissance->format('d/m/Y'),
                'Niveau' => $inscription->classe->niveau->nom,
                'Classe' => $inscription->classe->nom,
                'Statut' => $inscription->eleve->statut,
                'Date inscription' => $inscription->date_inscription->format('d/m/Y'),
                'Montant payé' => number_format($inscription->montant_paye, 0, ',', ' ') . ' FCFA',
                'Reste à payer' => number_format($inscription->montant_restant, 0, ',', ' ') . ' FCFA',
                'Statut paiement' => $inscription->statut_paiement
            ];
        });
    }

    public function headings(): array
    {
        return [
            'INE',
            'Nom',
            'Prénom', 
            'Sexe',
            'Date de naissance',
            'Niveau',
            'Classe',
            'Statut élève',
            'Date inscription',
            'Montant payé',
            'Reste à payer',
            'Statut paiement'
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