<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;

    protected $fillable = [
        'ine',
        'prenom',
        'nom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'existence_extrait',
        'classe_id',
        'motif_entre',
        'statut',
        'contact_parent',
        'adresse',
        'photo_path',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'existence_extrait' => 'boolean',
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }
}