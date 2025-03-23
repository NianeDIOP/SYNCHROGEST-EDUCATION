@extends('layouts.app')

@section('title', 'Gestion des Articles')
@section('page-title', 'Gestion des Articles')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Liste des articles</h6>
        <a href="{{ route('matieres.nouvelArticle') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> Nouvel article
        </a>
    </div>
    <div class="card-body">
        <!-- Filtres de recherche -->
        <form action="{{ route('matieres.articles') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Code, désignation...">
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="categorie_id" class="form-label">Catégorie</label>
                    <select class="form-select" id="categorie_id" name="categorie_id">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ request('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="etat_stock" class="form-label">État du stock</label>
                    <select class="form-select" id="etat_stock" name="etat_stock">
                        <option value="">Tous les états</option>
                        <option value="disponible" {{ request('etat_stock') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="alerte" {{ request('etat_stock') == 'alerte' ? 'selected' : '' }}>En alerte</option>
                        <option value="rupture" {{ request('etat_stock') == 'rupture' ? 'selected' : '' }}>En rupture</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('matieres.articles') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        <!-- Tableau des articles -->
        @if($articles->isEmpty())
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fa-2x"></i>
                    <div>
                        <h5 class="mb-1">Aucun article trouvé</h5>
                        <p class="mb-0">Veuillez ajuster vos critères de recherche ou ajouter de nouveaux articles.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Désignation</th>
                            <th>Catégorie</th>
                            <th>Stock</th>
                            <th>Seuil</th>
                            <th>Prix unitaire</th>
                            <th>Valeur stock</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $article)
                            <tr class="{{ $article->quantite_stock <= 0 ? 'table-danger' : ($article->quantite_stock <= $article->seuil_alerte ? 'table-warning' : '') }}">
                                <td>{{ $article->code }}</td>
                                <td>{{ $article->designation }}</td>
                                <td>{{ $article->categorie->nom }}</td>
                                <td>{{ $article->quantite_stock }} {{ $article->unite_mesure }}</td>
                                <td>{{ $article->seuil_alerte }} {{ $article->unite_mesure }}</td>
                                <td>{{ number_format($article->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                <td>{{ number_format($article->valeurStock(), 0, ',', ' ') }} FCFA</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('matieres.nouveauMouvement', ['article_id' => $article->id, 'type_mouvement' => 'entrée']) }}" class="btn btn-sm btn-success" title="Entrée">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                        <a href="{{ route('matieres.nouveauMouvement', ['article_id' => $article->id, 'type_mouvement' => 'sortie']) }}" class="btn btn-sm btn-danger" title="Sortie">
                                            <i class="fas fa-arrow-up"></i>
                                        </a>
                                        <a href="{{ route('matieres.showArticle', $article->id) }}" class="btn btn-sm btn-info" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('matieres.editArticle', $article->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $articles->firstItem() ?? 0 }} à {{ $articles->lastItem() ?? 0 }} sur {{ $articles->total() }} articles
                </div>
                {{ $articles->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection