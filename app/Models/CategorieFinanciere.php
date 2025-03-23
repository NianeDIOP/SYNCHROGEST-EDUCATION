<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieFinanciere extends Model
{
    use HasFactory;
    
    protected $table = 'categories_financieres';

    protected $fillable = [
        'nom',
        'type',
        'description',
    ];

    /**
     * Récupère toutes les transactions associées à cette catégorie
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'categorie_id');
    }

    /**
     * Scope pour filtrer les catégories de recettes
     */
    public function scopeRecettes($query)
    {
        return $query->where('type', 'recette');
    }

    /**
     * Scope pour filtrer les catégories de dépenses
     */
    public function scopeDepenses($query)
    {
        return $query->where('type', 'depense');
    }
}