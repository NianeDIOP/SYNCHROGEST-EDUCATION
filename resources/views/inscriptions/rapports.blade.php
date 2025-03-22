@extends('layouts.app')

@section('title', 'Rapports d\'Inscription')
@section('page-title', 'Rapports d\'Inscription')

@section('content')
<div class="card">
    <div class="card-body">
        <h2 class="card-title mb-4">Générer des Rapports</h2>
        
        <form action="{{ route('inscriptions.genererRapport') }}" method="POST" target="_blank">
            @csrf
            <div class="mb-4">
                <label class="form-label d-block">Type de rapport</label>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="type" id="typeGeneral" value="general" checked>
                    <label class="btn btn-outline-primary" for="typeGeneral">Général (tous les niveaux)</label>
                    
                    <input type="radio" class="btn-check" name="type" id="typeNiveau" value="niveau">
                    <label class="btn btn-outline-primary" for="typeNiveau">Par niveau</label>
                    
                    <input type="radio" class="btn-check" name="type" id="typeClasse" value="classe">
                    <label class="btn btn-outline-primary" for="typeClasse">Par classe</label>
                </div>
            </div>
            
            <div id="niveauContainer" class="mb-4" style="display: none;">
                <label class="form-label">Niveau</label>
                <select class="form-select" name="niveau_id" id="niveauSelect">
                    <option value="">-- Sélectionner un niveau --</option>
                    @foreach($niveaux as $niveau)
                        <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                    @endforeach
                </select>
            </div>
            
            <div id="classeContainer" class="mb-4" style="display: none;">
                <label class="form-label">Classe</label>
                <select class="form-select" name="classe_id" id="classeSelect">
                    <option value="">-- Sélectionner d'abord un niveau --</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Statut des élèves</label>
                <select class="form-select" name="statut">
                    <option value="tous">Tous les statuts</option>
                    <option value="Nouveau">Nouveaux</option>
                    <option value="Ancien">Anciens</option>
                    <option value="Redoublant">Redoublants</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="form-label d-block">Format du rapport</label>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="format" id="formatPdf" value="pdf" checked>
                    <label class="btn btn-outline-secondary" for="formatPdf">PDF</label>
                    
                    <input type="radio" class="btn-check" name="format" id="formatExcel" value="excel">
                    <label class="btn btn-outline-secondary" for="formatExcel">Excel</label>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" id="genererBtn">
                    <span class="material-icons">description</span> Générer le rapport
                </button>
            </div>
        </form>
        
        <div class="mt-5 p-4 bg-light rounded">
            <h3 class="mb-3">Types de rapports disponibles</h3>
            <ul class="list-group mb-3">
                <li class="list-group-item">
                    <strong>Rapport général</strong> - Liste de tous les élèves inscrits pour l'année scolaire en cours.
                </li>
                <li class="list-group-item">
                    <strong>Rapport par niveau</strong> - Liste des élèves inscrits dans un niveau spécifique.
                </li>
                <li class="list-group-item">
                    <strong>Rapport par classe</strong> - Liste des élèves inscrits dans une classe spécifique.
                </li>
            </ul>
            
            <div class="alert alert-info">
                <p class="mb-0">Tous les rapports incluent les informations d'inscription, les statuts de paiement et les détails des élèves.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeGeneral = document.getElementById('typeGeneral');
        const typeNiveau = document.getElementById('typeNiveau');
        const typeClasse = document.getElementById('typeClasse');
        const niveauContainer = document.getElementById('niveauContainer');
        const classeContainer = document.getElementById('classeContainer');
        const niveauSelect = document.getElementById('niveauSelect');
        const classeSelect = document.getElementById('classeSelect');
        const genererBtn = document.getElementById('genererBtn');
        
        // Données des niveaux et classes
        const niveauxClasses = {
            @foreach($niveaux as $niveau)
                {{ $niveau->id }}: [
                    @foreach($niveau->classes as $classe)
                        { id: {{ $classe->id }}, nom: "{{ $classe->nom }}" },
                    @endforeach
                ],
            @endforeach
        };
        
        // Gérer le changement de type de rapport
        function handleTypeChange() {
            if (typeGeneral.checked) {
                niveauContainer.style.display = 'none';
                classeContainer.style.display = 'none';
                niveauSelect.required = false;
                classeSelect.required = false;
            } else if (typeNiveau.checked) {
                niveauContainer.style.display = 'block';
                classeContainer.style.display = 'none';
                niveauSelect.required = true;
                classeSelect.required = false;
            } else if (typeClasse.checked) {
                niveauContainer.style.display = 'block';
                classeContainer.style.display = 'block';
                niveauSelect.required = true;
                classeSelect.required = true;
            }
        }
        
        typeGeneral.addEventListener('change', handleTypeChange);
        typeNiveau.addEventListener('change', handleTypeChange);
        typeClasse.addEventListener('change', handleTypeChange);
        
        // Mettre à jour les options de classe lorsque le niveau change
        niveauSelect.addEventListener('change', function() {
            const niveauId = this.value;
            
            // Vider le select de classe
            classeSelect.innerHTML = '';
            
            if (niveauId) {
                // Ajouter l'option par défaut
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Sélectionner une classe --';
                classeSelect.appendChild(defaultOption);
                
                // Ajouter les options de classe pour ce niveau
                if (niveauxClasses[niveauId]) {
                    niveauxClasses[niveauId].forEach(classe => {
                        const option = document.createElement('option');
                        option.value = classe.id;
                        option.textContent = classe.nom;
                        classeSelect.appendChild(option);
                    });
                }
            } else {
                // Si aucun niveau n'est sélectionné
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Sélectionner d\'abord un niveau --';
                classeSelect.appendChild(defaultOption);
            }
        });
        
        // Validation du formulaire avant soumission
        document.querySelector('form').addEventListener('submit', function(event) {
            if (typeNiveau.checked && !niveauSelect.value) {
                event.preventDefault();
                alert('Veuillez sélectionner un niveau.');
                return false;
            }
            
            if (typeClasse.checked && (!niveauSelect.value || !classeSelect.value)) {
                event.preventDefault();
                alert('Veuillez sélectionner un niveau et une classe.');
                return false;
            }
            
            // Afficher un indicateur de chargement
            genererBtn.disabled = true;
            genererBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Génération en cours...';
        });
        
        // Initialiser l'affichage
        handleTypeChange();
    });
</script>
@endsection