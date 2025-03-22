<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Classe;
use App\Models\Niveau;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InscriptionController extends Controller
{
    public function dashboard()
    {
        $parametres = Parametre::first();
        $anneeScolaire = $parametres ? $parametres->annee_scolaire : null;
        
        // Statistiques
        $totalEleves = Eleve::count();
        $totalInscrits = Inscription::where('annee_scolaire', $anneeScolaire)->count();
        $nouveauxInscrits = Inscription::where('annee_scolaire', $anneeScolaire)
            ->whereHas('eleve', function($query) {
                $query->where('statut', 'Nouveau');
            })->count();
        
        // Inscriptions par niveau
        $inscriptionsParNiveau = DB::table('inscriptions')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->where('inscriptions.annee_scolaire', $anneeScolaire)
            ->select('niveaux.nom', DB::raw('count(*) as total'))
            ->groupBy('niveaux.nom')
            ->get();
        
        return view('inscriptions.dashboard', compact('parametres', 'totalEleves', 'totalInscrits', 'nouveauxInscrits', 'inscriptionsParNiveau'));
    }

    public function parametres()
    {
        $parametres = Parametre::first();
        
        return view('inscriptions.parametres', compact('parametres'));
    }

    public function niveaux()
    {
        $niveaux = Niveau::with('classes')->get();
        
        return view('inscriptions.niveaux', compact('niveaux'));
    }

    public function saveParametres(Request $request)
    {
        $request->validate([
            'nom_etablissement' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'annee_scolaire' => 'required|string|max:9',
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

        return redirect()->route('inscriptions.parametres')->with('success', 'Paramètres enregistrés avec succès');
    }

    public function saveNiveaux(Request $request)
    {
        try {
            // Validation plus complète
            $request->validate([
                'niveaux' => 'required|array',
                'niveaux.*.nom' => 'required|string|max:50',
                'niveaux.*.frais_inscription' => 'required|numeric|min:0',
                'niveaux.*.frais_scolarite' => 'required|numeric|min:0',
                'niveaux.*.classes' => 'nullable|array',
                'niveaux.*.classes.*.nom' => 'required|string|max:50',
                'niveaux.*.classes.*.capacite' => 'required|integer|min:1',
            ]);
    
            DB::beginTransaction();
            
            // Collecter les IDs des niveaux pour vérifier la suppression
            $niveauIds = [];
            
            // Traiter les niveaux et classes
            foreach ($request->niveaux as $niveauData) {
                if (!isset($niveauData['nom']) || empty($niveauData['nom'])) {
                    continue; // Ignorer les niveaux sans nom
                }
                
                // Debug pour voir les données reçues
                \Log::info('Données de niveau reçues:', $niveauData);
                
                $niveau = Niveau::updateOrCreate(
                    ['id' => $niveauData['id'] ? $niveauData['id'] : null],
                    [
                        'nom' => $niveauData['nom'],
                        'frais_inscription' => $niveauData['frais_inscription'],
                        'frais_scolarite' => $niveauData['frais_scolarite'],
                        'est_niveau_examen' => isset($niveauData['est_niveau_examen']),
                        'frais_examen' => isset($niveauData['est_niveau_examen']) ? ($niveauData['frais_examen'] ?? 0) : 0,
                    ]
                );
                
                \Log::info('Niveau créé/mis à jour:', ['id' => $niveau->id, 'nom' => $niveau->nom]);
                
                $niveauIds[] = $niveau->id;
    
                // Gérer les classes de ce niveau
                if (isset($niveauData['classes']) && is_array($niveauData['classes'])) {
                    $classeIds = [];
                    
                    foreach ($niveauData['classes'] as $classeData) {
                        if (!isset($classeData['nom']) || empty($classeData['nom'])) {
                            continue; // Ignorer les classes sans nom
                        }
                        
                        \Log::info('Données de classe reçues:', $classeData);
                        
                        $classe = Classe::updateOrCreate(
                            ['id' => $classeData['id'] ? $classeData['id'] : null],
                            [
                                'niveau_id' => $niveau->id,
                                'nom' => $classeData['nom'],
                                'capacite' => $classeData['capacite'],
                            ]
                        );
                        
                        \Log::info('Classe créée/mise à jour:', ['id' => $classe->id, 'nom' => $classe->nom]);
                        
                        $classeIds[] = $classe->id;
                    }
                    
                    // Supprimer les classes qui n'existent plus pour ce niveau
                    if (!empty($classeIds)) {
                        $deletedClasses = Classe::where('niveau_id', $niveau->id)
                            ->whereNotIn('id', $classeIds)
                            ->delete();
                        
                        \Log::info("Classes supprimées pour le niveau {$niveau->id}:", ['count' => $deletedClasses]);
                    }
                }
            }
            
            // Supprimer les niveaux qui ont été supprimés dans l'interface
            if (!empty($niveauIds)) {
                $deletedNiveaux = Niveau::whereNotIn('id', $niveauIds)->delete();
                \Log::info('Niveaux supprimés:', ['count' => $deletedNiveaux]);
            }
    
            DB::commit();
            
            return redirect()->route('inscriptions.niveaux')
                ->with('success', 'Niveaux et classes enregistrés avec succès');
        }
        catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de l\'enregistrement des niveaux:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage()])
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.');
        }
    }

    public function showImport()
    {
        $niveaux = Niveau::with('classes')->get();
        
        return view('inscriptions.import', compact('niveaux'));
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'classe_id' => 'required|exists:classes,id',
        ]);

        $classe = Classe::with('niveau')->findOrFail($request->classe_id);

        if ($request->hasFile('file')) {
            // Stocker le fichier temporairement
            $path = $request->file('file')->store('temp');
            
            // Analyser le fichier Excel (cela sera fait côté client avec JavaScript)
            // Ici, nous redirigeons simplement vers la même page avec un message de succès
            
            return redirect()->route('inscriptions.import')->with([
                'success' => 'Fichier importé avec succès. Veuillez vérifier les données avant de finaliser l\'importation.',
                'file_path' => $path,
                'classe_id' => $request->classe_id,
                'classe_nom' => $classe->nom,
                'niveau_nom' => $classe->niveau->nom,
            ]);
        }

        return redirect()->route('inscriptions.import')->with('error', 'Erreur lors du téléchargement du fichier');
    }

    public function saveImportedData(Request $request)
    {
        $request->validate([
            'eleves' => 'required|array',
            'eleves.*.ine' => 'required|string|unique:eleves,ine',
            'eleves.*.prenom' => 'required|string',
            'eleves.*.nom' => 'required|string',
            'eleves.*.sexe' => 'required|in:M,F',
            'eleves.*.date_naissance' => 'required|date',
            'eleves.*.lieu_naissance' => 'required|string',
            'eleves.*.existence_extrait' => 'boolean',
            'eleves.*.classe_id' => 'required|exists:classes,id',
            'eleves.*.motif_entre' => 'nullable|string',
            'eleves.*.statut' => 'required|in:Nouveau,Ancien,Redoublant',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->eleves as $eleveData) {
                Eleve::create($eleveData);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Élèves importés avec succès']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'importation: ' . $e->getMessage()], 500);
        }
    }

    public function eleves(Request $request)
    {
        $query = Eleve::query()->with('classe.niveau');
        
        // Filtrage
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ine', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('classe_id') && !empty($request->classe_id)) {
            $query->where('classe_id', $request->classe_id);
        }
        
        if ($request->has('statut') && !empty($request->statut)) {
            $query->where('statut', $request->statut);
        }
        
        $eleves = $query->orderBy('nom')->orderBy('prenom')->paginate(15);
        $classes = Classe::with('niveau')->get();
        
        return view('inscriptions.eleves', compact('eleves', 'classes'));
    }

    public function showEleve($id)
    {
        $eleve = Eleve::with('classe.niveau')->findOrFail($id);
        $inscriptions = Inscription::with('classe.niveau')
            ->where('eleve_id', $id)
            ->orderBy('annee_scolaire', 'desc')
            ->get();
        
        return response()->json([
            'eleve' => $eleve,
            'inscriptions' => $inscriptions
        ]);
    }
    
    public function deleteEleve($id)
    {
        try {
            $eleve = Eleve::findOrFail($id);
            
            // Vérifier si l'élève a des inscriptions
            $hasInscriptions = Inscription::where('eleve_id', $id)->exists();
            
            if ($hasInscriptions) {
                return redirect()->route('inscriptions.eleves')
                    ->with('error', 'Impossible de supprimer cet élève car il possède des inscriptions.');
            }
            
            $eleve->delete();
            
            return redirect()->route('inscriptions.eleves')
                ->with('success', 'Élève supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('inscriptions.eleves')
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
    public function nouvelleInscription(Request $request)
    {
        $eleve = null;
        $searchIne = $request->ine;
        
        if ($searchIne) {
            $eleve = Eleve::with('classe.niveau')->where('ine', $searchIne)->first();
        }
        
        $classes = Classe::with('niveau')->get();
        $parametres = Parametre::first();
        
        return view('inscriptions.nouvelle', compact('eleve', 'classes', 'parametres', 'searchIne'));
    }

    public function enregistrerInscription(Request $request)
    {
        $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'montant_paye' => 'required|numeric|min:0',
            'date_inscription' => 'required|date',
        ]);
        
        $parametres = Parametre::first();
        
        if (!$parametres || !$parametres->annee_scolaire) {
            return redirect()->back()->with('error', 'Veuillez configurer les paramètres de l\'établissement');
        }

        $classe = Classe::with('niveau')->findOrFail($request->classe_id);
        $fraisTotal = $classe->niveau->frais_inscription;
        
        // Ajouter frais d'examen si applicable
        if ($classe->niveau->est_niveau_examen) {
            $fraisTotal += $classe->niveau->frais_examen;
        }
        
        $montantRestant = $fraisTotal - $request->montant_paye;
        $statutPaiement = $montantRestant <= 0 ? 'Complet' : 'Partiel';
        
        // Générer numéro de reçu unique
        $numeroRecu = 'INS-' . date('Y') . '-' . Str::padLeft(Inscription::count() + 1, 4, '0');
        
        DB::beginTransaction();
        try {
            $inscription = Inscription::create([
                'eleve_id' => $request->eleve_id,
                'classe_id' => $request->classe_id,
                'annee_scolaire' => $parametres->annee_scolaire,
                'date_inscription' => $request->date_inscription,
                'montant_paye' => $request->montant_paye,
                'montant_restant' => $montantRestant,
                'numero_recu' => $numeroRecu,
                'statut_paiement' => $statutPaiement,
                'user_id' => auth()->id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('inscriptions.recu', ['id' => $inscription->id])
                ->with('success', 'Inscription enregistrée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    public function recu($id)
    {
        $inscription = Inscription::with(['eleve', 'classe.niveau', 'user'])->findOrFail($id);
        $parametres = Parametre::first();
        
        return view('inscriptions.recu', compact('inscription', 'parametres'));
    }

    public function rapports()
    {
        $niveaux = Niveau::with('classes')->get();
        $parametres = Parametre::first();
        
        return view('inscriptions.rapports', compact('niveaux', 'parametres'));
    }
    
    public function genererRapport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:general,niveau,classe',
            'niveau_id' => 'required_if:type,niveau|nullable|exists:niveaux,id',
            'classe_id' => 'required_if:type,classe|nullable|exists:classes,id',
            'statut' => 'nullable|in:tous,Nouveau,Ancien,Redoublant',
            'format' => 'required|in:pdf,excel',
        ]);
        
        $parametres = Parametre::first();
        $anneeScolaire = $parametres ? $parametres->annee_scolaire : null;
        
        // Construire la requête selon les filtres
        $query = Inscription::query()
            ->where('annee_scolaire', $anneeScolaire)
            ->with(['eleve', 'classe.niveau']);
        
        if ($request->type === 'niveau' && $request->niveau_id) {
            $query->whereHas('classe', function($q) use ($request) {
                $q->where('niveau_id', $request->niveau_id);
            });
        }
        
        if ($request->type === 'classe' && $request->classe_id) {
            $query->where('classe_id', $request->classe_id);
        }
        
        if ($request->statut && $request->statut !== 'tous') {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('statut', $request->statut);
            });
        }
        
        $inscriptions = $query->get();
        
        // Générer le rapport (cette partie serait à implémenter avec une bibliothèque PDF ou Excel)
        if ($request->format === 'pdf') {
            // Pour l'instant, rediriger avec un message
            return redirect()->route('inscriptions.rapports')
                ->with('success', 'La fonctionnalité de génération de PDF sera implémentée ultérieurement.');
        } else {
            // Pour l'instant, rediriger avec un message
            return redirect()->route('inscriptions.rapports')
                ->with('success', 'La fonctionnalité de génération d\'Excel sera implémentée ultérieurement.');
        }
    }
}