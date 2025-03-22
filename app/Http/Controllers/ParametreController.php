<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use App\Models\Niveau;
use App\Models\Classe;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParametreController extends Controller
{
    public function inscription()
    {
        $parametres = Parametre::first();
        $niveaux = Niveau::with('classes')->get();

        return Inertia::render('Inscription/Parametres', [
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
            'annee_scolaire' => 'required|string|max:9',
            'niveaux' => 'required|array',
            'niveaux.*.nom' => 'required|string|max:50',
            'niveaux.*.frais_inscription' => 'required|numeric|min:0',
            'niveaux.*.frais_scolarite' => 'required|numeric|min:0',
            'niveaux.*.est_niveau_examen' => 'boolean',
            'niveaux.*.frais_examen' => 'nullable|numeric|min:0',
            'niveaux.*.classes' => 'required|array',
            'niveaux.*.classes.*.nom' => 'required|string|max:50',
            'niveaux.*.classes.*.capacite' => 'required|integer|min:1',
        ]);

        // Enregistrer ou mettre à jour les paramètres généraux
        Parametre::updateOrCreate(
            ['id' => 1],
            [
                'nom_etablissement' => $request->nom_etablissement,
                'adresse' => $request->adresse,
                'telephone' => $request->telephone,
                'email' => $request->email,
                'annee_scolaire' => $request->annee_scolaire,
            ]
        );

        // Traiter les niveaux et classes
        foreach ($request->niveaux as $niveauData) {
            $niveau = Niveau::updateOrCreate(
                ['id' => $niveauData['id'] ?? null],
                [
                    'nom' => $niveauData['nom'],
                    'frais_inscription' => $niveauData['frais_inscription'],
                    'frais_scolarite' => $niveauData['frais_scolarite'],
                    'est_niveau_examen' => $niveauData['est_niveau_examen'] ?? false,
                    'frais_examen' => $niveauData['frais_examen'] ?? 0,
                ]
            );

            // Gérer les classes de ce niveau
            foreach ($niveauData['classes'] as $classeData) {
                Classe::updateOrCreate(
                    ['id' => $classeData['id'] ?? null],
                    [
                        'niveau_id' => $niveau->id,
                        'nom' => $classeData['nom'],
                        'capacite' => $classeData['capacite'],
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Paramètres enregistrés avec succès');
    }

    public function finance()
    {
        $parametres = Parametre::first();
        
        return Inertia::render('Finance/Parametres', [
            'parametres' => $parametres,
        ]);
    }

    public function matiere()
    {
        $parametres = Parametre::first();
        
        return Inertia::render('Matiere/Parametres', [
            'parametres' => $parametres,
        ]);
    }
}