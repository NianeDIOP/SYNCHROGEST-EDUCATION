@extends('layouts.app')

@section('title', 'Niveaux et Classes - Module Inscription')
@section('page-title', 'Configuration des niveaux et classes')

@section('styles')
<style>
    .niveau-card {
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        border: none;
    }
    
    .niveau-card .card-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        font-weight: 700;
        padding: 1rem;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    
    .niveau-card .card-body {
        border: 1px solid #e3e6f0;
        border-top: none;
        border-radius: 0 0 0.5rem 0.5rem;
        padding: 1.5rem;
    }
    
    .classe-item {
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    
    .classe-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.15rem 1.25rem 0 rgba(58, 59, 69, 0.1);
    }
    
    .classe-item .classe-header {
        background-color: #f8f9fc;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem 0.5rem 0 0;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .classe-item .classe-body {
        padding: 1rem;
    }
    
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-icon .fas {
        margin-right: 0.5rem;
    }
    
    .niveau-counter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: var(--primary);
        color: white;
        font-weight: 700;
        margin-right: 0.75rem;
    }
    
    .add-niveau-section {
        border: 2px dashed #e3e6f0;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .add-niveau-section:hover {
        border-color: var(--primary);
        background-color: rgba(78, 115, 223, 0.05);
    }
    
    .drag-placeholder {
        border: 2px dashed #e3e6f0;
        border-radius: 0.5rem;
        background-color: #f8f9fc;
        margin-bottom: 1.5rem;
        height: 60px;
    }
    
    .action-bar {
        position: sticky;
        bottom: 0;
        background-color: white;
        box-shadow: 0 -0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        padding: 1rem;
        margin: 2rem -1.5rem -1.5rem -1.5rem;
        display: flex;
        justify-content: flex-end;
        z-index: 10;
    }
</style>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Structure pédagogique</h1>
    <button type="button" class="btn btn-primary btn-icon" data-bs-toggle="modal" data-bs-target="#addNiveauModal">
        <i class="fas fa-plus"></i> Ajouter un niveau
    </button>
</div>

<!-- Alert for validation errors -->
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-3"></i>
        <div>
            <h5 class="mb-1">Erreur de validation</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<form action="{{ route('inscriptions.niveaux') }}" method="POST" id="niveauxForm">
    @csrf
    
    <!-- Niveaux Container -->
    <div class="row" id="niveauxContainer">
        @forelse($niveaux as $niveauIndex => $niveau)
        <div class="col-lg-6 niveau-block" data-index="{{ $niveauIndex }}">
            <div class="card niveau-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="niveau-counter">{{ $niveauIndex + 1 }}</div>
                        <h5 class="mb-0">{{ $niveau->nom }}</h5>
                    </div>
                    <div>
                        <button type="button" class="btn btn-light btn-sm edit-niveau-btn" data-niveau-id="{{ $niveau->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm remove-niveau-btn" data-niveau-id="{{ $niveau->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="niveaux[{{ $niveauIndex }}][id]" value="{{ $niveau->id }}">
                    
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nom du niveau <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="niveaux[{{ $niveauIndex }}][nom]" value="{{ $niveau->nom }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frais d'inscription <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="niveaux[{{ $niveauIndex }}][frais_inscription]" value="{{ $niveau->frais_inscription }}" min="0" required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frais de scolarité <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="niveaux[{{ $niveauIndex }}][frais_scolarite]" value="{{ $niveau->frais_scolarite }}" min="0" required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input niveau-examen-checkbox" type="checkbox" id="niveau_examen_{{ $niveauIndex }}" name="niveaux[{{ $niveauIndex }}][est_niveau_examen]" value="1" {{ $niveau->est_niveau_examen ? 'checked' : '' }}>
                                <label class="form-check-label" for="niveau_examen_{{ $niveauIndex }}">Niveau d'examen</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="frais-examen-container mb-4" style="{{ $niveau->est_niveau_examen ? '' : 'display: none;' }}">
                        <label class="form-label">Frais d'examen</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="niveaux[{{ $niveauIndex }}][frais_examen]" value="{{ $niveau->frais_examen }}" min="0">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-primary fw-bold mb-0">Classes ({{ $niveau->classes->count() }})</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon add-classe-btn" data-niveau-index="{{ $niveauIndex }}">
                            <i class="fas fa-plus"></i> Ajouter une classe
                        </button>
                    </div>
                    
                    <div class="classes-container">
                        @if($niveau->classes->count() > 0)
                            @foreach($niveau->classes as $classeIndex => $classe)
                            <div class="classe-item" data-classe-id="{{ $classe->id }}">
                                <div class="classe-header">
                                    <h6 class="mb-0">Classe {{ $classeIndex + 1 }}</h6>
                                    <button type="button" class="btn btn-sm btn-danger remove-classe-btn" data-classe-id="{{ $classe->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="classe-body">
                                    <input type="hidden" name="niveaux[{{ $niveauIndex }}][classes][{{ $classeIndex }}][id]" value="{{ $classe->id }}">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="niveaux[{{ $niveauIndex }}][classes][{{ $classeIndex }}][nom]" value="{{ $classe->nom }}" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Capacité <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="niveaux[{{ $niveauIndex }}][classes][{{ $classeIndex }}][capacite]" value="{{ $classe->capacite }}" min="1" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="alert alert-info mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>Aucune classe définie pour ce niveau.</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fa-2x"></i>
                    <div>
                        <h5 class="mb-1">Aucun niveau configuré</h5>
                        <p class="mb-0">Commencez par ajouter un niveau d'étude pour votre établissement.</p>
                    </div>
                </div>
            </div>
        </div>
        @endforelse
        
        <!-- Add Niveau Section -->
        <div class="col-lg-6">
            <div class="add-niveau-section" data-bs-toggle="modal" data-bs-target="#addNiveauModal" role="button">
                <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                <h5>Ajouter un niveau</h5>
                <p class="text-muted mb-0">Cliquez ici pour ajouter un nouveau niveau d'étude</p>
            </div>
        </div>
    </div>
    
    <!-- Action Bar (Fixed at bottom) -->
    <div class="action-bar">
        <button type="submit" class="btn btn-success btn-lg" id="saveBtn">
            <i class="fas fa-save me-2"></i> Enregistrer les modifications
        </button>
    </div>
</form>

<!-- Add Niveau Modal -->
<div class="modal fade" id="addNiveauModal" tabindex="-1" aria-labelledby="addNiveauModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addNiveauModalLabel">Ajouter un niveau</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="nouveauNiveauForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nom du niveau <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nouveau_niveau_nom" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frais d'inscription <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="nouveau_niveau_frais_inscription" min="0" required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frais de scolarité <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="nouveau_niveau_frais_scolarite" min="0" required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="nouveau_niveau_examen">
                                <label class="form-check-label" for="nouveau_niveau_examen">Niveau d'examen</label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="nouveau_frais_examen_container" class="mb-4" style="display: none;">
                        <label class="form-label">Frais d'examen</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="nouveau_niveau_frais_examen" min="0">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ajouter_classes">
                            <label class="form-check-label" for="ajouter_classes">
                                Ajouter des classes à ce niveau
                            </label>
                        </div>
                    </div>
                    
                    <div id="classes_container" class="mt-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-primary fw-bold mb-0">Classes</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-icon" id="addClassFieldBtn">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </div>
                        
                        <div id="class_fields">
                            <div class="card mb-3 class-field-row">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control class-name-field" placeholder="ex: 6ème A">
                                        </div>
                                        <div class="col-md-5 mb-3">
                                            <label class="form-label">Capacité <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control class-capacity-field" value="50" min="1">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end mb-3">
                                            <button type="button" class="btn btn-sm btn-danger remove-class-field-btn">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary btn-icon" id="ajouterNiveauBtn">
                    <i class="fas fa-save me-2"></i> Ajouter ce niveau
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Niveau Confirmation Modal -->
<div class="modal fade" id="deleteNiveauModal" tabindex="-1" aria-labelledby="deleteNiveauModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteNiveauModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                    <h5 class="mb-0">Êtes-vous sûr de vouloir supprimer ce niveau ?</h5>
                </div>
                <p>Cette action entraînera la suppression de toutes les classes associées à ce niveau. Cette opération est irréversible.</p>
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>Attention: Les élèves associés à ce niveau devront être réaffectés.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger btn-icon" id="confirmDeleteNiveauBtn">
                    <i class="fas fa-trash me-2"></i> Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let niveauCounter = {{ count($niveaux) }};
        let niveauToDelete = null;
        
        // Initialize modals
        const addNiveauModal = new bootstrap.Modal(document.getElementById('addNiveauModal'));
        const deleteNiveauModal = new bootstrap.Modal(document.getElementById('deleteNiveauModal'));
        
        // Initialize sortable for drag & drop reordering of niveaux
        new Sortable(document.getElementById('niveauxContainer'), {
            animation: 150,
            handle: '.card-header',
            ghostClass: 'drag-placeholder',
            onEnd: function() {
                updateNiveauCounters();
            },
            filter: '.add-niveau-section', // Don't allow dragging the "Add Niveau" button
            preventOnFilter: false
        });
        
        // Function to update niveau counters after drag & drop
        function updateNiveauCounters() {
            document.querySelectorAll('.niveau-counter').forEach((counter, index) => {
                counter.textContent = index + 1;
            });
        }
        
        // Handle niveau d'examen checkbox
        document.querySelectorAll('.niveau-examen-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const container = this.closest('.card-body').querySelector('.frais-examen-container');
                container.style.display = this.checked ? 'block' : 'none';
            });
        });
        
        // Handle new niveau d'examen checkbox
        document.getElementById('nouveau_niveau_examen').addEventListener('change', function() {
            const container = document.getElementById('nouveau_frais_examen_container');
            container.style.display = this.checked ? 'block' : 'none';
        });
        
        // Handle add classes checkbox
        document.getElementById('ajouter_classes').addEventListener('change', function() {
            const container = document.getElementById('classes_container');
            container.style.display = this.checked ? 'block' : 'none';
        });
        
        // Add class field
        document.getElementById('addClassFieldBtn').addEventListener('click', function() {
            addClassField();
        });
        
        // Handle remove class field buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-class-field-btn')) {
                const row = e.target.closest('.class-field-row');
                
                // Don't remove if it's the last row
                if (document.querySelectorAll('.class-field-row').length > 1) {
                    row.remove();
                }
            }
        });
        
        // Function to add class field
        function addClassField() {
            const classeFields = document.getElementById('class_fields');
            const newFieldHtml = `
                <div class="card mb-3 class-field-row">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                                <input type="text" class="form-control class-name-field" placeholder="ex: 6ème A">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Capacité <span class="text-danger">*</span></label>
                                <input type="number" class="form-control class-capacity-field" value="50" min="1">
                            </div>
                            <div class="col-md-1 d-flex align-items-end mb-3">
                                <button type="button" class="btn btn-sm btn-danger remove-class-field-btn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            classeFields.insertAdjacentHTML('beforeend', newFieldHtml);
        }
        
        // Add new niveau
        document.getElementById('ajouterNiveauBtn').addEventListener('click', function() {
            const nom = document.getElementById('nouveau_niveau_nom').value;
            const fraisInscription = document.getElementById('nouveau_niveau_frais_inscription').value;
            const fraisScolarite = document.getElementById('nouveau_niveau_frais_scolarite').value;
            const estNiveauExamen = document.getElementById('nouveau_niveau_examen').checked;
            const fraisExamen = document.getElementById('nouveau_niveau_frais_examen').value || 0;
            const ajouterClasses = document.getElementById('ajouter_classes').checked;
            
            // Validation
            if (!nom || !fraisInscription || !fraisScolarite) {
                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }
            
            // Get classes if enabled
            let classes = [];
            if (ajouterClasses) {
                const classNameFields = document.querySelectorAll('.class-name-field');
                const classCapacityFields = document.querySelectorAll('.class-capacity-field');
                
                for (let i = 0; i < classNameFields.length; i++) {
                    if (classNameFields[i].value.trim() !== '') {
                        classes.push({
                            nom: classNameFields[i].value,
                            capacite: classCapacityFields[i].value || 50
                        });
                    }
                }
            }
            
            // Create HTML for new niveau
            const niveauHtml = `
                <div class="col-lg-6 niveau-block" data-index="${niveauCounter}">
                    <div class="card niveau-card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="niveau-counter">${niveauCounter + 1}</div>
                                <h5 class="mb-0">${nom}</h5>
                            </div>
                            <div>
                                <button type="button" class="btn btn-light btn-sm edit-niveau-btn" data-niveau-id="">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm remove-niveau-btn" data-niveau-id="">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="niveaux[${niveauCounter}][id]" value="">
                            
                            <div class="row mb-4">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nom du niveau <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="niveaux[${niveauCounter}][nom]" value="${nom}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Frais d'inscription <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="niveaux[${niveauCounter}][frais_inscription]" value="${fraisInscription}" min="0" required>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Frais de scolarité <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="niveaux[${niveauCounter}][frais_scolarite]" value="${fraisScolarite}" min="0" required>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input niveau-examen-checkbox" type="checkbox" id="niveau_examen_${niveauCounter}" name="niveaux[${niveauCounter}][est_niveau_examen]" value="1" ${estNiveauExamen ? 'checked' : ''}>
                                        <label class="form-check-label" for="niveau_examen_${niveauCounter}">Niveau d'examen</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="frais-examen-container mb-4" style="${estNiveauExamen ? '' : 'display: none;'}">
                                <label class="form-label">Frais d'examen</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="niveaux[${niveauCounter}][frais_examen]" value="${fraisExamen}" min="0">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary fw-bold mb-0">Classes (${classes.length})</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-icon add-classe-btn" data-niveau-index="${niveauCounter}">
                                    <i class="fas fa-plus"></i> Ajouter une classe
                                </button>
                            </div>
                            
                            <div class="classes-container">
                                ${classes.length > 0 ? '' : `
                                    <div class="alert alert-info mb-0">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <div>Aucune classe définie pour ce niveau.</div>
                                        </div>
                                    </div>
                                `}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add niveau to container
            const addNiveauSection = document.querySelector('.add-niveau-section').closest('.col-lg-6');
            addNiveauSection.insertAdjacentHTML('beforebegin', niveauHtml);
            
            // Add classes if any
            if (classes.length > 0) {
                const classesContainer = document.querySelector(`.niveau-block[data-index="${niveauCounter}"] .classes-container`);
                classesContainer.innerHTML = '';
                
                classes.forEach((classe, classeIndex) => {
                    const classeHtml = `
                        <div class="classe-item" data-classe-id="">
                            <div class="classe-header">
                                <h6 class="mb-0">Classe ${classeIndex + 1}</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-classe-btn" data-classe-id="">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="classe-body">
                                <input type="hidden" name="niveaux[${niveauCounter}][classes][${classeIndex}][id]" value="">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="niveaux[${niveauCounter}][classes][${classeIndex}][nom]" value="${classe.nom}" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Capacité <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="niveaux[${niveauCounter}][classes][${classeIndex}][capacite]" value="${classe.capacite}" min="1" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    classesContainer.insertAdjacentHTML('beforeend', classeHtml);
                });
            }
            
            // Update UI for empty state
            const alertInfo = document.querySelector('.alert-info');
            if (alertInfo && alertInfo.textContent.includes('Aucun niveau configuré')) {
                alertInfo.remove();
            }
            
            // Add event listeners to the new niveau
            const newNiveau = document.querySelector(`.niveau-block[data-index="${niveauCounter}"]`);
            
            // Niveau d'examen checkbox
            const examenCheckbox = newNiveau.querySelector('.niveau-examen-checkbox');
            examenCheckbox.addEventListener('change', function() {
                const container = this.closest('.card-body').querySelector('.frais-examen-container');
                container.style.display = this.checked ? 'block' : 'none';
            });
            
            // Add class button
            const addClasseBtn = newNiveau.querySelector('.add-classe-btn');
            addClasseBtn.addEventListener('click', function() {
                addClasse(this);
            });
            
            // Remove niveau button
            const removeNiveauBtn = newNiveau.querySelector('.remove-niveau-btn');
            removeNiveauBtn.addEventListener('click', function() {
                niveauToDelete = newNiveau;
                deleteNiveauModal.show();
            });
            
            // Increment niveau counter
            niveauCounter++;
            
            // Update niveau counters
            updateNiveauCounters();
            
            // Reset form and close modal
            document.getElementById('nouveauNiveauForm').reset();
            document.getElementById('nouveau_frais_examen_container').style.display = 'none';
            document.getElementById('classes_container').style.display = 'none';
            document.getElementById('class_fields').innerHTML = `
                <div class="card mb-3 class-field-row">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                                <input type="text" class="form-control class-name-field" placeholder="ex: 6ème A">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Capacité <span class="text-danger">*</span></label>
                                <input type="number" class="form-control class-capacity-field" value="50" min="1">
                            </div>
                            <div class="col-md-1 d-flex align-items-end mb-3">
                                <button type="button" class="btn btn-sm btn-danger remove-class-field-btn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            addNiveauModal.hide();
            
            // Show success toast
            showToastNotification('Niveau ajouté avec succès. N\'oubliez pas d\'enregistrer les changements.', 'success');
        });
        
        // Add classe to niveau
        document.querySelectorAll('.add-classe-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                addClasse(this);
            });
        });
        
        // Function to add classe
        function addClasse(button) {
            const niveauIndex = button.getAttribute('data-niveau-index');
            const classesContainer = button.closest('.card-body').querySelector('.classes-container');
            const classeCount = classesContainer.querySelectorAll('.classe-item').length;
            
            // Remove "no classes" alert if it exists
            const alertInfo = classesContainer.querySelector('.alert-info');
            if (alertInfo) {
                alertInfo.remove();
            }
            
            const classeHtml = `
                <div class="classe-item" data-classe-id="">
                    <div class="classe-header">
                        <h6 class="mb-0">Classe ${classeCount + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-classe-btn" data-classe-id="">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="classe-body">
                        <input type="hidden" name="niveaux[${niveauIndex}][classes][${classeCount}][id]" value="">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="niveaux[${niveauIndex}][classes][${classeCount}][nom]" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Capacité <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="niveaux[${niveauIndex}][classes][${classeCount}][capacite]" value="50" min="1" required>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            classesContainer.insertAdjacentHTML('beforeend', classeHtml);
            
            // Add event listener to the new remove button
            const removeBtn = classesContainer.lastElementChild.querySelector('.remove-classe-btn');
            removeBtn.addEventListener('click', function() {
                removeClasse(this);
            });
            
            // Update classes count in header
            updateClassesCount(button.closest('.card-body'));
        }
        
        // Function to remove a classe
        function removeClasse(button) {
            const confirmed = confirm('Êtes-vous sûr de vouloir supprimer cette classe ?');
            if (!confirmed) return;
            
            const classeItem = button.closest('.classe-item');
            const classesContainer = classeItem.closest('.classes-container');
            const cardBody = classesContainer.closest('.card-body');
            
            classeItem.remove();
            
            // Update classes numbering
            classesContainer.querySelectorAll('.classe-item').forEach((item, index) => {
                item.querySelector('h6').textContent = `Classe ${index + 1}`;
            });
            
            // Add "no classes" alert if no classes left
            if (classesContainer.querySelectorAll('.classe-item').length === 0) {
                classesContainer.innerHTML = `
                    <div class="alert alert-info mb-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>Aucune classe définie pour ce niveau.</div>
                        </div>
                    </div>
                `;
            }
            
            // Update classes count in header
            updateClassesCount(cardBody);
        }
        
        // Function to update classes count in header
        function updateClassesCount(cardBody) {
            const classesCount = cardBody.querySelectorAll('.classe-item').length;
            const classesHeader = cardBody.querySelector('h6.text-primary');
            classesHeader.textContent = `Classes (${classesCount})`;
        }
        
        // Add click event to existing remove classe buttons
        document.querySelectorAll('.remove-classe-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                removeClasse(this);
            });
        });
        
        // Show delete confirmation modal
        document.querySelectorAll('.remove-niveau-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                niveauToDelete = this.closest('.niveau-block');
                deleteNiveauModal.show();
            });
        });
        
        // Confirm delete niveau
        document.getElementById('confirmDeleteNiveauBtn').addEventListener('click', function() {
            if (niveauToDelete) {
                niveauToDelete.remove();
                deleteNiveauModal.hide();
                updateNiveauCounters();
                showToastNotification('Niveau supprimé avec succès. N\'oubliez pas d\'enregistrer les changements.', 'success');
            }
        });
        
        // Form submission
        document.getElementById('niveauxForm').addEventListener('submit', function(event) {
            // Check if at least one niveau exists
            if (document.querySelectorAll('.niveau-block').length === 0) {
                event.preventDefault();
                showToastNotification('Veuillez ajouter au moins un niveau avant d\'enregistrer.', 'error');
                return false;
            }
            
            // Check if all required fields are filled
            let isValid = true;
            
            // Validate niveau names
            document.querySelectorAll('input[name$="[nom]"]').forEach(input => {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            // Validate fees
            document.querySelectorAll('input[name$="[frais_inscription]"], input[name$="[frais_scolarite]"]').forEach(input => {
                if (input.hasAttribute('required') && (isNaN(parseFloat(input.value)) || parseFloat(input.value) < 0)) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                showToastNotification('Veuillez corriger les erreurs dans le formulaire.', 'error');
                return false;
            }
            
            // Disable save button and show loader
            document.getElementById('saveBtn').disabled = true;
            document.getElementById('saveBtn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement en cours...';
        });
        
        // Toast notification function
        function showToastNotification(message, type) {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            
            // Create toast element
            const toastId = 'toast-' + Date.now();
            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            // Initialize and show toast
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
            toast.show();
            
            // Remove toast after it's hidden
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
    });
</script>
@endsection