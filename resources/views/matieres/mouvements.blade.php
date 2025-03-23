@extends('layouts.app')

@section('title', 'Mouvements de Stock')
@section('page-title', 'Gestion des Mouvements de Stock')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Historique des mouvements</h6>
        <a href="{{ route('matieres.nouveauMouvement') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> Nouveau mouvement
        </a>
    </div>
    <div class="card-body">
        <!-- Filtres de recherche -->
        <form action="{{ route('matieres.mouvements') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="article_id" class="form-label">Article</label>
                    <select class="form-select" id="article_id" name="article_id">
                        <option value="">Tous les articles</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}" {{ request('article_id') == $article->id ? 'selected' : '' }}>
                                {{ $article->designation }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="type_mouvement" class="form-label">Type</label>
                    <select class="form-select" id="type_mouvement" name="type_mouvement">
                        <option value="">Tous les types</option>
                        <option value="entrée" {{ request('type_mouvement') == 'entrée' ? 'selected' : '' }}>Entrées</option>
                        <option value="sortie" {{ request('type_mouvement') == 'sortie' ? 'selected' : '' }}>Sorties</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="date_debut" class="form-label">Date début</label>
                    <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="date_fin" class="form-label">Date fin</label>
                    <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                </div>
                
                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tableau des mouvements -->
        @if($mouvements->isEmpty())
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fa-2x"></i>
                    <div>
                        <h5 class="mb-1">Aucun mouvement trouvé</h5>
                        <p class="mb-0">Veuillez ajuster vos critères de recherche ou enregistrer de nouveaux mouvements.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Article</th>
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
                                <td>{{ $mouvement->article->designation }}</td>
                                <td>
                                    <span class="badge bg-{{ $mouvement->type_mouvement == 'entrée' ? 'success' : 'danger' }} rounded-pill">
                                        {{ ucfirst($mouvement->type_mouvement) }}
                                    </span>
                                </td>
                                <td>{{ $mouvement->quantite }} {{ $mouvement->article->unite_mesure }}</td>
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
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $mouvements->firstItem() ?? 0 }} à {{ $mouvements->lastItem() ?? 0 }} sur {{ $mouvements->total() }} mouvements
                </div>
                {{ $mouvements->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection