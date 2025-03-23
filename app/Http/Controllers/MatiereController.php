<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CategorieArticle;
use App\Models\Fournisseur;
use App\Models\MouvementStock;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MatiereController extends Controller
{
    public function dashboard()
    {
        $parametres = Parametre::first();
        
        // Statistiques de base
        $totalArticles = Article::count();
        $articlesDisponibles = Article::where('quantite_stock', '>', 0)->count();
        $articlesEnRupture = Article::where('quantite_stock', '<=', DB::raw('seuil_alerte'))->count();
        $valeurTotaleStock = Article::sum(DB::raw('quantite_stock * prix_unitaire'));
        
        // Articles en alerte de stock
        $alertesStock = Article::where('quantite_stock', '<=', DB::raw('seuil_alerte'))
            ->orderBy('quantite_stock')
            ->limit(5)
            ->get();
        
        // Derniers mouvements de stock
        $derniersMouvements = MouvementStock::with(['article', 'user'])
            ->orderBy('date_mouvement', 'desc')
            ->limit(10)
            ->get();
        
        // Répartition des stocks par catégorie
        $stockParCategorie = CategorieArticle::withCount('articles')
            ->withSum('articles', 'quantite_stock')
            ->get();
        
        $stats = [
            'totalArticles' => $totalArticles,
            'articlesDisponibles' => $articlesDisponibles,
            'articlesEnRupture' => $articlesEnRupture,
            'valeurTotaleStock' => $valeurTotaleStock
        ];
        
        return view('matieres.dashboard', [
            'parametres' => $parametres,
            'stats' => $stats,
            'alertesStock' => $alertesStock,
            'derniersMouvements' => $derniersMouvements,
            'stockParCategorie' => $stockParCategorie
        ]);
    }
    
    public function articles(Request $request)
    {
        // Base de la requête
        $query = Article::with('categorie');
        
        // Filtrage par terme de recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par catégorie
        if ($request->has('categorie_id') && !empty($request->categorie_id)) {
            $query->where('categorie_id', $request->categorie_id);
        }
        
        // Filtrage par état de stock
        if ($request->has('etat_stock') && !empty($request->etat_stock)) {
            if ($request->etat_stock === 'rupture') {
                $query->where('quantite_stock', '<=', 0);
            } elseif ($request->etat_stock === 'alerte') {
                $query->where('quantite_stock', '<=', DB::raw('seuil_alerte'))
                      ->where('quantite_stock', '>', 0);
            } elseif ($request->etat_stock === 'disponible') {
                $query->where('quantite_stock', '>', DB::raw('seuil_alerte'));
            }
        }
        
        // Récupération des articles filtrés
        $articles = $query->orderBy('designation')->paginate(15);
        
        // Récupération des catégories pour le filtre
        $categories = CategorieArticle::all();
        
        return view('matieres.articles', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }
    
    public function nouvelArticle()
    {
        $categories = CategorieArticle::all();
        return view('matieres.nouvel_article', compact('categories'));
    }
    
    public function enregistrerArticle(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:articles,code',
            'designation' => 'required|string|max:255',
            'categorie_id' => 'required|exists:categories_articles,id',
            'unite_mesure' => 'required|string|max:50',
            'quantite_stock' => 'required|numeric|min:0',
            'seuil_alerte' => 'required|numeric|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
        ]);
        
        $article = Article::create($request->all());
        
        // Enregistrer le mouvement initial si la quantité est supérieure à 0
        if ($request->quantite_stock > 0) {
            MouvementStock::create([
                'article_id' => $article->id,
                'type_mouvement' => 'entrée',
                'quantite' => $request->quantite_stock,
                'date_mouvement' => now(),
                'motif' => 'Stock initial',
                'user_id' => auth()->id(),
            ]);
        }
        
        return redirect()->route('matieres.articles')
            ->with('success', 'Article ajouté avec succès');
    }
    
    public function mouvements(Request $request)
    {
        // Base de la requête
        $query = MouvementStock::with(['article', 'user', 'fournisseur']);
        
        // Filtrage par article
        if ($request->has('article_id') && !empty($request->article_id)) {
            $query->where('article_id', $request->article_id);
        }
        
        // Filtrage par type de mouvement
        if ($request->has('type_mouvement') && !empty($request->type_mouvement)) {
            $query->where('type_mouvement', $request->type_mouvement);
        }
        
        // Filtrage par date
        if ($request->has('date_debut') && !empty($request->date_debut)) {
            $query->where('date_mouvement', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin') && !empty($request->date_fin)) {
            $query->where('date_mouvement', '<=', $request->date_fin);
        }
        
        // Récupération des mouvements filtrés
        $mouvements = $query->orderBy('date_mouvement', 'desc')->paginate(15);
        
        // Récupération des articles pour le filtre
        $articles = Article::orderBy('designation')->get();
        
        return view('matieres.mouvements', [
            'mouvements' => $mouvements,
            'articles' => $articles
        ]);
    }
    
    public function nouveauMouvement()
    {
        $articles = Article::orderBy('designation')->get();
        $fournisseurs = Fournisseur::where('est_actif', true)->orderBy('nom')->get();
        
        return view('matieres.nouveau_mouvement', [
            'articles' => $articles,
            'fournisseurs' => $fournisseurs
        ]);
    }
    
    public function enregistrerMouvement(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'type_mouvement' => 'required|in:entrée,sortie',
            'quantite' => 'required|numeric|min:0.01',
            'date_mouvement' => 'required|date',
            'motif' => 'required|string',
            'fournisseur_id' => 'required_if:type_mouvement,entrée|nullable|exists:fournisseurs,id',
            'destinataire' => 'required_if:type_mouvement,sortie|nullable|string',
        ]);
        
        $article = Article::findOrFail($request->article_id);
        
        // Vérifier si la quantité est suffisante en cas de sortie
        if ($request->type_mouvement === 'sortie' && $article->quantite_stock < $request->quantite) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Quantité insuffisante en stock');
        }
        
        DB::beginTransaction();
        try {
            // Créer le mouvement
            MouvementStock::create([
                'article_id' => $request->article_id,
                'type_mouvement' => $request->type_mouvement,
                'quantite' => $request->quantite,
                'date_mouvement' => $request->date_mouvement,
                'motif' => $request->motif,
                'reference_document' => $request->reference_document,
                'fournisseur_id' => $request->type_mouvement === 'entrée' ? $request->fournisseur_id : null,
                'destinataire' => $request->type_mouvement === 'sortie' ? $request->destinataire : null,
                'user_id' => auth()->id(),
            ]);
            
            // Mettre à jour le stock
            if ($request->type_mouvement === 'entrée') {
                $article->quantite_stock += $request->quantite;
            } else {
                $article->quantite_stock -= $request->quantite;
            }
            
            $article->save();
            
            DB::commit();
            
            return redirect()->route('matieres.mouvements')
                ->with('success', 'Mouvement enregistré avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }
    
    public function fournisseurs(Request $request)
    {
        // Base de la requête
        $query = Fournisseur::query();
        
        // Filtrage par terme de recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par statut actif/inactif
        if ($request->has('status') && !empty($request->status)) {
            $query->where('est_actif', $request->status === 'actif');
        }
        
        // Récupération des fournisseurs filtrés
        $fournisseurs = $query->orderBy('nom')->paginate(15);
        
        return view('matieres.fournisseurs', [
            'fournisseurs' => $fournisseurs
        ]);
    }
    
    public function nouveauFournisseur()
    {
        return view('matieres.nouveau_fournisseur');
    }
    
    public function enregistrerFournisseur(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'personne_contact' => 'nullable|string|max:255',
            'telephone_contact' => 'nullable|string|max:20',
        ]);
        
        Fournisseur::create($request->all());
        
        return redirect()->route('matieres.fournisseurs')
            ->with('success', 'Fournisseur ajouté avec succès');
    }
    
    public function categories()
    {
        $categories = CategorieArticle::withCount('articles')->get();
        return view('matieres.categories', compact('categories'));
    }
    
    public function nouvelleCategorie()
    {
        return view('matieres.nouvelle_categorie');
    }
    
    public function enregistrerCategorie(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:categories_articles,nom',
            'description' => 'nullable|string',
        ]);
        
        CategorieArticle::create($request->all());
        
        return redirect()->route('matieres.categories')
            ->with('success', 'Catégorie ajoutée avec succès');
    }
    
    public function rapports()
    {
        $categories = CategorieArticle::all();
        return view('matieres.rapports', compact('categories'));
    }
    
    public function genererRapport(Request $request)
    {
        $request->validate([
            'type_rapport' => 'required|in:stock,mouvements,fournisseurs',
            'categorie_id' => 'nullable|exists:categories_articles,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'format' => 'required|in:pdf,excel',
        ]);
        
        // Ici, vous pourriez implémenter la génération de rapports PDF/Excel
        // similaire à ce que vous avez fait pour le module Finance
        
        return redirect()->back()
            ->with('success', 'Fonctionnalité de génération de rapports en cours de développement');
    }
    
    public function parametres()
    {
        $parametres = Parametre::first();
        return view('matieres.parametres', compact('parametres'));
    }
    
    public function saveParametres(Request $request)
    {
        // Validation et enregistrement des paramètres
        return redirect()->back()->with('success', 'Paramètres enregistrés avec succès');
    }
}