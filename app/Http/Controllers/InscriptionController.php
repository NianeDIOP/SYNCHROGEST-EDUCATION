<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Classe;
use App\Models\Niveau;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
        
        return Inertia::render('Inscription/Dashboard', [
            'parametres' => $parametres,
            'stats' => [
                'totalEleves' => $totalEleves,
                'totalInscrits' => $totalInscrits,
                'nouveauxInscrits' => $nouveauxInscrits,
                'inscriptionsParNiveau' => $inscriptionsParNiveau,
            ],
        ]);
    }

    public function showImport()
    {
        $niveaux = Niveau::with('classes')->get();
        
        return Inertia::render('Inscription/Import', [
            'niveaux' => $niveaux,
        ]);
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'classe_id' => 'required|exists:classes,id',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->storeAs('imports', 'eleves_' . time() . '.' . $request->file('file')->getClientOriginalExtension());
            
            // Le traitement du fichier Excel se fera côté frontend via React
            // Ici, nous simulons juste une réponse pour indiquer que le fichier a été téléchargé
            
            return response()->json([
                'success' => true,
                'file_path' => $path,
                'classe_id' => $request->classe_id,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Erreur lors du téléchargement du fichier'], 400);
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
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ine', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('classe_id') && $request->classe_id) {
            $query->where('classe_id', $request->classe_id);
        }
        
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }
        
        $eleves = $query->paginate(15);
        $classes = Classe::with('niveau')->get();
        
        return Inertia::render('Inscription/Eleves', [
            'eleves' => $eleves,
            'classes' => $classes,
            'filters' => $request->only(['search', 'classe_id', 'statut']),
        ]);
    }

    public function nouvelleInscription(Request $request)
    {
        $ine = $request->ine;
        $eleve = null;
        
        if ($ine) {
            $eleve = Eleve::with('classe.niveau')->where('ine', $ine)->first();
        }
        
        $classes = Classe::with('niveau')->get();
        $parametres = Parametre::first();
        
        return Inertia::render('Inscription/Nouvelle', [
            'eleve' => $eleve,
            'classes' => $classes,
            'parametres' => $parametres,
            'searchIne' => $ine,
        ]);
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
            return redirect()->back()->withErrors(['error' => 'Veuillez configurer les paramètres de l\'établissement']);
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
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()]);
        }
    }

    public function recu($id)
    {
        $inscription = Inscription::with(['eleve', 'classe.niveau', 'user'])->findOrFail($id);
        $parametres = Parametre::first();
        
        return Inertia::render('Inscription/Recu', [
            'inscription' => $inscription,
            'parametres' => $parametres,
        ]);
    }

    public function rapports()
    {
        $niveaux = Niveau::with('classes')->get();
        $parametres = Parametre::first();
        
        return Inertia::render('Inscription/Rapports', [
            'niveaux' => $niveaux,
            'parametres' => $parametres,
        ]);
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
        
        // Générer et télécharger le rapport
        if ($request->format === 'pdf') {
            // Code pour générer un PDF
            // À implémenter avec une bibliothèque PDF
            return response()->json(['success' => true, 'message' => 'Fonctionnalité PDF à implémenter']);
        } else {
            // Code pour générer un fichier Excel
            // À implémenter avec une bibliothèque Excel
            return response()->json(['success' => true, 'message' => 'Fonctionnalité Excel à implémenter']);
        }
    }
}