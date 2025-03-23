@extends('layouts.app')

@section('title', 'Tableau de Bord - Matières')
@section('page-title', 'Tableau de Bord - Gestion des Stocks')

@section('content')
<!-- Content Row - Statistics Cards -->
<div class="row">
    <!-- Total Articles Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            TOTAL DES ARTICLES</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['totalArticles']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Articles Disponibles Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            ARTICLES DISPONIBLES</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['articlesDisponibles']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Articles en Rupture Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            ARTICLES EN ALERTE</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['articlesEnRupture']) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Valeur Stock Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            VALEUR TOTALE DU STOCK</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['valeurTotaleStock'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Alertes de Stock -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Alertes de stock</h6>
                <a href="{{ route('matieres.articles', ['etat_stock' => 'alerte']) }}" class="btn btn-sm btn-primary">
                    Voir tout
                </a>
            </div>
            <div class="card-body">
                @if(isset($alertesStock) && count($alertesStock) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Catégorie</th>
                                    <th>Stock</th>
                                    <th>Seuil</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alertesStock as $article)
                                <tr class="{{ $article->quantite_stock <= 0 ? 'table-danger' : 'table-warning' }}">
                                    <td>{{ $article->designation }}</td>
                                    <td>{{ $article->categorie->nom }}</td>
                                    <td>{{ $article->quantite_stock }} {{ $article->unite_mesure }}</td>
                                    <td>{{ $article->seuil_alerte }} {{ $article->unite_mesure }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('matieres.nouveauMouvement', ['article_id' => $article->id]) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus-circle"></i> Entrée
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i> Aucun article en alerte de stock.
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Derniers mouvements -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Derniers mouvements</h6>
                <a href="{{ route('matieres.mouvements') }}" class="btn btn-sm btn-primary">
                    Voir tout
                </a>
            </div>
            <div class="card-body">
                @if(isset($derniersMouvements) && count($derniersMouvements) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Article</th>
                                    <th>Type</th>
                                    <th>Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($derniersMouvements as $mouvement)
                                <tr>
                                    <td>{{ $mouvement->date_mouvement->format('d/m/Y') }}</td>
                                    <td>{{ $mouvement->article->designation }}</td>
                                    <td>
                                        <span class="badge bg-{{ $mouvement->type_mouvement == 'entrée' ? 'success' : 'danger' }} rounded-pill">
                                            {{ ucfirst($mouvement->type_mouvement) }}
                                        </span>
                                    </td>
                                    <td>{{ $mouvement->quantite }} {{ $mouvement->article->unite_mesure }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucun mouvement de stock récent.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Répartition par catégorie -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Répartition du stock par catégorie</h6>
            </div>
            <div class="card-body">
                @if(isset($stockParCategorie) && count($stockParCategorie) > 0)
                    @foreach($stockParCategorie as $categorie)
                        <h4 class="small font-weight-bold">
                            {{ $categorie->nom }}
                            <span class="float-end">{{ $categorie->articles_count }} article(s)</span>
                        </h4>
                        <div class="progress mb-4">
                            <div class="progress-bar" role="progressbar" style="width: {{ min(100, $categorie->articles_sum_quantite_stock) }}%">
                                {{ $categorie->articles_sum_quantite_stock ?? 0 }} unités
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucune donnée de stock par catégorie disponible.
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Actions rapides -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Actions rapides</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('matieres.nouvelArticle') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-plus-circle me-2"></i> Nouvel article
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('matieres.nouveauMouvement') }}" class="btn btn-success btn-block">
                            <i class="fas fa-exchange-alt me-2"></i> Nouveau mouvement
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('matieres.nouveauFournisseur') }}" class="btn btn-info btn-block">
                            <i class="fas fa-truck me-2"></i> Nouveau fournisseur
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('matieres.rapports') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-chart-bar me-2"></i> Générer rapport
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ici, vous pourriez initialiser des graphiques ou d'autres
        // éléments interactifs pour le tableau de bord
    });
</script>
@endsection