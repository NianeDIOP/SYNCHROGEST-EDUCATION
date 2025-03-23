@extends('layouts.app')

@section('title', 'Paramètres - Gestion des Matières')
@section('page-title', 'Paramètres du module Matières')

@section('content')
<div class="row">
    <!-- Paramètres généraux -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Paramètres généraux</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('matieres.saveParametres') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="emplacement_defaut" class="form-label">Emplacement par défaut pour les nouveaux articles</label>
                        <input type="text" class="form-control" id="emplacement_defaut" name="emplacement_defaut" value="{{ $parametres->emplacement_defaut ?? '' }}">
                        <div class="form-text">Cet emplacement sera suggéré lors de la création d'un nouvel article.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="seuil_alerte_defaut" class="form-label">Seuil d'alerte par défaut</label>
                        <input type="number" class="form-control" id="seuil_alerte_defaut" name="seuil_alerte_defaut" value="{{ $parametres->seuil_alerte_defaut ?? 5 }}" min="0">
                        <div class="form-text">Nombre d'unités à partir duquel un article sera considéré en alerte de stock.</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="notifications_rupture" name="notifications_rupture" value="1" {{ ($parametres->notifications_rupture ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="notifications_rupture">
                                Activer les notifications de rupture de stock
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Enregistrer les paramètres
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Préférences d'affichage -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Préférences d'affichage</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('matieres.saveParametres') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="affichage">
                    
                    <div class="mb-3">
                        <label for="articles_par_page" class="form-label">Nombre d'articles par page</label>
                        <select class="form-select" id="articles_par_page" name="articles_par_page">
                            <option value="10" {{ ($parametres->articles_par_page ?? 15) == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ ($parametres->articles_par_page ?? 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ ($parametres->articles_par_page ?? 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($parametres->articles_par_page ?? 15) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($parametres->articles_par_page ?? 15) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Colonnes à afficher dans la liste des articles</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="col_code" name="colonnes[]" value="code" {{ in_array('code', $parametres->colonnes_article ?? ['code', 'designation', 'categorie', 'stock', 'valeur']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="col_code">Code</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="col_designation" name="colonnes[]" value="designation" {{ in_array('designation', $parametres->colonnes_article ?? ['code', 'designation', 'categorie', 'stock', 'valeur']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="col_designation">Désignation</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="col_categorie" name="colonnes[]" value="categorie" {{ in_array('categorie', $parametres->colonnes_article ?? ['code', 'designation', 'categorie', 'stock', 'valeur']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="col_categorie">Catégorie</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="col_stock" name="colonnes[]" value="stock" {{ in_array('stock', $parametres->colonnes_article ?? ['code', 'designation', 'categorie', 'stock', 'valeur']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="col_stock">Stock</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="col_seuil" name="colonnes[]" value="seuil" {{ in_array('seuil', $parametres->colonnes_article ?? ['code', 'designation', 'categorie', 'stock', 'valeur']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="col_seuil">Seuil d'alerte</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="col_valeur" name="colonnes[]" value="valeur" {{ in_array('valeur', $parametres->colonnes_article ?? ['code', 'designation', 'categorie', 'stock', 'valeur']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="col_valeur">Valeur stock</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Enregistrer les préférences
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Paramètres système -->
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Information sur le système</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-bold">Établissement :</label>
                            <p>{{ $parametres->nom_etablissement ?? 'Non configuré' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Année scolaire active :</label>
                            <p>{{ $parametres->annee_scolaire ?? 'Non configurée' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-bold">Total des articles :</label>
                            <p>{{ App\Models\Article::count() }} articles</p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Valeur totale de l'inventaire :</label>
                            <p>{{ number_format(App\Models\Article::sum(DB::raw('quantite_stock * prix_unitaire')), 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Pour modifier les paramètres généraux de l'établissement, veuillez vous rendre dans le module Inscriptions.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection