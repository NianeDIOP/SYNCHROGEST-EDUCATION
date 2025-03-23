@extends('layouts.app')

@section('title', 'Gestion des Catégories')
@section('page-title', 'Gestion des Catégories d\'Articles')

@section('content')
<div class="row">
    <!-- Liste des catégories -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Liste des catégories</h6>
                <a href="{{ route('matieres.nouvelleCategorie') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i> Nouvelle catégorie
                </a>
            </div>
            <div class="card-body">
                @if($categories->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucune catégorie d'article enregistrée.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Nombre d'articles</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $categorie)
                                    <tr>
                                        <td>{{ $categorie->nom }}</td>
                                        <td>{{ $categorie->description ?: 'N/A' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill">{{ $categorie->articles_count }}</span>
                                            @if($categorie->articles_count > 0)
                                                <a href="{{ route('matieres.articles', ['categorie_id' => $categorie->id]) }}" class="btn btn-sm btn-link">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-warning edit-category-btn" 
                                                    data-id="{{ $categorie->id }}"
                                                    data-nom="{{ $categorie->nom }}"
                                                    data-description="{{ $categorie->description }}"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            @if($categorie->articles_count == 0)
                                                <button type="button" class="btn btn-sm btn-danger delete-category-btn" 
                                                        data-id="{{ $categorie->id }}"
                                                        data-nom="{{ $categorie->nom }}"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Formulaire d'ajout/modification -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary" id="form-title">Ajouter une catégorie</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('matieres.enregistrerCategorie') }}" method="POST" id="category-form">
                    @csrf
                    <input type="hidden" name="id" id="categorie_id">
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="reset-btn">
                            <i class="fas fa-redo me-1"></i> Réinitialiser
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> <span id="submit-text">Enregistrer</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong id="category-name"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteForm = document.getElementById('delete-form');
        const categoryForm = document.getElementById('category-form');
        const formTitle = document.getElementById('form-title');
        const submitText = document.getElementById('submit-text');
        const resetBtn = document.getElementById('reset-btn');
        
        // Gestion des boutons de suppression
        document.querySelectorAll('.delete-category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const categoryId = this.dataset.id;
                const categoryName = this.dataset.nom;
                
                document.getElementById('category-name').textContent = categoryName;
                deleteForm.action = `{{ url('matieres/categories') }}/${categoryId}`;
                
                deleteModal.show();
            });
        });
        
        // Gestion des boutons de modification
        document.querySelectorAll('.edit-category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const categoryId = this.dataset.id;
                const categoryName = this.dataset.nom;
                const categoryDescription = this.dataset.description;
                
                document.getElementById('categorie_id').value = categoryId;
                document.getElementById('nom').value = categoryName;
                document.getElementById('description').value = categoryDescription;
                
                formTitle.textContent = 'Modifier une catégorie';
                submitText.textContent = 'Mettre à jour';
                
                // Faire défiler vers le formulaire
                document.getElementById('category-form').scrollIntoView({ behavior: 'smooth' });
            });
        });
        
        // Réinitialisation du formulaire
        resetBtn.addEventListener('click', function() {
            categoryForm.reset();
            document.getElementById('categorie_id').value = '';
            formTitle.textContent = 'Ajouter une catégorie';
            submitText.textContent = 'Enregistrer';
        });
    });
</script>
@endsection