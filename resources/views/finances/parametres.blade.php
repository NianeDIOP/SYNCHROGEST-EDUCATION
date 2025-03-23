@extends('layouts.app')

@section('title', 'Paramètres Financiers')
@section('page-title', 'Configuration Financière')

@section('content')
<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Paramètres financiers</h1>
</div>

<!-- Alert de confirmation ou d'erreur -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <!-- Gestion des catégories financières -->
    <div class="col-xl-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Catégories financières</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCategorieModal">
                    <i class="fas fa-plus me-1"></i> Ajouter
                </button>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="categoriesTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="recettes-tab" data-bs-toggle="tab" data-bs-target="#recettes" type="button" role="tab" aria-controls="recettes" aria-selected="true">
                            <i class="fas fa-arrow-down text-success me-1"></i> Catégories de recettes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="depenses-tab" data-bs-toggle="tab" data-bs-target="#depenses" type="button" role="tab" aria-controls="depenses" aria-selected="false">
                            <i class="fas fa-arrow-up text-danger me-1"></i> Catégories de dépenses
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="categoriesTabsContent">
                    <!-- Onglet des catégories de recettes -->
                    <div class="tab-pane fade show active" id="recettes" role="tabpanel" aria-labelledby="recettes-tab">
                        @if($categories->where('type', 'recette')->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categories->where('type', 'recette') as $categorie)
                                            <tr>
                                                <td>{{ $categorie->nom }}</td>
                                                <td>{{ $categorie->description ?: 'N/A' }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-warning edit-categorie-btn" data-categorie-id="{{ $categorie->id }}" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-categorie-btn" data-categorie-id="{{ $categorie->id }}" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Aucune catégorie de recette définie. Commencez par en ajouter une.
                            </div>
                        @endif
                    </div>
                    
                    <!-- Onglet des catégories de dépenses -->
                    <div class="tab-pane fade" id="depenses" role="tabpanel" aria-labelledby="depenses-tab">
                        @if($categories->where('type', 'depense')->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categories->where('type', 'depense') as $categorie)
                                            <tr>
                                                <td>{{ $categorie->nom }}</td>
                                                <td>{{ $categorie->description ?: 'N/A' }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-warning edit-categorie-btn" data-categorie-id="{{ $categorie->id }}" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-categorie-btn" data-categorie-id="{{ $categorie->id }}" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Aucune catégorie de dépense définie. Commencez par en ajouter une.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Paramètres généraux finances -->
    <div class="col-xl-6">
        <div class="card shadow mb-4">
        <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Paramètres généraux</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('finances.saveParametres') }}" method="POST" id="parametresFinanceForm">
                    @csrf
                    
                    <div class="mb-4">
                        <h6 class="font-weight-bold">Structure financière</h6>
                        <hr>
                        
                        <div class="mb-3">
                            <label for="devise" class="form-label">Devise</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="devise" name="devise" value="{{ $parametres->devise ?? 'FCFA' }}">
                                <button class="btn btn-outline-secondary" type="button" id="resetDeviseBtn" title="Réinitialiser à FCFA">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">La devise utilisée pour toutes les transactions financières.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="annee_fiscale_debut" class="form-label">Début de l'année fiscale</label>
                            <div class="input-group">
                                <select class="form-select" id="annee_fiscale_debut" name="annee_fiscale_debut">
                                    <option value="1" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 1 ? 'selected' : '' }}>Janvier</option>
                                    <option value="2" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 2 ? 'selected' : '' }}>Février</option>
                                    <option value="3" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 3 ? 'selected' : '' }}>Mars</option>
                                    <option value="4" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 4 ? 'selected' : '' }}>Avril</option>
                                    <option value="5" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 5 ? 'selected' : '' }}>Mai</option>
                                    <option value="6" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 6 ? 'selected' : '' }}>Juin</option>
                                    <option value="7" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 7 ? 'selected' : '' }}>Juillet</option>
                                    <option value="8" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 8 ? 'selected' : '' }}>Août</option>
                                    <option value="9" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 9 ? 'selected' : '' }}>Septembre</option>
                                    <option value="10" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 10 ? 'selected' : '' }}>Octobre</option>
                                    <option value="11" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 11 ? 'selected' : '' }}>Novembre</option>
                                    <option value="12" {{ isset($parametres->annee_fiscale_debut) && $parametres->annee_fiscale_debut == 12 ? 'selected' : '' }}>Décembre</option>
                                </select>
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            </div>
                            <small class="form-text text-muted">Le mois de début de l'année fiscale de votre établissement.</small>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="font-weight-bold">Paiements échelonnés</h6>
                        <hr>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="paiement_echelonne" name="paiement_echelonne" {{ isset($parametres->paiement_echelonne) && $parametres->paiement_echelonne ? 'checked' : '' }}>
                            <label class="form-check-label" for="paiement_echelonne">Activer les paiements échelonnés</label>
                        </div>
                        
                        <div id="echelonnementOptions" class="ps-4 {{ isset($parametres->paiement_echelonne) && $parametres->paiement_echelonne ? '' : 'd-none' }}">
                            <div class="mb-3">
                                <label for="nb_echeances" class="form-label">Nombre d'échéances par défaut</label>
                                <input type="number" class="form-control" id="nb_echeances" name="nb_echeances" min="2" max="12" value="{{ $parametres->nb_echeances ?? 3 }}">
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" id="frais_retard" name="frais_retard" {{ isset($parametres->frais_retard) && $parametres->frais_retard ? 'checked' : '' }}>
                                <label class="form-check-label" for="frais_retard">Appliquer des frais de retard</label>
                            </div>
                            
                            <div id="fraisRetardOptions" class="mb-3 {{ isset($parametres->frais_retard) && $parametres->frais_retard ? '' : 'd-none' }}">
                                <label for="taux_retard" class="form-label">Taux de pénalité (%)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="taux_retard" name="taux_retard" min="0" max="100" step="0.5" value="{{ $parametres->taux_retard ?? 5 }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="font-weight-bold">Rappels & notifications</h6>
                        <hr>
                        
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" id="rappels_auto" name="rappels_auto" {{ isset($parametres->rappels_auto) && $parametres->rappels_auto ? 'checked' : '' }}>
                            <label class="form-check-label" for="rappels_auto">Activer les rappels automatiques</label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="delai_rappel" class="form-label">Délai de rappel avant échéance (jours)</label>
                            <input type="number" class="form-control" id="delai_rappel" name="delai_rappel" min="1" max="30" value="{{ $parametres->delai_rappel ?? 7 }}">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success" id="saveParametresBtn">
                            <i class="fas fa-save me-2"></i> Enregistrer les paramètres
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ajouter/Modifier Catégorie Modal -->
<div class="modal fade" id="addCategorieModal" tabindex="-1" aria-labelledby="addCategorieModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addCategorieModalLabel">Ajouter une catégorie</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('finances.ajouterCategorie') }}" method="POST" id="categorieForm">
                @csrf
                <input type="hidden" name="categorie_id" id="categorie_id" value="">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categorie_type" class="form-label">Type de catégorie <span class="text-danger">*</span></label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="type" id="type_recette" value="recette" checked>
                                <label class="form-check-label" for="type_recette">
                                    Recette
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_depense" value="depense">
                                <label class="form-check-label" for="type_depense">
                                    Dépense
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categorie_nom" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categorie_nom" name="nom" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categorie_description" class="form-label">Description</label>
                        <textarea class="form-control" id="categorie_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="saveCategorieBtn">
                        <i class="fas fa-save me-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation de suppression Modal -->
