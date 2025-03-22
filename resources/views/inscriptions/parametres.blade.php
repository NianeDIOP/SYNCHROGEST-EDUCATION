@extends('layouts.app')

@section('title', 'Paramètres - Module Inscription')
@section('page-title', 'Paramètres généraux')

@section('content')
<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Informations de l'établissement</h1>
</div>

<div class="card shadow mb-4 border-left-primary">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Paramètres généraux</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('inscriptions.parametres') }}" method="POST" id="parametresForm">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="nom_etablissement" class="form-label">Nom de l'établissement <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom_etablissement') is-invalid @enderror" id="nom_etablissement" name="nom_etablissement" value="{{ $parametres->nom_etablissement ?? '' }}" required>
                        @error('nom_etablissement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="annee_scolaire" class="form-label">Année Scolaire <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('annee_scolaire') is-invalid @enderror" id="annee_scolaire" name="annee_scolaire" value="{{ $parametres->annee_scolaire ?? '' }}" placeholder="ex: 2024-2025" required>
                        @error('annee_scolaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" value="{{ $parametres->adresse ?? '' }}">
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ $parametres->telephone ?? '' }}">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $parametres->email ?? '' }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="logo" class="form-label">Logo de l'établissement</label>
                        <div class="input-group">
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
                            <button class="btn btn-outline-secondary" type="button" disabled>
                                <i class="material-icons-round">upload</i>
                            </button>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">Format recommandé: PNG, JPG (max 2Mo)</small>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-success" id="submitBtn">
                    <i class="material-icons-round">save</i> Enregistrer les paramètres
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4 border-left-info">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-info">Prévisualisation de l'entête des documents</h6>
    </div>
    <div class="card-body">
        <div class="border p-4 text-center">
            <div class="mb-3">
                <img src="{{ $parametres->logo_path ?? 'https://via.placeholder.com/150x150?text=Logo' }}" alt="Logo" style="max-height: 100px;" class="img-fluid">
            </div>
            <h3 class="text-primary mb-1">{{ $parametres->nom_etablissement ?? 'Nom de l\'établissement' }}</h3>
            <p class="mb-1">{{ $parametres->adresse ?? 'Adresse de l\'établissement' }}</p>
            <p class="mb-1">Tél: {{ $parametres->telephone ?? '+XXX XX XX XX XX' }} | Email: {{ $parametres->email ?? 'email@etablissement.com' }}</p>
            <p class="font-weight-bold">Année scolaire: {{ $parametres->annee_scolaire ?? '20XX-20XX' }}</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Real-time preview updates
        const formInputs = document.querySelectorAll('#parametresForm input');
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                updatePreview();
            });
        });
        
        function updatePreview() {
            const nom = document.getElementById('nom_etablissement').value || 'Nom de l\'établissement';
            const adresse = document.getElementById('adresse').value || 'Adresse de l\'établissement';
            const telephone = document.getElementById('telephone').value || '+XXX XX XX XX XX';
            const email = document.getElementById('email').value || 'email@etablissement.com';
            const anneeScolaire = document.getElementById('annee_scolaire').value || '20XX-20XX';
            
            document.querySelector('.card-body .text-primary').textContent = nom;
            document.querySelector('.card-body p:nth-of-type(1)').textContent = adresse;
            document.querySelector('.card-body p:nth-of-type(2)').textContent = `Tél: ${telephone} | Email: ${email}`;
            document.querySelector('.card-body p:nth-of-type(3)').textContent = `Année scolaire: ${anneeScolaire}`;
        }
        
        // Form validation
        const form = document.getElementById('parametresForm');
        form.addEventListener('submit', function(event) {
            // Prevent form submission if validation fails
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Show error toast
                window.showToast('Veuillez corriger les erreurs dans le formulaire.', 'error');
            } else {
                // Disable submit button to prevent multiple submissions
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...';
            }
            
            form.classList.add('was-validated');
        });
    });
</script>
@endsection