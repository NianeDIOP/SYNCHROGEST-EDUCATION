<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'categorie_id',
        'montant',
        'date',
        'description',
        'reference',
        'annee_scolaire',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'decimal:2',
    ];

    /**
     * Récupère la catégorie financière associée à cette transaction
     */
    public function categorie()
    {
        return $this->belongsTo(CategorieFinanciere::class, 'categorie_id');
    }

    /**
     * Récupère l'utilisateur qui a créé cette transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour filtrer les recettes
     */
    public function scopeRecettes($query)
    {
        return $query->where('type', 'recette');
    }

    /**
     * Scope pour filtrer les dépenses
     */
    public function scopeDepenses($query)
    {
        return $query->where('type', 'depense');
    }
}