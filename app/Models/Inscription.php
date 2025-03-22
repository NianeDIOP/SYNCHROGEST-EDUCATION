<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'classe_id',
        'annee_scolaire',
        'date_inscription',
        'montant_paye',
        'montant_restant',
        'numero_recu',
        'statut_paiement',
        'user_id',
    ];

    protected $casts = [
        'date_inscription' => 'date',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}