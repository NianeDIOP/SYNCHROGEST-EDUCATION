<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MatiereController extends Controller
{
    public function dashboard()
    {
        $parametres = Parametre::first();
        
        // À remplir avec les données de stocks réelles
        $stats = [
            'totalArticles' => 0,
            'articlesDisponibles' => 0,
            'articlesEnRupture' => 0,
        ];
        
        return Inertia::render('Matiere/Dashboard', [
            'parametres' => $parametres,
            'stats' => $stats,
        ]);
    }
    
    // Autres méthodes pour la gestion des matières
}