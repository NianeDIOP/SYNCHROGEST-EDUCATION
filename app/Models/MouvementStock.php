<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'article_id',
        'type_mouvement', // entrée, sortie
        'quantite',
        'date_mouvement',
        'motif',
        'reference_document',
        'fournisseur_id',
        'destinataire',
        'user_id',
    ];

    protected $casts = [
        'date_mouvement' => 'date',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}