@extends('layouts.app')

@section('title', 'Modifier Article')
@section('page-title', 'Modifier un article')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Modification de l'article</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('matieres.updateArticle', $article->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">Code article <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $article->code) }}" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="designation" class="form-label">Désignation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation', $article->designation) }}" required>
                    @error('designation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $article->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="categorie_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                    <select class="form-select @error('categorie_id') is-invalid @enderror" id="categorie_id" name="categorie_id" required>
                        <option value="">-- Sélectionner une catégorie --</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ old('categorie_id', $article->categorie_id) == $categorie->id ? 'selected' : '' }}>
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
                    <input type="text" class="form-control @error('unite_mesure') is-invalid @enderror" id="unite_mesure" name="unite_mesure" value="{{ old('unite_mesure', $article->unite_mesure) }}" placeholder="ex: unité, kg, litre, etc." required>
                    @error('unite_mesure')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="seuil_alerte" class="form-label">Seuil d'alerte <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('seuil_alerte') is-invalid @enderror" id="seuil_alerte" name="seuil_alerte" value="{{ old('seuil_alerte', $article->seuil_alerte) }}" step="0.01" min="0" required>
                    @error('seuil_alerte')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="prix_unitaire" class="form-label">Prix unitaire (FCFA) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('prix_unitaire') is-invalid @enderror" id="prix_unitaire" name="prix_unitaire" value="{{ old('prix_unitaire', $article->prix_unitaire) }}" step="0.01" min="0" required>
                    @error('prix_unitaire')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="quantite_stock" class="form-label">Quantité en stock</label>
                    <input type="number" class="form-control" id="quantite_stock" value="{{ $article->quantite_stock }}" disabled>
                    <div class="form-text text-muted">
                        Pour modifier le stock, utilisez les mouvements d'entrée/sortie.
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="emplacement" class="form-label">Emplacement</label>
                    <input type="text" class="form-control @error('emplacement') is-invalid @enderror" id="emplacement" name="emplacement" value="{{ old('emplacement', $article->emplacement) }}" placeholder="ex: Armoire A, Étagère 3, etc.">
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
                    
                    @if($article->image)
                        <div class="d-flex align-items-center mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="supprimer_image" name="supprimer_image" value="1">
                                <label class="form-check-label" for="supprimer_image">
                                    Supprimer l'image actuelle
                                </label>
                            </div>
                            <a href="{{ asset('storage/articles/' . $article->image) }}" target="_blank" class="btn btn-sm btn-info ms-3">
                                <i class="fas fa-eye me-1"></i> Voir l'image
                            </a>
                        </div>
                    @endif
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="est_actif" name="est_actif" value="1" {{ old('est_actif', $article->est_actif) ? 'checked' : '' }}>
                        <label class="form-check-label" for="est_actif">Article actif</label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('matieres.showArticle', $article->id) }}" class="btn btn-secondary me-2">
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