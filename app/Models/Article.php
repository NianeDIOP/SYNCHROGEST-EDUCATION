<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'designation',
        'description',
        'unite_mesure',
        'quantite_stock',
        'seuil_alerte',
        'prix_unitaire',
        'categorie_id',
        'emplacement',
        'image_path',
        'est_actif',
    ];

    public function categorie()
    {
        return $this->belongsTo(CategorieArticle::class, 'categorie_id');
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function enAlerte()
    {
        return $this->quantite_stock <= $this->seuil_alerte;
    }

    public function valeurStock()
    {
        return $this->quantite_stock * $this->prix_unitaire;
    }
}