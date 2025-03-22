@extends('layouts.app')

@section('title', 'Paramètres - Module Inscription')
@section('page-title', 'Paramètres généraux')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="card-title">Informations de l'établissement</h2>
        </div>
        
        <form action="{{ route('inscriptions.parametres') }}" method="POST">
            @csrf
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom de l'établissement</label>
                    <input type="text" class="form-control" name="nom_etablissement" value="{{ $parametres->nom_etablissement ?? '' }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Adresse</label>
                    <input type="text" class="form-control" name="adresse" value="{{ $parametres->adresse ?? '' }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" class="form-control" name="telephone" value="{{ $parametres->telephone ?? '' }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ $parametres->email ?? '' }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Année Scolaire</label>
                    <input type="text" class="form-control" name="annee_scolaire" value="{{ $parametres->annee_scolaire ?? '' }}" placeholder="ex: 2024-2025" required>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-success">
                    <span class="material-icons">save</span> Enregistrer les paramètres
                </button>
            </div>
        </form>
    </div>
</div>
@endsection