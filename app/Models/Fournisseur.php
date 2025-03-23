<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'email',
        'personne_contact',
        'telephone_contact',
        'est_actif',
    ];

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }
}