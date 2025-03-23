@extends('layouts.app')

@section('title', 'Rapports Financiers')
@section('page-title', 'Rapports Financiers')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Génération de rapports financiers</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('finances.genererRapport') }}" method="POST" id="rapportForm" target="_blank">
            @csrf
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Type de rapport</label>
                    <div class="btn-group d-flex" role="group">
                        <input type="radio" class="btn-check" name="type_rapport" id="typeGeneral" value="general" checked>
                        <label class="btn btn-outline-primary" for="typeGeneral">Général</label>
                        
                        <input type="radio" class="btn-check" name="type_rapport" id="typeMensuel" value="mensuel">
                        <label class="btn btn-outline-primary" for="typeMensuel">Mensuel</label>
                        
                        <input type="radio" class="btn-check" name="type_rapport" id="typeCategorie" value="categorie">
                        <label class="btn btn-outline-primary" for="typeCategorie">Par catégorie</label>
                    </div>
                </div>
                
                <div class="col-md-8 mb-3" id="periodeContainer" style="display: none;">
                    <label class="form-label">Période</label>
                    <div class="input-group">
                        <span class="input-group-text">Du</span>
                        <input type="date" class="form-control" id="periode_debut" name="periode_debut">
                        <span class="input-group-text">Au</span>
                        <input type="date" class="form-control" id="periode_fin" name="periode_fin">
                    </div>
                </div>
                
                <div class="col-md-4 mb-3" id="categorieContainer" style="display: none;">
                    <label class="form-label">Catégorie financière</label>
                    <select class="form-select" id="categorie_id" name="categorie_id">
                        <option value="">-- Sélectionner une catégorie --</option>
                        <optgroup label="Recettes">
                            @foreach($categories->where('type', 'recette') as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Dépenses">
                            @foreach($categories->where('type', 'depense') as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Format</label>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="format" id="formatPdf" value="pdf" checked>
                    <label class="btn btn-outline-primary" for="formatPdf">
                        <i class="fas fa-file-pdf me-2"></i> PDF
                    </label>
                    
                    <input type="radio" class="btn-check" name="format" id="formatExcel" value="excel">
                    <label class="btn btn-outline-primary" for="formatExcel">
                        <i class="fas fa-file-excel me-2"></i> Excel
                    </label>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" id="genererBtn">
                <i class="fas fa-chart-bar me-2"></i> Générer le rapport
            </button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Récapitulatif financier annuel</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Catégorie</th>
                                <th class="text-end">Montant ({{ $parametres->devise ?? 'FCFA' }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-success">
                                <td>Total Recettes</td>
                                <td class="text-end fw-bold">{{ number_format($stats['totalRecettes'] ?? 0, 0, ',', ' ') }}</td>
                            </tr>
                            <tr class="table-danger">
                                <td>Total Dépenses</td>
                                <td class="text-end fw-bold">{{ number_format($stats['totalDepenses'] ?? 0, 0, ',', ' ') }}</td>
                            </tr>
                            <tr class="{{ ($stats['solde'] ?? 0) >= 0 ? 'table-primary' : 'table-warning' }}">
                                <td>Solde</td>
                                <td class="text-end fw-bold">{{ number_format($stats['solde'] ?? 0, 0, ',', ' ') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i> Ces données concernent l'année scolaire {{ $parametres->annee_scolaire ?? 'actuelle' }}.
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rapports disponibles</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action" onclick="preselect('general', 'pdf')">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Bilan financier général</h5>
                            <small class="text-primary">PDF</small>
                        </div>
                        <p class="mb-1">Résumé complet de toutes les recettes et dépenses pour l'année scolaire.</p>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="preselect('mensuel', 'pdf')">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Rapport mensuel</h5>
                            <small class="text-primary">PDF</small>
                        </div>
                        <p class="mb-1">Détails des transactions pour une période spécifique.</p>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="preselect('categorie', 'excel')">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Rapport par catégorie</h5>
                            <small class="text-success">Excel</small>
                        </div>
                        <p class="mb-1">Analyse détaillée des transactions par catégorie financière.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeGeneral = document.getElementById('typeGeneral');
        const typeMensuel = document.getElementById('typeMensuel');
        const typeCategorie = document.getElementById('typeCategorie');
        
        const periodeContainer = document.getElementById('periodeContainer');
        const categorieContainer = document.getElementById('categorieContainer');
        
        const periodeDebut = document.getElementById('periode_debut');
        const periodeFin = document.getElementById('periode_fin');
        const categorieId = document.getElementById('categorie_id');
        
        // Gérer l'affichage des champs selon le type de rapport
        function updateFormFields() {
            periodeContainer.style.display = typeMensuel.checked ? 'block' : 'none';
            categorieContainer.style.display = typeCategorie.checked ? 'block' : 'none';
            
            // Mettre à jour les champs requis
            periodeDebut.required = typeMensuel.checked;
            periodeFin.required = typeMensuel.checked;
            categorieId.required = typeCategorie.checked;
        }
        
        // Initialiser les champs
        updateFormFields();
        
        // Attacher les événements
        typeGeneral.addEventListener('change', updateFormFields);
        typeMensuel.addEventListener('change', updateFormFields);
        typeCategorie.addEventListener('change', updateFormFields);
        
        // Valider le formulaire avant soumission
        document.getElementById('rapportForm').addEventListener('submit', function(event) {
            if (typeMensuel.checked) {
                if (!periodeDebut.value || !periodeFin.value) {
                    event.preventDefault();
                    alert('Veuillez spécifier une période complète (début et fin).');
                    return;
                }
                
                const debut = new Date(periodeDebut.value);
                const fin = new Date(periodeFin.value);
                
                if (debut > fin) {
                    event.preventDefault();
                    alert('La date de début doit être antérieure à la date de fin.');
                    return;
                }
            }
            
            if (typeCategorie.checked && !categorieId.value) {
                event.preventDefault();
                alert('Veuillez sélectionner une catégorie.');
                return;
            }
        });
    });
    
    // Fonction pour présélectionner un type de rapport
    function preselect(type, format) {
        document.getElementById('type' + type.charAt(0).toUpperCase() + type.slice(1)).checked = true;
        document.getElementById('format' + format.charAt(0).toUpperCase() + format.slice(1)).checked = true;
        
        // Mettre à jour l'affichage des champs
        const event = new Event('change');
        document.getElementById('type' + type.charAt(0).toUpperCase() + type.slice(1)).dispatchEvent(event);
        
        // Faire défiler jusqu'au formulaire
        document.getElementById('rapportForm').scrollIntoView({ behavior: 'smooth' });
    }
</script>
@endsection