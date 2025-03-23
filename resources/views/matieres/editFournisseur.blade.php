@extends('layouts.app')

@section('title', 'Modifier Fournisseur')
@section('page-title', 'Modifier un fournisseur')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informations du fournisseur</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('matieres.updateFournisseur', $fournisseur->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nom" class="form-label">Nom / Raison sociale <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $fournisseur->nom) }}" required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $fournisseur->telephone) }}">
                    @error('telephone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $fournisseur->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" value="{{ old('adresse', $fournisseur->adresse) }}">
                    @error('adresse')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="personne_contact" class="form-label">Personne de contact</label>
                    <input type="text" class="form-control @error('personne_contact') is-invalid @enderror" id="personne_contact" name="personne_contact" value="{{ old('personne_contact', $fournisseur->personne_contact) }}">
                    @error('personne_contact')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="telephone_contact" class="form-label">Téléphone contact</label>
                    <input type="text" class="form-control @error('telephone_contact') is-invalid @enderror" id="telephone_contact" name="telephone_contact" value="{{ old('telephone_contact', $fournisseur->telephone_contact) }}">
                    @error('telephone_contact')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="est_actif" name="est_actif" value="1" {{ old('est_actif', $fournisseur->est_actif) ? 'checked' : '' }}>
                        <label class="form-check-label" for="est_actif">Fournisseur actif</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('matieres.fournisseurs') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection