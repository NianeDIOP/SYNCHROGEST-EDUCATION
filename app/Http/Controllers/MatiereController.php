<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CategorieArticle;
use App\Models\Fournisseur;
use App\Models\MouvementStock;
use App\Models\Parametre;
use App\Exports\StockExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);
        
        // Création de l'article
        $article = new Article();
        $article->code = $request->code;
        $article->designation = $request->designation;
        $article->description = $request->description;
        $article->categorie_id = $request->categorie_id;
        $article->unite_mesure = $request->unite_mesure;
        $article->quantite_stock = $request->quantite_stock;
        $article->seuil_alerte = $request->seuil_alerte;
        $article->prix_unitaire = $request->prix_unitaire;
        $article->emplacement = $request->emplacement;
        $article->est_actif = $request->has('est_actif');
        
        // Gestion de l'image si fournie
        if ($request->hasFile('image')) {
            $imageName = Str::slug($request->code) . '-' . time() . '.' . $request->image->extension();
            $request->image->storeAs('public/articles', $imageName);
            $article->image = $imageName;
        }
        
        $article->save();
        
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
    
    /**
     * Afficher les détails d'un article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showArticle($id)
    {
        $article = Article::findOrFail($id);
        
        // Récupérer les derniers mouvements de cet article
        $mouvements = MouvementStock::where('article_id', $id)
            ->with(['user', 'fournisseur'])
            ->orderBy('date_mouvement', 'desc')
            ->limit(10)
            ->get();
        
        return view('matieres.showArticle', compact('article', 'mouvements'));
    }

    /**
     * Afficher le formulaire d'édition d'un article.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editArticle($id)
    {
        $article = Article::findOrFail($id);
        $categories = CategorieArticle::all();
        
        return view('matieres.editArticle', compact('article', 'categories'));
    }

    /**
     * Mettre à jour un article dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateArticle(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        
        $request->validate([
            'code' => 'required|string|max:50|unique:articles,code,' . $id,
            'designation' => 'required|string|max:255',
            'categorie_id' => 'required|exists:categories_articles,id',
            'unite_mesure' => 'required|string|max:50',
            'seuil_alerte' => 'required|numeric|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);
        
        // Gérer l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($article->image && Storage::exists('public/articles/' . $article->image)) {
                Storage::delete('public/articles/' . $article->image);
            }
            
            // Sauvegarder la nouvelle image
            $imageName = Str::slug($request->code) . '-' . time() . '.' . $request->image->extension();
            $request->image->storeAs('public/articles', $imageName);
            $article->image = $imageName;
        } elseif ($request->has('supprimer_image') && $request->supprimer_image) {
            // Supprimer l'image existante
            if ($article->image && Storage::exists('public/articles/' . $article->image)) {
                Storage::delete('public/articles/' . $article->image);
            }
            $article->image = null;
        }
        
        // Mise à jour des autres champs
        $article->code = $request->code;
        $article->designation = $request->designation;
        $article->description = $request->description;
        $article->categorie_id = $request->categorie_id;
        $article->unite_mesure = $request->unite_mesure;
        $article->seuil_alerte = $request->seuil_alerte;
        $article->prix_unitaire = $request->prix_unitaire;
        $article->emplacement = $request->emplacement;
        $article->est_actif = $request->has('est_actif');
        
        $article->save();
        
        return redirect()->route('matieres.showArticle', $article->id)
            ->with('success', 'Article modifié avec succès');
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
    
    public function nouveauMouvement(Request $request)
    {
        $articles = Article::orderBy('designation')->get();
        
        // Vérifier si la table fournisseurs existe avant d'y accéder
        $fournisseurs = [];
        try {
            // Vérifier si la table existe
            if (Schema::hasTable('fournisseurs')) {
                $fournisseurs = Fournisseur::where('est_actif', true)->orderBy('nom')->get();
            }
        } catch (\Exception $e) {
            // Si une erreur se produit, ne pas arrêter l'exécution
            $fournisseurs = [];
        }
        
        // Pré-remplir l'article si fourni dans la requête
        $articlePreselectionne = null;
        $typeMouvementPreselectionne = null;
        
        if ($request->has('article_id')) {
            $articlePreselectionne = $request->article_id;
        }
        
        if ($request->has('type_mouvement')) {
            $typeMouvementPreselectionne = $request->type_mouvement;
        }
        
        return view('matieres.nouveau_mouvement', [
            'articles' => $articles,
            'fournisseurs' => $fournisseurs,
            'article_id' => $articlePreselectionne,
            'type_mouvement' => $typeMouvementPreselectionne
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
        'fournisseur_id' => 'nullable', // Changé de 'required_if:type_mouvement,entrée|nullable|exists:fournisseurs,id'
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
        $mouvementData = [
            'article_id' => $request->article_id,
            'type_mouvement' => $request->type_mouvement,
            'quantite' => $request->quantite,
            'date_mouvement' => $request->date_mouvement,
            'motif' => $request->motif,
            'reference_document' => $request->reference_document,
            'destinataire' => $request->type_mouvement === 'sortie' ? $request->destinataire : null,
            'user_id' => auth()->id(),
        ];
        
        // Vérifier si la table fournisseurs existe avant d'ajouter le fournisseur_id
        if (Schema::hasTable('fournisseurs') && $request->type_mouvement === 'entrée' && $request->fournisseur_id) {
            $mouvementData['fournisseur_id'] = $request->fournisseur_id;
        }
        
        MouvementStock::create($mouvementData);
        
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
        try {
            // Vérifier si la table existe
            if (!Schema::hasTable('fournisseurs')) {
                return view('matieres.fournisseurs', [
                    'fournisseurs' => collect([])
                ])->with('error', 'La table des fournisseurs n\'existe pas encore. Veuillez exécuter les migrations.');
            }
            
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
        } catch (\Exception $e) {
            return view('matieres.fournisseurs', [
                'fournisseurs' => collect([])
            ])->with('error', 'Erreur lors de l\'accès à la table des fournisseurs : ' . $e->getMessage());
        }
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
    
    /**
     * Afficher le formulaire d'édition d'un fournisseur.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editFournisseur($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        
        return view('matieres.editFournisseur', compact('fournisseur'));
    }

    /**
     * Mettre à jour un fournisseur dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFournisseur(Request $request, $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'personne_contact' => 'nullable|string|max:255',
            'telephone_contact' => 'nullable|string|max:20',
        ]);
        
        $fournisseur->nom = $request->nom;
        $fournisseur->adresse = $request->adresse;
        $fournisseur->telephone = $request->telephone;
        $fournisseur->email = $request->email;
        $fournisseur->personne_contact = $request->personne_contact;
        $fournisseur->telephone_contact = $request->telephone_contact;
        $fournisseur->est_actif = $request->has('est_actif');
        
        $fournisseur->save();
        
        return redirect()->route('matieres.fournisseurs')
            ->with('success', 'Fournisseur modifié avec succès');
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
        $articles = Article::orderBy('designation')->get();
        $fournisseurs = Fournisseur::where('est_actif', true)->orderBy('nom')->get();
        
        return view('matieres.rapports', compact('categories', 'articles', 'fournisseurs'));
    }
    
    /**
     * Générer différents types de rapports selon les filtres.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function genererRapport(Request $request)
    {
        $request->validate([
            'type_rapport' => 'required|in:stock,mouvements,fournisseurs',
            'format' => 'required|in:pdf,excel',
        ]);
        
        $parametres = Parametre::first();
        
        switch ($request->type_rapport) {
            case 'stock':
                return $this->genererRapportStock($request, $parametres);
                break;
            
            case 'mouvements':
                return $this->genererRapportMouvements($request, $parametres);
                break;
            
            case 'fournisseurs':
                return $this->genererRapportFournisseurs($request, $parametres);
                break;
            
            default:
                return redirect()->back()->with('error', 'Type de rapport non reconnu');
        }
    }

    /**
     * Génère un rapport d'état du stock.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Parametre  $parametres
     * @return \Illuminate\Http\Response
     */
    private function genererRapportStock($request, $parametres)
    {
        // Filtre par catégorie
        $query = Article::with('categorie');
        
        if ($request->has('categorie_id') && !empty($request->categorie_id)) {
            $query->where('categorie_id', $request->categorie_id);
        }
        
        // Filtre par état du stock
        if ($request->has('etat_stock')) {
            if ($request->etat_stock === 'alerte') {
                $query->where('quantite_stock', '<=', DB::raw('seuil_alerte'))
                    ->where('quantite_stock', '>', 0);
            } elseif ($request->etat_stock === 'rupture') {
                $query->where('quantite_stock', '<=', 0);
            }
        }
        
        $articles = $query->orderBy('designation')->get();
        
        // Calculs pour le rapport
        $totalArticles = $articles->count();
        $valeurTotale = $articles->sum(function ($article) {
            return $article->quantite_stock * $article->prix_unitaire;
        });
        
        // Titre du rapport
        $categorie = null;
        if ($request->has('categorie_id') && !empty($request->categorie_id)) {
            $categorie = CategorieArticle::find($request->categorie_id);
        }
        
        $titre = 'État du Stock';
        if ($categorie) {
            $titre .= ' - Catégorie : ' . $categorie->nom;
        }
        
        if ($request->etat_stock === 'alerte') {
            $titre .= ' - Articles en Alerte';
        } elseif ($request->etat_stock === 'rupture') {
            $titre .= ' - Articles en Rupture';
        }
        
        // Générer selon le format
        if ($request->format === 'pdf') {
            $pdf = PDF::loadView('matieres.rapports.stock_pdf', [
                'articles' => $articles,
                'parametres' => $parametres,
                'titre' => $titre,
                'totalArticles' => $totalArticles,
                'valeurTotale' => $valeurTotale,
                'dateGeneration' => Carbon::now()->format('d/m/Y H:i')
            ]);
            
            return $pdf->download('rapport-stock-' . date('Y-m-d') . '.pdf');
        } else {
            // Pour Excel, vous devriez créer une classe d'export
            // qui étend Maatwebsite\Excel\Concerns\FromCollection
            return Excel::download(new StockExport($articles, $titre, $parametres), 'rapport-stock-' . date('Y-m-d') . '.xlsx');
        }
    }

    /**
     * Génère un rapport des mouvements de stock.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Parametre  $parametres
     * @return \Illuminate\Http\Response
     */
    private function genererRapportMouvements($request, $parametres)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);
        
        // Construire la requête
        $query = MouvementStock::with(['article.categorie', 'user', 'fournisseur'])
            ->whereBetween('date_mouvement', [$request->date_debut, $request->date_fin]);
        
        // Filtrer par type de mouvement
        if ($request->has('type_mouvement') && !empty($request->type_mouvement)) {
            $query->where('type_mouvement', $request->type_mouvement);
        }
        
        // Filtrer par article
        if ($request->has('article_id') && !empty($request->article_id)) {
            $query->where('article_id', $request->article_id);
        }
        
        $mouvements = $query->orderBy('date_mouvement', 'desc')->get();
        
        // Titre du rapport
        $titre = 'Rapport des Mouvements de Stock';
        $titre .= ' du ' . Carbon::parse($request->date_debut)->format('d/m/Y');
        $titre .= ' au ' . Carbon::parse($request->date_fin)->format('d/m/Y');
        
        if ($request->has('type_mouvement') && !empty($request->type_mouvement)) {
            $titre .= ' - Type : ' . ucfirst($request->type_mouvement) . 's';
        }
        
        if ($request->has('article_id') && !empty($request->article_id)) {
            $article = Article::find($request->article_id);
            if ($article) {
                $titre .= ' - Article : ' . $article->designation;
            }
        }
        
        // Pour l'instant, renvoyer un message temporaire
        // TODO: Implémenter la génération de PDF/Excel avec une bibliothèque
        return redirect()->back()->with('info', 'La génération de rapport sera implémentée prochainement. Titre du rapport : ' . $titre);
    }

    /**
     * Génère un rapport des fournisseurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Parametre  $parametres
     * @return \Illuminate\Http\Response
     */
    private function genererRapportFournisseurs($request, $parametres)
    {
        // Construire la requête
        $query = Fournisseur::query();
        
        // Filtrer par fournisseur spécifique
        if ($request->has('fournisseur_id') && !empty($request->fournisseur_id)) {
            $query->where('id', $request->fournisseur_id);
        }
        
        $fournisseurs = $query->orderBy('nom')->get();
        
        // Récupérer les transactions si demandé
        $transactions = [];
        if ($request->has('inclure_transactions') && $request->inclure_transactions) {
            foreach ($fournisseurs as $fournisseur) {
                $transactionQuery = MouvementStock::where('fournisseur_id', $fournisseur->id)
                    ->with(['article']);
                
                // Filtrer par date si demandé
                if ($request->has('date_debut') && !empty($request->date_debut)) {
                    $transactionQuery->where('date_mouvement', '>=', $request->date_debut);
                }
                
                if ($request->has('date_fin') && !empty($request->date_fin)) {
                    $transactionQuery->where('date_mouvement', '<=', $request->date_fin);
                }
                
                $transactions[$fournisseur->id] = $transactionQuery->orderBy('date_mouvement', 'desc')->get();
            }
        }
        
        // Titre du rapport
        $titre = 'Rapport des Fournisseurs';
        
        if ($request->has('fournisseur_id') && !empty($request->fournisseur_id)) {
            $fournisseur = Fournisseur::find($request->fournisseur_id);
            if ($fournisseur) {
                $titre .= ' - ' . $fournisseur->nom;
            }
        }
        
        // Pour l'instant, renvoyer un message temporaire
        // TODO: Implémenter la génération de PDF/Excel avec une bibliothèque
        return redirect()->back()->with('info', 'La génération de rapport sera implémentée prochainement. Titre du rapport : ' . $titre);
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