<div class="modal fade" id="deleteCategorieModal" tabindex="-1" aria-labelledby="deleteCategorieModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCategorieModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                    <div>
                        <p>Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible.</p>
                        <p class="text-danger mb-0">Attention : Toutes les transactions associées à cette catégorie seront également supprimées.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form id="deleteCategorieForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du formulaire de catégorie
        const addCategorieModal = new bootstrap.Modal(document.getElementById('addCategorieModal'));
        const deleteCategorieModal = new bootstrap.Modal(document.getElementById('deleteCategorieModal'));
        
        // Réinitialiser le formulaire lors de l'ouverture du modal
        document.getElementById('addCategorieModal').addEventListener('show.bs.modal', function() {
            document.getElementById('categorieForm').reset();
            document.getElementById('categorie_id').value = '';
            document.getElementById('addCategorieModalLabel').textContent = 'Ajouter une catégorie';
            document.getElementById('categorieForm').action = "{{ route('finances.ajouterCategorie') }}";
        });
        
        // Éditer une catégorie
        document.querySelectorAll('.edit-categorie-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const categorieId = this.getAttribute('data-categorie-id');
                
                // TODO: Remplacer par une requête AJAX réelle
                // Pour l'instant, nous simulons des données
                document.getElementById('categorie_id').value = categorieId;
                document.getElementById('addCategorieModalLabel').textContent = 'Modifier la catégorie';
                document.getElementById('categorieForm').action = "{{ route('finances.ajouterCategorie') }}";
                
                // Exemple - à remplacer par des données réelles
                if (categorieId % 2 === 0) {
                    document.getElementById('type_recette').checked = true;
                    document.getElementById('categorie_nom').value = 'Recette ' + categorieId;
                } else {
                    document.getElementById('type_depense').checked = true;
                    document.getElementById('categorie_nom').value = 'Dépense ' + categorieId;
                }
                document.getElementById('categorie_description').value = 'Description de la catégorie ' + categorieId;
                
                addCategorieModal.show();
            });
        });
        
        // Supprimer une catégorie
        document.querySelectorAll('.delete-categorie-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const categorieId = this.getAttribute('data-categorie-id');
                document.getElementById('deleteCategorieForm').action = `/finances/categories/${categorieId}`;
                deleteCategorieModal.show();
            });
        });
        
        // Gestion de l'échelonnement des paiements
        const paiementEchelonneCheck = document.getElementById('paiement_echelonne');
        const echelonnementOptions = document.getElementById('echelonnementOptions');
        
        paiementEchelonneCheck.addEventListener('change', function() {
            echelonnementOptions.classList.toggle('d-none', !this.checked);
        });
        
        // Gestion des frais de retard
        const fraisRetardCheck = document.getElementById('frais_retard');
        const fraisRetardOptions = document.getElementById('fraisRetardOptions');
        
        fraisRetardCheck.addEventListener('change', function() {
            fraisRetardOptions.classList.toggle('d-none', !this.checked);
        });
        
        // Réinitialiser la devise
        document.getElementById('resetDeviseBtn').addEventListener('click', function() {
            document.getElementById('devise').value = 'FCFA';
        });
        
        // Validation du formulaire des paramètres
        document.getElementById('parametresFinanceForm').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                document.getElementById('saveParametresBtn').disabled = true;
                document.getElementById('saveParametresBtn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...';
            }
            
            this.classList.add('was-validated');
        });
    });
</script>
@endsection