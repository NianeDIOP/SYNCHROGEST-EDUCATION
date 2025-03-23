@extends('layouts.app')

@section('title', 'Nouvelle Catégorie d\'Articles')
@section('page-title', 'Ajouter une nouvelle catégorie')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informations de la catégorie</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('matieres.enregistrerCategorie') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="nom" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Décrivez la catégorie et les types d'articles qu'elle contiendra.
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('matieres.categories') }}" class="btn btn-secondary me-2">
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