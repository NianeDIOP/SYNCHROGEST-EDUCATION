@extends('layouts.app')

@section('title', 'Détails de l\'Article')
@section('page-title', 'Fiche détaillée de l\'article')

@section('content')
<div class="row">
    <!-- Détails de l'article -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                <div>
                    <a href="{{ route('matieres.editArticle', $article->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                    <a href="{{ route('matieres.nouveauMouvement', ['article_id' => $article->id]) }}" class="btn btn-sm btn-success ms-2">
                        <i class="fas fa-exchange-alt me-1"></i> Mouvement
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Code article</label>
                            <p class="border-bottom pb-2">{{ $article->code }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Désignation</label>
                            <p class="border-bottom pb-2">{{ $article->designation }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Catégorie</label>
                            <p class="border-bottom pb-2">{{ $article->categorie->nom }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Unité de mesure</label>
                            <p class="border-bottom pb-2">{{ $article->unite_mesure }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Emplacement</label>
                            <p class="border-bottom pb-2">{{ $article->emplacement ?: 'Non spécifié' }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Prix unitaire</label>
                            <p class="border-bottom pb-2">{{ number_format($article->prix_unitaire, 0, ',', ' ') }} FCFA</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Statut</label>
                            <p class="border-bottom pb-2">
                                <span class="badge bg-{{ $article->est_actif ? 'success' : 'secondary' }}">
                                    {{ $article->est_actif ? 'Actif' : 'Inactif' }}
                                </span>
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Date de création</label>
                            <p class="border-bottom pb-2">{{ $article->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                
                @if($article->description)
                <div class="mb-4">
                    <label class="form-label fw-bold">Description</label>
                    <p class="border-bottom pb-2">{{ $article->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- État du stock -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">État du stock</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($article->quantite_stock <= 0)
                        <div class="display-4 text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4 class="text-danger">RUPTURE DE STOCK</h4>
                    @elseif($article->quantite_stock <= $article->seuil_alerte)
                        <div class="display-4 text-warning">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <h4 class="text-warning">STOCK EN ALERTE</h4>
                    @else
                        <div class="display-4 text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4 class="text-success">STOCK DISPONIBLE</h4>
                    @endif
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-muted mb-1">Quantité actuelle</h6>
                            <h3 class="mb-0">{{ $article->quantite_stock }} <small>{{ $article->unite_mesure }}</small></h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-muted mb-1">Seuil d'alerte</h6>
                            <h3 class="mb-0">{{ $article->seuil_alerte }} <small>{{ $article->unite_mesure }}</small></h3>
                        </div>
                    </div>
                </div>
                
                <div class="border rounded p-3 mb-3 text-center">
                    <h6 class="text-muted mb-1">Valeur totale du stock</h6>
                    <h3 class="mb-0">{{ number_format($article->valeurStock(), 0, ',', ' ') }} <small>FCFA</small></h3>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('matieres.nouveauMouvement', ['article_id' => $article->id, 'type_mouvement' => 'entrée']) }}" class="btn btn-success">
                        <i class="fas fa-arrow-down me-1"></i> Entrée de stock
                    </a>
                    <a href="{{ route('matieres.nouveauMouvement', ['article_id' => $article->id, 'type_mouvement' => 'sortie']) }}" class="btn btn-danger">
                        <i class="fas fa-arrow-up me-1"></i> Sortie de stock
                    </a>
                </div>
            </div>
        </div>
        
        @if($article->image)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Image de l'article</h6>
            </div>
            <div class="card-body text-center">
                <img src="{{ asset('storage/articles/' . $article->image) }}" alt="{{ $article->designation }}" class="img-fluid rounded">
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Historique des mouvements -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Historique des mouvements</h6>
        <a href="{{ route('matieres.mouvements', ['article_id' => $article->id]) }}" class="btn btn-sm btn-primary">
            Voir tout l'historique
        </a>
    </div>
    <div class="card-body">
        @if($mouvements->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Aucun mouvement enregistré pour cet article.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Motif</th>
                            <th>Référence</th>
                            <th>Fournisseur/Destinataire</th>
                            <th>Enregistré par</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mouvements as $mouvement)
                            <tr>
                                <td>{{ $mouvement->date_mouvement->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $mouvement->type_mouvement == 'entrée' ? 'success' : 'danger' }} rounded-pill">
                                        {{ ucfirst($mouvement->type_mouvement) }}
                                    </span>
                                </td>
                                <td>{{ $mouvement->quantite }} {{ $article->unite_mesure }}</td>
                                <td>{{ $mouvement->motif }}</td>
                                <td>{{ $mouvement->reference_document ?: 'N/A' }}</td>
                                <td>
                                    @if($mouvement->type_mouvement == 'entrée')
                                        {{ $mouvement->fournisseur->nom ?? 'N/A' }}
                                    @else
                                        {{ $mouvement->destinataire ?: 'N/A' }}
                                    @endif
                                </td>
                                <td>{{ $mouvement->user->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection