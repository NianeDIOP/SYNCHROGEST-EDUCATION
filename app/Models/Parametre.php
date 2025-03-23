<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_etablissement',
        'adresse',
        'telephone',
        'email',
        'logo_path',
        'annee_scolaire',
        'annee_active',
    ];
    
    protected $casts = [
        'annee_active' => 'boolean',
    ];
}