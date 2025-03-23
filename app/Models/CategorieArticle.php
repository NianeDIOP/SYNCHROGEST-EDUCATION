<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieArticle extends Model
{
    use HasFactory;

    protected $table = 'categories_articles';

    protected $fillable = [
        'nom',
        'description',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class, 'categorie_id');
    }
}