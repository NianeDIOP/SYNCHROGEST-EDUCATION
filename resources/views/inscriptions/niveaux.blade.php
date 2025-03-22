@extends('layouts.app')

@section('title', 'Niveaux et Classes - Module Inscription')
@section('page-title', 'Configuration des niveaux et classes')

@section('styles')
<style>
    .niveau-card {
        transition: all 0.3s ease;
    }
    
    .niveau-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .niveau-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border-radius: calc(var(--border-radius) - 1px) calc(var(--border-radius) - 1px) 0 0;
    }
    
    .class-item {
        transition: all 0.2s ease;
    }
    
    .class-item:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }
    
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
    }
    
    .form-switch .form-check-input:checked {
        background-color: var(--success);
        border-color: var(--success);
    }
    
    .add-class-btn {
        transition: all 0.2s ease;
    }
    
    .add-class-btn:hover {
        transform: translateY(-2px);
    }
    
    .class-counter {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: var(--primary);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Niveaux et Classes</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNiveauModal">
        <i class="material-icons-round">add</i> Ajouter un niveau
    </button>
</div>

<!-- Alert for validation errors -->
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="material-icons-round me-2">error</i>
        <div>
            <strong>Erreur de validation</strong>
            <ul class="mb-0 mt-1">
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
    
    <div class="row" id="niveauxContainer">
        @foreach($niveaux as $niveauIndex => $niveau)
        <div class="col-lg-6 mb-4 niveau-block" data-index="{{ $niveauIndex }}">
            <div class="card shadow niveau-card border-0">
                <div class="card-header niveau-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">{{ $niveau->nom }}</h6>
                    <div>
                        <button type="button" class="btn btn-light btn-sm edit-niveau-btn" data-niveau-id="{{ $niveau->id }}">
                            <i class="material-icons-round">edit</i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm remove-niveau-btn" data-niveau-id="{{ $niveau->id }}">
                            <i class="material-icons-round">delete</i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="niveaux[{{ $niveauIndex }}][id]" value="{{ $niveau->id }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
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
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
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
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-primary font-weight-bold mb-0">Classes</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill add-classe-btn" data-niveau-index="{{ $niveauIndex }}">
                            <i class="material-icons-round">add</i> Ajouter une classe
                        </button>
                    </div>
                    
                    <div class="classes-container">
                        @if($niveau->classes->count() > 0)
                            @foreach($niveau->classes as $classeIndex => $classe)
                            <div class="card class-item mb-2 border" data-classe-id="{{ $classe->id }}">
                                <div class="card-body py-2 px-3">
                                    <div class="row align-items-center">
                                        <input type="hidden" name="niveaux[{{ $niveauIndex }}][classes][{{ $classeIndex }}][id]" value="{{ $classe->id }}">
                                        
                                        <div class="col-md-5">
                                            <div class="form-group mb-0">
                                                <label class="form-label small">Nom de la classe</label>
                                                <input type="text" class="form-control form-control-sm" name="niveaux[{{ $niveauIndex }}][classes][{{ $classeIndex }}][nom]" value="{{ $classe->nom }}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-5">
                                            <div class="form-group mb-0">
                                                <label class="form-label small">Capacité</label>
                                                <input type="number" class="form-control form-control-sm" name="niveaux[{{ $niveauIndex }}][classes][{{ $classeIndex }}][capacite]" value="{{ $classe->capacite }}" min="1" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2 text-end">
                                            <button type="button" class="btn btn-sm btn-danger remove-classe-btn" data-classe-id="{{ $classe->id }}">
                                                <i class="material-icons-round">delete</i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="class-counter">{{ $classeIndex + 1 }}</div>
                            </div>
                            @endforeach
                        @else
                            <div class="alert alert-info mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="material-icons-round me-2">info</i>
                                    <div>Aucune classe définie pour ce niveau.</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body text-center py-5">
            <button type="submit" class="btn btn-success btn-lg" id="saveBtn">
                <i class="material-icons-round">save</i> Enregistrer les niveaux et classes
            </button>
        </div>
    </div>
</form>

<!-- Add Niveau Modal -->
<div class="modal fade" id="addNiveauModal" tabindex="-1" aria-labelledby="addNiveauModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header niveau-header">
                <h5 class="modal-title" id="addNiveauModalLabel">Ajouter un niveau</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="nouveauNiveauForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
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
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
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
                    
                    <div class="mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ajouter_classes">
                            <label class="form-check-label" for="ajouter_classes">
                                Ajouter des classes à ce niveau
                            </label>
                        </div>
                    </div>
                    
                    <div id="classes_container" class="mt-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="font-weight-bold mb-0">Classes</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addClassFieldBtn">
                                <i class="material-icons-round">add</i> Ajouter
                            </button>
                        </div>
                        
                        <div id="class_fields">
                            <div class="row mb-2 class-field-row">
                                <div class="col-md-6">
                                    <label class="form-label small">Nom de la classe</label>
                                    <input type="text" class="form-control form-control-sm class-name-field" placeholder="ex: 6ème A">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small">Capacité</label>
                                    <input type="number" class="form-control form-control-sm class-capacity-field" value="50" min="1">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-class-field-btn">
                                        <i class="material-icons-round">close</i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="ajouterNiveauBtn">
                    <i class="material-icons-round">save</i> Ajouter ce niveau
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
                <p>Êtes-vous sûr de vouloir supprimer ce niveau et toutes ses classes associées ? Cette action est irréversible.</p>
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <i class="material-icons-round me-2">warning</i>
                        <div>Attention: Tous les élèves associés à ce niveau devront être réaffectés.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteNiveauBtn">
                    <i class="material-icons-round">delete</i> Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let niveauCounter = {{ count($niveaux) }};
        let niveauToDelete = null;
        
        // Initialize modals
        const addNiveauModal = new bootstrap.Modal(document.getElementById('addNiveauModal'));
        const deleteNiveauModal = new bootstrap.Modal(document.getElementById('deleteNiveauModal'));
        
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
            if (e.target.classList.contains('remove-class-field-btn') || e.target.closest('.remove-class-field-btn')) {
                const btn = e.target.classList.contains('remove-class-field-btn') ? e.target : e.target.closest('.remove-class-field-btn');
                const row = btn.closest('.class-field-row');
                
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
                <div class="row mb-2 class-field-row">
                    <div class="col-md-6">
                        <label class="form-label small">Nom de la classe</label>
                        <input type="text" class="form-control form-control-sm class-name-field" placeholder="ex: 6ème A">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small">Capacité</label>
                        <input type="number" class="form-control form-control-sm class-capacity-field" value="50" min="1">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-danger remove-class-field-btn">
                            <i class="material-icons-round">close</i>
                        </button>
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
                window.showToast('Veuillez remplir tous les champs obligatoires.', 'error');
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
                <div class="col-lg-6 mb-4 niveau-block" data-index="${niveauCounter}">
                    <div class="card shadow niveau-card border-0">
                        <div class="card-header niveau-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold">${nom}</h6>
                            <div>
                                <button type="button" class="btn btn-light btn-sm edit-niveau-btn" data-niveau-id="">
                                    <i class="material-icons-round">edit</i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm remove-niveau-btn" data-niveau-id="">
                                    <i class="material-icons-round">delete</i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="niveaux[${niveauCounter}][id]" value="">
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
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
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch mt-4">
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
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary font-weight-bold mb-0">Classes</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill add-classe-btn" data-niveau-index="${niveauCounter}">
                                    <i class="material-icons-round">add</i> Ajouter une classe
                                </button>
                            </div>
                            
                            <div class="classes-container">
                                ${classes.length > 0 ? '' : `
                                    <div class="alert alert-info mb-0">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons-round me-2">info</i>
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
            document.getElementById('niveauxContainer').insertAdjacentHTML('beforeend', niveauHtml);
            
            // Add classes if any
            if (classes.length > 0) {
                const classesContainer = document.querySelector(`.niveau-block[data-index="${niveauCounter}"] .classes-container`);
                classesContainer.innerHTML = '';
                
                classes.forEach((classe, classeIndex) => {
                    const classeHtml = `
                        <div class="card class-item mb-2 border" data-classe-id="">
                            <div class="card-body py-2 px-3">
                                <div class="row align-items-center">
                                    <input type="hidden" name="niveaux[${niveauCounter}][classes][${classeIndex}][id]" value="">
                                    
                                    <div class="col-md-5">
                                        <div class="form-group mb-0">
                                            <label class="form-label small">Nom de la classe</label>
                                            <input type="text" class="form-control form-control-sm" name="niveaux[${niveauCounter}][classes][${classeIndex}][nom]" value="${classe.nom}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-5">
                                        <div class="form-group mb-0">
                                            <label class="form-label small">Capacité</label>
                                            <input type="number" class="form-control form-control-sm" name="niveaux[${niveauCounter}][classes][${classeIndex}][capacite]" value="${classe.capacite}" min="1" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-sm btn-danger remove-classe-btn" data-classe-id="">
                                            <i class="material-icons-round">delete</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="class-counter">${classeIndex + 1}</div>
                        </div>
                    `;
                    
                    classesContainer.insertAdjacentHTML('beforeend', classeHtml);
                });
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
            
            // Reset form and close modal
            document.getElementById('nouveauNiveauForm').reset();
            document.getElementById('nouveau_frais_examen_container').style.display = 'none';
            document.getElementById('classes_container').style.display = 'none';
            document.getElementById('class_fields').innerHTML = `
                <div class="row mb-2 class-field-row">
                    <div class="col-md-6">
                        <label class="form-label small">Nom de la classe</label>
                        <input type="text" class="form-control form-control-sm class-name-field" placeholder="ex: 6ème A">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small">Capacité</label>
                        <input type="number" class="form-control form-control-sm class-capacity-field" value="50" min="1">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-danger remove-class-field-btn">
                            <i class="material-icons-round">close</i>
                        </button>
                    </div>
                </div>
            `;
            addNiveauModal.hide();
            
            // Show success toast
            window.showToast('Niveau ajouté avec succès. N\'oubliez pas d\'enregistrer les changements.', 'success');
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
            const classeCount = classesContainer.querySelectorAll('.class-item').length;
            
            // Remove "no classes" alert if it exists
            const alertInfo = classesContainer.querySelector('.alert-info');
            if (alertInfo) {
                alertInfo.remove();
            }
            
            const classeHtml = `
                <div class="card class-item mb-2 border" data-classe-id="">
                    <div class="card-body py-2 px-3">
                        <div class="row align-items-center">
                            <input type="hidden" name="niveaux[${niveauIndex}][classes][${classeCount}][id]" value="">
                            
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label class="form-label small">Nom de la classe</label>
                                    <input type="text" class="form-control form-control-sm" name="niveaux[${niveauIndex}][classes][${classeCount}][nom]" required>
                                </div>
                            </div>
                            
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label class="form-label small">Capacité</label>
                                    <input type="number" class="form-control form-control-sm" name="niveaux[${niveauIndex}][classes][${classeCount}][capacite]" value="50" min="1" required>
                                </div>
                            </div>
                            
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-sm btn-danger remove-classe-btn" data-classe-id="">
                                    <i class="material-icons-round">delete</i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="class-counter">${classeCount + 1}</div>
                </div>
            `;
            
            classesContainer.insertAdjacentHTML('beforeend', classeHtml);
            
            // Add event listener to the new remove button
            const removeBtn = classesContainer.lastElementChild.querySelector('.remove-classe-btn');
            removeBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')) {
                    this.closest('.class-item').remove();
                    
                    // Update class counters
                    updateClassCounters(classesContainer);
                    
                    // Add "no classes" alert if no classes left
                    if (classesContainer.querySelectorAll('.class-item').length === 0) {
                        classesContainer.innerHTML = `
                            <div class="alert alert-info mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="material-icons-round me-2">info</i>
                                    <div>Aucune classe définie pour ce niveau.</div>
                                </div>
                            </div>
                        `;
                    }
                }
            });
        }
        
        // Function to update class counters
        function updateClassCounters(container) {
            const classes = container.querySelectorAll('.class-item');
            classes.forEach((classe, index) => {
                classe.querySelector('.class-counter').textContent = index + 1;
            });
        }
        
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
                window.showToast('Niveau supprimé avec succès. N\'oubliez pas d\'enregistrer les changements.', 'success');
            }
        });
        
        // Remove classe
        document.querySelectorAll('.remove-classe-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')) {
                    const classItem = this.closest('.class-item');
                    const classesContainer = classItem.closest('.classes-container');
                    
                    classItem.remove();
                    
                    // Update class counters
                    updateClassCounters(classesContainer);
                    
                    // Add "no classes" alert if no classes left
                    if (classesContainer.querySelectorAll('.class-item').length === 0) {
                        classesContainer.innerHTML = `
                            <div class="alert alert-info mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="material-icons-round me-2">info</i>
                                    <div>Aucune classe définie pour ce niveau.</div>
                                </div>
                            </div>
                        `;
                    }
                }
            });
        });
        
        // Form submission
        document.getElementById('niveauxForm').addEventListener('submit', function(event) {
            // Check if at least one niveau exists
            if (document.querySelectorAll('.niveau-block').length === 0) {
                event.preventDefault();
                window.showToast('Veuillez ajouter au moins un niveau avant d\'enregistrer.', 'error');
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
                window.showToast('Veuillez corriger les erreurs dans le formulaire.', 'error');
                return false;
            }
            
            // Disable save button and show loader
            document.getElementById('saveBtn').disabled = true;
            document.getElementById('saveBtn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement en cours...';
        });
    });
</script>
@endsection