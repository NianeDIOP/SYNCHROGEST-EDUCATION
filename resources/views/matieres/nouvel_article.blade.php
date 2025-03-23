@extends('layouts.app')

@section('title', 'Nouvel Article')
@section('page-title', 'Ajouter un nouvel article')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informations de l'article</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('matieres.nouvelArticle') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">Code article <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="designation" class="form-label">Désignation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation') }}" required>
                    @error('designation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="categorie_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                    <select class="form-select @error('categorie_id') is-invalid @enderror" id="categorie_id" name="categorie_id" required>
                        <option value="">-- Sélectionner une catégorie --</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <a href="{{ route('matieres.nouvelleCategorie') }}" target="_blank">Ajouter une nouvelle catégorie</a>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="unite_mesure" class="form-label">Unité de mesure <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('unite_mesure') is-invalid @enderror" id="unite_mesure" name="unite_mesure" value="{{ old('unite_mesure') }}" placeholder="ex: unité, kg, litre, etc." required>
                    @error('unite_mesure')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="quantite_stock" class="form-label">Quantité initiale en stock <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('quantite_stock') is-invalid @enderror" id="quantite_stock" name="quantite_stock" value="{{ old('quantite_stock', 0) }}" step="0.01" min="0" required>
                    @error('quantite_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="seuil_alerte" class="form-label">Seuil d'alerte <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('seuil_alerte') is-invalid @enderror" id="seuil_alerte" name="seuil_alerte" value="{{ old('seuil_alerte', 5) }}" step="0.01" min="0" required>
                    @error('seuil_alerte')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="prix_unitaire" class="form-label">Prix unitaire (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('prix_unitaire') is-invalid @enderror" id="prix_unitaire" name="prix_unitaire" value="{{ old('prix_unitaire', 0) }}" step="0.01" min="0" required>
                    @error('prix_unitaire')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="emplacement" class="form-label">Emplacement</label>
                    <input type="text" class="form-control @error('emplacement') is-invalid @enderror" id="emplacement" name="emplacement" value="{{ old('emplacement') }}" placeholder="ex: Armoire A, Étagère 3, etc.">
                    @error('emplacement')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="image" class="form-label">Image de l'article</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="est_actif" name="est_actif" value="1" {{ old('est_actif', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="est_actif">Article actif</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('matieres.articles') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Générer un code article unique si le champ est vide
        const codeInput = document.getElementById('code');
        if (!codeInput.value) {
            const prefix = 'ART-';
            const random = Math.floor(Math.random() * 100000).toString().padStart(5, '0');
            codeInput.value = prefix + random;
        }
    });
</script>
@endsection