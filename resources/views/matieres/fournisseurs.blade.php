@extends('layouts.app')

@section('title', 'Gestion des Fournisseurs')
@section('page-title', 'Gestion des Fournisseurs')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Liste des fournisseurs</h6>
        <a href="{{ route('matieres.nouveauFournisseur') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> Nouveau fournisseur
        </a>
    </div>
    <div class="card-body">
        <!-- Filtres de recherche -->
        <form action="{{ route('matieres.fournisseurs') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="search" class="form-label">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nom, email, téléphone...">
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actifs</option>
                        <option value="inactif" {{ request('status') == 'inactif' ? 'selected' : '' }}>Inactifs</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('matieres.fournisseurs') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        <!-- Tableau des fournisseurs -->
        @if($fournisseurs->isEmpty())
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fa-2x"></i>
                    <div>
                        <h5 class="mb-1">Aucun fournisseur trouvé</h5>
                        <p class="mb-0">Veuillez ajuster vos critères de recherche ou ajouter de nouveaux fournisseurs.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Personne de contact</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fournisseurs as $fournisseur)
                            <tr class="{{ $fournisseur->est_actif ? '' : 'table-secondary' }}">
                                <td>{{ $fournisseur->nom }}</td>
                                <td>{{ $fournisseur->telephone ?: 'N/A' }}</td>
                                <td>{{ $fournisseur->email ?: 'N/A' }}</td>
                                <td>{{ $fournisseur->personne_contact ?: 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $fournisseur->est_actif ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                        {{ $fournisseur->est_actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('matieres.editFournisseur', $fournisseur->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-{{ $fournisseur->est_actif ? 'secondary' : 'success' }} toggle-status-btn" 
                                            data-id="{{ $fournisseur->id }}" 
                                            data-status="{{ $fournisseur->est_actif ? 'actif' : 'inactif' }}" 
                                            title="{{ $fournisseur->est_actif ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas fa-{{ $fournisseur->est_actif ? 'ban' : 'check' }}"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $fournisseurs->firstItem() ?? 0 }} à {{ $fournisseurs->lastItem() ?? 0 }} sur {{ $fournisseurs->total() }} fournisseurs
                </div>
                {{ $fournisseurs->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Confirmation changement de statut -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalTitle">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="statusModalBody">
                Êtes-vous sûr de vouloir changer le statut de ce fournisseur ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="est_actif" id="statusValue">
                    <button type="submit" class="btn btn-primary">Confirmer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        const statusForm = document.getElementById('statusForm');
        const statusValue = document.getElementById('statusValue');
        const statusModalTitle = document.getElementById('statusModalTitle');
        const statusModalBody = document.getElementById('statusModalBody');
        
        // Gestion du changement de statut
        document.querySelectorAll('.toggle-status-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const currentStatus = this.dataset.status;
                const newStatus = currentStatus === 'actif' ? false : true;
                
                statusForm.action = `{{ url('matieres/fournisseurs') }}/${id}`;
                statusValue.value = newStatus ? 1 : 0;
                
                if (currentStatus === 'actif') {
                    statusModalTitle.textContent = 'Désactiver le fournisseur';
                    statusModalBody.textContent = 'Êtes-vous sûr de vouloir désactiver ce fournisseur ? Il ne sera plus disponible pour les nouveaux mouvements.';
                } else {
                    statusModalTitle.textContent = 'Activer le fournisseur';
                    statusModalBody.textContent = 'Êtes-vous sûr de vouloir activer ce fournisseur ?';
                }
                
                statusModal.show();
            });
        });
    });
</script>
@endsection