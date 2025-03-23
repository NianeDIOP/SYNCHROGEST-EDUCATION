<?php

namespace App\Http\Controllers;

use App\Models\Parametre;
use App\Models\Inscription;
use App\Models\Transaction;
use App\Models\CategorieFinanciere;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function dashboard()
    {
        $parametres = Parametre::first();
        $anneeScolaire = $parametres ? $parametres->annee_scolaire : null;
        
        // Statistiques financières
        $totalRecettes = 0;
        $totalDepenses = 0;
        $solde = 0;
        
        // Si nous avons développé la table des transactions
        if (class_exists('App\Models\Transaction')) {
            $totalRecettes = Transaction::where('type', 'recette')
                ->where('annee_scolaire', $anneeScolaire)
                ->sum('montant');
                
            $totalDepenses = Transaction::where('type', 'depense')
                ->where('annee_scolaire', $anneeScolaire)
                ->sum('montant');
                
            $solde = $totalRecettes - $totalDepenses;
        }
        
        // Revenus d'inscription
        $totalInscriptions = Inscription::where('annee_scolaire', $anneeScolaire)
            ->sum('montant_paye');
        
        // Récupérer les dernières transactions
        $transactions = [];
        if (class_exists('App\Models\Transaction')) {
            $transactions = Transaction::with('categorie', 'user')
                ->where('annee_scolaire', $anneeScolaire)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();
        }
        
        // Données pour le graphique d'évolution financière
        // Localiser ce bloc de code dans app/Http/Controllers/FinanceController.php
// et remplacer la section entière par ce qui suit:

// Données pour le graphique d'évolution financière
            $financesParMois = [];
            $mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

            if (class_exists('App\Models\Transaction')) {
                // Recettes par mois - Utilisation de strftime pour SQLite
                $recettesParMois = Transaction::where('type', 'recette')
                    ->where('annee_scolaire', $anneeScolaire)
                    ->selectRaw("strftime('%m', date) as mois, SUM(montant) as total")
                    ->groupBy('mois')
                    ->get()
                    ->pluck('total', 'mois')
                    ->toArray();
                
                // Dépenses par mois - Utilisation de strftime pour SQLite
                $depensesParMois = Transaction::where('type', 'depense')
                    ->where('annee_scolaire', $anneeScolaire)
                    ->selectRaw("strftime('%m', date) as mois, SUM(montant) as total")
                    ->groupBy('mois')
                    ->get()
                    ->pluck('total', 'mois')
                    ->toArray();
                
                // Compléter les mois manquants
                for ($i = 1; $i <= 12; $i++) {
                    // Formater i en '01', '02', etc. pour correspondre au format de strftime('%m')
                    $monthKey = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $financesParMois['recettes'][] = $recettesParMois[$monthKey] ?? 0;
                    $financesParMois['depenses'][] = $depensesParMois[$monthKey] ?? 0;
                }
            } else {
                // Données factices pour le développement
                $financesParMois = [
                    'recettes' => [0, 10000, 20000, 15000, 25000, 30000, 25000, 20000, 30000, 40000, 35000, 50000],
                    'depenses' => [0, 5000, 10000, 12000, 15000, 20000, 15000, 10000, 18000, 25000, 20000, 30000]
                ];
            }
        
        // Récupérer les données de répartition des dépenses
        $repartitionDepenses = [];
        if (class_exists('App\Models\Transaction')) {
            $repartitionDepenses = Transaction::where('transactions.type', 'depense')
                ->where('transactions.annee_scolaire', $anneeScolaire)
                ->join('categories_financieres', 'transactions.categorie_id', '=', 'categories_financieres.id')
                ->selectRaw('categories_financieres.nom, SUM(transactions.montant) as total')
                ->groupBy('categories_financieres.id', 'categories_financieres.nom')
                ->orderBy('total', 'desc')
                ->get();
        }
        
        // Récupérer les montants à recouvrer par niveau
        $recouvrements = DB::table('inscriptions')
            ->join('eleves', 'inscriptions.eleve_id', '=', 'eleves.id')
            ->join('classes', 'eleves.classe_id', '=', 'classes.id')
            ->join('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->where('inscriptions.annee_scolaire', $anneeScolaire)
            ->select(
                'niveaux.id',
                'niveaux.nom as niveau_nom',
                DB::raw('COUNT(DISTINCT inscriptions.eleve_id) as nb_eleves'),
                DB::raw('SUM(inscriptions.montant_paye) as montant_paye'),
                DB::raw('SUM(inscriptions.montant_restant) as montant_restant')
            )
            ->groupBy('niveaux.id', 'niveaux.nom')
            ->get();
        
        // Stats pour affichage
        $stats = [
            'totalRecettes' => $totalRecettes,
            'totalDepenses' => $totalDepenses,
            'solde' => $solde,
            'totalInscriptions' => $totalInscriptions
        ];
        
        return view('finances.dashboard', [
            'parametres' => $parametres,
            'stats' => $stats,
            'transactions' => $transactions,
            'financesParMois' => $financesParMois,
            'mois' => $mois,
            'repartitionDepenses' => $repartitionDepenses,
            'recouvrements' => $recouvrements
        ]);
    }
    
    public function transactions(Request $request)
    {
        $parametres = Parametre::first();
        $anneeScolaire = $parametres ? $parametres->annee_scolaire : null;
        
        // Base de la requête
        $query = Transaction::query()->with(['categorie', 'user']);
        
        // Filtrage par année scolaire
        $query->where('annee_scolaire', $anneeScolaire);
        
        // Filtrage par type (recette/dépense)
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }
        
        // Filtrage par catégorie
        if ($request->has('categorie_id') && !empty($request->categorie_id)) {
            $query->where('categorie_id', $request->categorie_id);
        }
        
        // Filtrage par date
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->where('date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->where('date', '<=', $request->date_to);
        }
        
        // Récupération des transactions filtrées
        $transactions = $query->orderBy('date', 'desc')->paginate(15);
        
        // Récupération des catégories pour le filtre
        $categories = CategorieFinanciere::all();
        
        return view('finances.transactions', [
            'parametres' => $parametres,
            'transactions' => $transactions,
            'categories' => $categories
        ]);
    }
    
    public function ajouterTransaction(Request $request)
    {
        $request->validate([
            'type' => 'required|in:recette,depense',
            'categorie_id' => 'required|exists:categories_financieres,id',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'required|string',
            'reference' => 'nullable|string',
        ]);
        
        $parametres = Parametre::first();
        
        if (!$parametres) {
            return redirect()->back()->with('error', 'Veuillez configurer les paramètres de l\'établissement');
        }
        
        if (!$parametres->annee_scolaire) {
            return redirect()->back()->with('error', 'Veuillez configurer l\'année scolaire dans les paramètres');
        }
        
        // Vérifier que la catégorie correspond au type de transaction
        $categorie = CategorieFinanciere::findOrFail($request->categorie_id);
        if ($categorie->type !== $request->type) {
            return redirect()->back()->with('error', 'La catégorie sélectionnée ne correspond pas au type de transaction');
        }
        
        Transaction::create([
            'type' => $request->type,
            'categorie_id' => $request->categorie_id,
            'montant' => $request->montant,
            'date' => $request->date,
            'description' => $request->description,
            'reference' => $request->reference,
            'annee_scolaire' => $parametres->annee_scolaire,
            'user_id' => auth()->id(),
        ]);
        
        return redirect()->back()->with('success', 'Transaction enregistrée avec succès');
    }
    
    public function supprimerTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        
        return redirect()->back()->with('success', 'Transaction supprimée avec succès');
    }
    
    public function parametres()
    {
        $parametres = Parametre::first();
        $categories = CategorieFinanciere::all();
        
        return view('finances.parametres', [
            'parametres' => $parametres,
            'categories' => $categories
        ]);
    }
    
    public function saveParametres(Request $request)
    {
        $request->validate([
            'devise' => 'required|string|max:10',
            'annee_fiscale_debut' => 'required|integer|min:1|max:12',
            'nb_echeances' => 'required_if:paiement_echelonne,on|nullable|integer|min:2|max:12',
            'taux_retard' => 'required_if:frais_retard,on|nullable|numeric|min:0|max:100',
            'delai_rappel' => 'required|integer|min:1|max:30',
        ]);

        $paiementEchelonne = $request->has('paiement_echelonne');
        $fraisRetard = $request->has('frais_retard');
        $rappelsAuto = $request->has('rappels_auto');

        // Mettre à jour ou créer les paramètres financiers
        $parametres = Parametre::first();
        
        if (!$parametres) {
            $parametres = new Parametre();
            
            // Valeurs par défaut si c'est la première fois
            $parametres->nom_etablissement = 'Mon Établissement';
            $parametres->annee_scolaire = date('Y') . '-' . (date('Y') + 1);
        }
        
        $parametres->devise = $request->devise;
        $parametres->annee_fiscale_debut = $request->annee_fiscale_debut;
        $parametres->paiement_echelonne = $paiementEchelonne;
        
        if ($paiementEchelonne) {
            $parametres->nb_echeances = $request->nb_echeances;
            $parametres->frais_retard = $fraisRetard;
            
            if ($fraisRetard) {
                $parametres->taux_retard = $request->taux_retard;
            }
        }
        
        $parametres->rappels_auto = $rappelsAuto;
        $parametres->delai_rappel = $request->delai_rappel;
        
        $parametres->save();

        return redirect()->back()->with('success', 'Paramètres financiers enregistrés avec succès');
    }
    
    public function ajouterCategorie(Request $request)
    {
        $request->validate([
            'type' => 'required|in:recette,depense',
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Créer ou mettre à jour la catégorie
        if ($request->filled('categorie_id')) {
            $categorie = CategorieFinanciere::findOrFail($request->categorie_id);
            $message = 'Catégorie mise à jour avec succès';
        } else {
            $categorie = new CategorieFinanciere();
            $message = 'Catégorie ajoutée avec succès';
        }
        
        $categorie->type = $request->type;
        $categorie->nom = $request->nom;
        $categorie->description = $request->description;
        $categorie->save();

        return redirect()->back()->with('success', $message);
    }
    
    public function supprimerCategorie($id)
    {
        $categorie = CategorieFinanciere::findOrFail($id);
        
        // Vérifier si des transactions sont liées à cette catégorie
        $transactionsCount = Transaction::where('categorie_id', $id)->count();
        
        if ($transactionsCount > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette catégorie car elle est utilisée dans ' . $transactionsCount . ' transaction(s).');
        }
        
        $categorie->delete();
        
        return redirect()->back()->with('success', 'Catégorie supprimée avec succès');
    }
    
    public function rapports()
    {
        $parametres = Parametre::first();
        $anneeScolaire = $parametres ? $parametres->annee_scolaire : null;
        
        $niveaux = Niveau::all();
        $categories = CategorieFinanciere::all();
        
        return view('finances.rapports', [
            'parametres' => $parametres,
            'niveaux' => $niveaux,
            'categories' => $categories
        ]);
    }
    
    public function genererRapport(Request $request)
    {
        $request->validate([
            'type_rapport' => 'required|in:general,mensuel,categorie',
            'periode_debut' => 'required_if:type_rapport,mensuel|date',
            'periode_fin' => 'required_if:type_rapport,mensuel|date|after_or_equal:periode_debut',
            'categorie_id' => 'required_if:type_rapport,categorie|exists:categories_financieres,id',
            'format' => 'required|in:pdf,excel',
        ]);
        
        $parametres = Parametre::first();
        
        if (!$parametres) {
            return redirect()->back()->with('error', 'Veuillez configurer les paramètres de l\'établissement');
        }
        
        $anneeScolaire = $parametres->annee_scolaire;
        
        // Logique pour générer le rapport spécifique
        switch ($request->type_rapport) {
            case 'general':
                // Rapport financier général
                $recettes = Transaction::where('type', 'recette')
                    ->where('annee_scolaire', $anneeScolaire)
                    ->sum('montant');
                
                $depenses = Transaction::where('type', 'depense')
                    ->where('annee_scolaire', $anneeScolaire)
                    ->sum('montant');
                
                $solde = $recettes - $depenses;
                
                // TODO: Générer le PDF ou Excel
                break;
                
            case 'mensuel':
                // Rapport financier pour une période spécifique
                $debut = $request->periode_debut;
                $fin = $request->periode_fin;
                
                $recettes = Transaction::where('type', 'recette')
                    ->where('annee_scolaire', $anneeScolaire)
                    ->whereBetween('date', [$debut, $fin])
                    ->sum('montant');
                
                $depenses = Transaction::where('type', 'depense')
                    ->where('annee_scolaire', $anneeScolaire)
                    ->whereBetween('date', [$debut, $fin])
                    ->sum('montant');
                
                $solde = $recettes - $depenses;
                
                // TODO: Générer le PDF ou Excel
                break;
                
            case 'categorie':
                // Rapport par catégorie
                $categorieId = $request->categorie_id;
                $categorie = CategorieFinanciere::findOrFail($categorieId);
                
                $transactions = Transaction::where('categorie_id', $categorieId)
                    ->where('annee_scolaire', $anneeScolaire)
                    ->get();
                
                $total = $transactions->sum('montant');
                
                // TODO: Générer le PDF ou Excel
                break;
        }
        
        // Pour l'instant, rediriger avec un message
        return redirect()->back()->with('success', 'La fonctionnalité de génération de rapports sera implémentée ultérieurement.');
    }
}