<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'frais_inscription',
        'frais_scolarite',
        'est_niveau_examen',
        'frais_examen',
    ];

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }
}