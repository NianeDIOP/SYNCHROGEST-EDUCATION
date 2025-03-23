<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use App\Models\Niveau;
use App\Models\Classe;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function inscription()
    {
        $parametres = Parametre::first();
        $niveaux = Niveau::with('classes')->get();
        
        // Utiliser la vue Blade au lieu d'Inertia
        return view('inscriptions.parametres', [
            'parametres' => $parametres,
            'niveaux' => $niveaux,
        ]);
    }

    public function saveInscription(Request $request)
    {
        // Valider les paramètres généraux
        $request->validate([
            'nom_etablissement' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'annee_scolaire' => 'required|string|regex:/^\d{4}-\d{4}$/',
        ], [
            'annee_scolaire.regex' => 'Le format de l\'année scolaire doit être AAAA-AAAA (exemple: 2024-2025)'
        ]);
        
        // Valider le format de l'année scolaire
        $years = explode('-', $request->annee_scolaire);
        if (count($years) == 2) {
            if (intval($years[1]) !== intval($years[0]) + 1) {
                return redirect()->back()->withErrors([
                    'annee_scolaire' => 'L\'année de fin doit être l\'année de début + 1'
                ])->withInput();
            }
        }
        
        // Enregistrer ou mettre à jour les paramètres généraux
        Parametre::updateOrCreate(
            ['id' => 1],
            [
                'nom_etablissement' => $request->nom_etablissement,
                'adresse' => $request->adresse,
                'telephone' => $request->telephone,
                'email' => $request->email,
                'annee_scolaire' => $request->annee_scolaire,
                'annee_active' => $request->has('annee_active'),
            ]
        );
        
        return redirect()->back()->with('success', 'Paramètres enregistrés avec succès');
    }

    public function finance()
    {
        $parametres = Parametre::first();
        
        // Utiliser la vue Blade au lieu d'Inertia
        return view('finances.parametres', [
            'parametres' => $parametres,
        ]);
    }

    public function matiere()
    {
        $parametres = Parametre::first();
        
        // Utiliser la vue Blade au lieu d'Inertia
        return view('matieres.parametres', [
            'parametres' => $parametres,
        ]);
    }
}