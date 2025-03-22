@extends('layouts.app')

@section('title', 'Niveaux et Classes - Module Inscription')
@section('page-title', 'Configuration des niveaux et classes')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="card-title">Niveaux et Classes</h2>
            <button type="button" id="addNiveauBtn" class="btn btn-primary">
                <span class="material-icons">add</span> Ajouter un niveau
            </button>
        </div>
        
        <form action="{{ route('inscriptions.niveaux') }}" method="POST" id="niveauxForm">
            @csrf
            <div id="niveauxContainer">
                @foreach($niveaux as $niveauIndex => $niveau)
                <div class="niveau-block card mb-4 bg-light" data-index="{{ $niveauIndex }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">{{ $niveau->nom }}</h4>
                            <button type="button" class="btn btn-sm btn-danger remove-niveau-btn">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>
                        
                        <input type="hidden" name="niveaux[{{ $niveauIndex }}][id]" value="{{ $niveau->id }}">
                        
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Nom du niveau</label>
                                <input type="text" class="form-control" name="niveaux[{{ $niveauIndex }}][nom]" value="{{ $niveau->nom }}" required>
                            </div>
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Frais d'inscription</label>
                                <input type="number" class="form-control" name="niveaux[{{ $niveauIndex }}][frais_inscription]" value="{{ $niveau->frais_inscription }}" min="0" required>
                            </div>
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Frais de scolarité</label>
                                <input type="number" class="form-control" name="niveaux[{{ $niveauIndex }}][frais_scolarite]" value="{{ $niveau->frais_scolarite }}" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="niveau_examen_{{ $niveauIndex }}" name="niveaux[{{ $niveauIndex }}][est_niveau_examen]" value="1" {{ $niveau->est_niveau_examen ? 'checked' : '' }}>
                                <label class="form-check-label" for="niveau_examen_{{ $niveauIndex }}">Niveau d'examen</label>
                            </div>
                            
                            <div class="frais-examen-container mt-2" style="{{ $niveau->est_niveau_examen ? '' : 'display: none;' }}">
                                <label class="form-label">Frais d'examen</label>
                                <input type="number" class="form-control" name="niveaux[{{ $niveauIndex }}][frais_examen]" value="{{ $niveau->frais_examen }}" min="0">
                            </div>
                        </div>
                        
                        <div class="classes-container">
                            <h5 class="mb-3">Classes</h5>
                            
                            @foreach($niveau->classes as $classeIndex => $classe)
                            <div class="classe-item row mb-2 align-items-center">
                                <input type="hidden" name="niveaux[{{ $niveauIndex }}][classes][{{ $classeIndex }}][id]" value="{{ $classe->id }}">
                                
                                <div class="col-md-5">
                                    <label class="form-label">Nom de la classe</label>
                                    <input type="text" class="form-control" name="niveaux[{{ $niveauIndex }}][classes][{{ $classeIndex }}][nom]" value="{{ $classe->nom }}" required>
                                </div>
                                
                                <div class="col-md-5">
                                    <label class="form-label">Capacité</label>
                                    <input type="number" class="form-control" name="niveaux[{{ $niveauIndex }}][classes][{{ $classeIndex }}][capacite]" value="{{ $classe->capacite }}" min="1" required>
                                </div>
                                
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-danger remove-classe-btn mt-3">
                                        <span class="material-icons">remove_circle</span>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-secondary add-classe-btn mt-3">
                            <span class="material-icons">add</span> Ajouter une classe
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div id="nouveauNiveauContainer" style="display: none;">
                <div class="card mb-4 bg-light">
                    <div class="card-body">
                        <h4 class="card-title">Ajouter un niveau</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Nom du niveau</label>
                                <input type="text" class="form-control" id="nouveau_niveau_nom" required>
                            </div>
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Frais d'inscription</label>
                                <input type="number" class="form-control" id="nouveau_niveau_frais_inscription" min="0" required>
                            </div>
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Frais de scolarité</label>
                                <input type="number" class="form-control" id="nouveau_niveau_frais_scolarite" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="nouveau_niveau_examen">
                                <label class="form-check-label" for="nouveau_niveau_examen">Niveau d'examen</label>
                            </div>
                            
                            <div id="nouveau_frais_examen_container" class="mt-2" style="display: none;">
                                <label class="form-label">Frais d'examen</label>
                                <input type="number" class="form-control" id="nouveau_niveau_frais_examen" min="0">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" id="ajouterNiveauBtn" class="btn btn-primary">
                                Ajouter ce niveau
                            </button>
                            <button type="button" id="annulerAjoutBtn" class="btn btn-secondary">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" id="saveBtn" class="btn btn-success">
                    <span class="material-icons">save</span> Enregistrer les niveaux et classes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addNiveauBtn = document.getElementById('addNiveauBtn');
        const nouveauNiveauContainer = document.getElementById('nouveauNiveauContainer');
        const ajouterNiveauBtn = document.getElementById('ajouterNiveauBtn');
        const annulerAjoutBtn = document.getElementById('annulerAjoutBtn');
        const removeNiveauBtns = document.querySelectorAll('.remove-niveau-btn');
        const removeClasseBtns = document.querySelectorAll('.remove-classe-btn');
        const addClasseBtns = document.querySelectorAll('.add-classe-btn');
        let niveauCounter = {{ count($niveaux) }};
        
        // Afficher le formulaire d'ajout de niveau
        addNiveauBtn.addEventListener('click', function() {
            nouveauNiveauContainer.style.display = 'block';
            this.style.display = 'none';
        });
        
        // Annuler l'ajout d'un niveau
        annulerAjoutBtn.addEventListener('click', function() {
            nouveauNiveauContainer.style.display = 'none';
            addNiveauBtn.style.display = 'block';
            // Réinitialiser le formulaire d'ajout
            document.getElementById('nouveau_niveau_nom').value = '';
            document.getElementById('nouveau_niveau_frais_inscription').value = '';
            document.getElementById('nouveau_niveau_frais_scolarite').value = '';
            document.getElementById('nouveau_niveau_examen').checked = false;
            document.getElementById('nouveau_niveau_frais_examen').value = '';
            document.getElementById('nouveau_frais_examen_container').style.display = 'none';
        });
        
        // Gérer l'affichage des frais d'examen
        document.querySelectorAll('input[name$="[est_niveau_examen]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const container = this.closest('.mb-3').querySelector('.frais-examen-container');
                container.style.display = this.checked ? 'block' : 'none';
            });
        });
        
        // Gérer l'affichage des frais d'examen dans le formulaire d'ajout
        document.getElementById('nouveau_niveau_examen').addEventListener('change', function() {
            const container = document.getElementById('nouveau_frais_examen_container');
            container.style.display = this.checked ? 'block' : 'none';
        });
        
        // Ajouter un nouveau niveau
        ajouterNiveauBtn.addEventListener('click', function() {
            const nom = document.getElementById('nouveau_niveau_nom').value;
            const fraisInscription = document.getElementById('nouveau_niveau_frais_inscription').value;
            const fraisScolarite = document.getElementById('nouveau_niveau_frais_scolarite').value;
            const estNiveauExamen = document.getElementById('nouveau_niveau_examen').checked;
            const fraisExamen = document.getElementById('nouveau_niveau_frais_examen').value || 0;
            
            if (!nom || !fraisInscription || !fraisScolarite) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
            
            // Créer un élément HTML pour le nouveau niveau
            const niveauHtml = `
                <div class="niveau-block card mb-4 bg-light" data-index="${niveauCounter}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">${nom}</h4>
                            <button type="button" class="btn btn-sm btn-danger remove-niveau-btn">
                                <span class="material-icons">delete</span>
                            </button>
                        </div>
                        
                        <input type="hidden" name="niveaux[${niveauCounter}][id]" value="">
                        
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Nom du niveau</label>
                                <input type="text" class="form-control" name="niveaux[${niveauCounter}][nom]" value="${nom}" required>
                            </div>
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Frais d'inscription</label>
                                <input type="number" class="form-control" name="niveaux[${niveauCounter}][frais_inscription]" value="${fraisInscription}" min="0" required>
                            </div>
                            
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Frais de scolarité</label>
                                <input type="number" class="form-control" name="niveaux[${niveauCounter}][frais_scolarite]" value="${fraisScolarite}" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input niveau-examen-checkbox" type="checkbox" id="niveau_examen_${niveauCounter}" name="niveaux[${niveauCounter}][est_niveau_examen]" value="1" ${estNiveauExamen ? 'checked' : ''}>
                                <label class="form-check-label" for="niveau_examen_${niveauCounter}">Niveau d'examen</label>
                            </div>
                            
                            <div class="frais-examen-container mt-2" style="${estNiveauExamen ? '' : 'display: none;'}">
                                <label class="form-label">Frais d'examen</label>
                                <input type="number" class="form-control" name="niveaux[${niveauCounter}][frais_examen]" value="${fraisExamen}" min="0">
                            </div>
                        </div>
                        
                        <div class="classes-container">
                            <h5 class="mb-3">Classes</h5>
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-secondary add-classe-btn mt-3">
                            <span class="material-icons">add</span> Ajouter une classe
                        </button>
                    </div>
                </div>
            `;
            
            // Ajouter le niveau au conteneur
            const niveauxContainer = document.getElementById('niveauxContainer');
            niveauxContainer.insertAdjacentHTML('beforeend', niveauHtml);
            
            // Ajouter les écouteurs d'événements au nouveau niveau
            const newNiveau = niveauxContainer.lastElementChild;
            
            // Écouteur pour le niveau d'examen
            const examenCheckbox = newNiveau.querySelector('.niveau-examen-checkbox');
            examenCheckbox.addEventListener('change', function() {
                const container = this.closest('.mb-3').querySelector('.frais-examen-container');
                container.style.display = this.checked ? 'block' : 'none';
            });
            
            // Écouteur pour supprimer le niveau
            const removeBtn = newNiveau.querySelector('.remove-niveau-btn');
            removeBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce niveau ?')) {
                    newNiveau.remove();
                }
            });
            
            // Écouteur pour ajouter une classe
            const addClasseBtn = newNiveau.querySelector('.add-classe-btn');
            addClasseBtn.addEventListener('click', function() {
                addClasse(this, niveauCounter, 0);
            });
            
            // Incrémenter le compteur de niveaux
            niveauCounter++;
            
            // Réinitialiser le formulaire d'ajout
            document.getElementById('nouveau_niveau_nom').value = '';
            document.getElementById('nouveau_niveau_frais_inscription').value = '';
            document.getElementById('nouveau_niveau_frais_scolarite').value = '';
            document.getElementById('nouveau_niveau_examen').checked = false;
            document.getElementById('nouveau_niveau_frais_examen').value = '';
            document.getElementById('nouveau_frais_examen_container').style.display = 'none';
            
            // Cacher le formulaire d'ajout et afficher le bouton d'ajout
            nouveauNiveauContainer.style.display = 'none';
            addNiveauBtn.style.display = 'block';
        });
        
        // Écouteurs pour supprimer un niveau
        removeNiveauBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce niveau ?')) {
                    this.closest('.niveau-block').remove();
                }
            });
        });
        
        // Écouteurs pour supprimer une classe
        removeClasseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')) {
                    this.closest('.classe-item').remove();
                }
            });
        });
        
        // Fonction pour ajouter une classe
        function addClasse(button, niveauIndex, classeIndex) {
            const classesContainer = button.previousElementSibling;
            const classeHtml = `
                <div class="classe-item row mb-2 align-items-center">
                    <input type="hidden" name="niveaux[${niveauIndex}][classes][${classeIndex}][id]" value="">
                    
                    <div class="col-md-5">
                        <label class="form-label">Nom de la classe</label>
                        <input type="text" class="form-control" name="niveaux[${niveauIndex}][classes][${classeIndex}][nom]" required>
                    </div>
                    
                    <div class="col-md-5">
                        <label class="form-label">Capacité</label>
                        <input type="number" class="form-control" name="niveaux[${niveauIndex}][classes][${classeIndex}][capacite]" value="50" min="1" required>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-danger remove-classe-btn mt-3">
                            <span class="material-icons">remove_circle</span>
                        </button>
                    </div>
                </div>
            `;
            
            classesContainer.insertAdjacentHTML('beforeend', classeHtml);
            
            // Ajouter l'écouteur pour supprimer la classe
            const removeBtn = classesContainer.lastElementChild.querySelector('.remove-classe-btn');
            removeBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')) {
                    this.closest('.classe-item').remove();
                }
            });
        }
        
        // Écouteurs pour ajouter une classe
        addClasseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const niveauBlock = this.closest('.niveau-block');
                const niveauIndex = niveauBlock.dataset.index;
                const classesCount = niveauBlock.querySelectorAll('.classe-item').length;
                addClasse(this, niveauIndex, classesCount);
            });
        });
        
        // S'assurer que le formulaire est soumis correctement
        document.getElementById('niveauxForm').addEventListener('submit', function() {
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...';
        });
    });
</script>
@endsection