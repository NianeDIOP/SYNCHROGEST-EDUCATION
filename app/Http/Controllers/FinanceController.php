<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinanceController extends Controller
{
    public function dashboard()
    {
        $parametres = Parametre::first();
        
        // À remplir avec les données financières réelles
        $stats = [
            'totalRevenu' => 0,
            'totalDepense' => 0,
            'balance' => 0,
        ];
        
        return Inertia::render('Finance/Dashboard', [
            'parametres' => $parametres,
            'stats' => $stats,
        ]);
    }
    
    // Autres méthodes pour la gestion financière
}