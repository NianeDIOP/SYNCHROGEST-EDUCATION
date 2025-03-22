<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'niveau_id',
        'nom',
        'capacite',
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function eleves()
    {
        return $this->hasMany(Eleve::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }
}