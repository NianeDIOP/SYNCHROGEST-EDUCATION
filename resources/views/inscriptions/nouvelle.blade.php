@extends('layouts.app')

@section('title', 'Nouvelle Inscription')
@section('page-title', 'Nouvelle Inscription')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="mb-4 p-4 bg-light rounded">
            <h3 class="mb-3">Rechercher un élève par INE</h3>
            <form action="{{ route('inscriptions.nouvelle') }}" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="ine" placeholder="Saisir l'INE..." value="{{ $searchIne }}" required>
                    <button class="btn btn-primary" type="submit">
                        <span class="material-icons">search</span> Rechercher
                    </button>
                </div>
            </form>
        </div>
        
        @if($eleve)
            <div class="mb-4 p-4 bg-light rounded">
                <h3 class="mb-3">Informations de l'élève</h3>
                <div class="row">
                    <div class="col-md-2 text-center mb-3">
                        <img src="{{ $eleve->photo_path ?? 'https://via.placeholder.com/150?text=Photo' }}" alt="Photo de l'élève" class="img-fluid rounded border" style="max-height: 150px;">
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <p class="mb-1"><strong>INE:</strong> {{ $eleve->ine }}</p>
                            </div>
                            <div class="col-md-4 mb-2">
                                <p class="mb-1"><strong>Nom & Prénom:</strong> {{ $eleve->nom }} {{ $eleve->prenom }}</p>
                            </div>
                            <div class="col-md-4 mb-2">
                                <p class="mb-1"><strong>Sexe:</strong> {{ $eleve->sexe }}</p>
                            </div>
                            <div class="col-md-4 mb-2">
                                <p class="mb-1"><strong>Date de naissance:</strong> {{ $eleve->date_naissance->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-4 mb-2">
                                <p class="mb-1"><strong>Lieu de naissance:</strong> {{ $eleve->lieu_naissance }}</p>
                            </div>
                            <div class="col-md-4 mb-2">
                                <p class="mb-1"><strong>Extrait:</strong> {{ $eleve->existence_extrait ? 'Oui' : 'Non' }}</p>
                            </div>
                            <div class="col-md-4 mb-2">
                                <p class="mb-1"><strong>Statut:</strong> <span class="badge {{ 
                                    $eleve->statut === 'Nouveau' ? 'bg-success' : 
                                    ($eleve->statut === 'Ancien' ? 'bg-primary' : 'bg-warning')
                                }}">{{ $eleve->statut }}</span></p>
                            </div>
                            <div class="col-md-8 mb-2">
                                <p class="mb-1"><strong>Classe actuelle:</strong> {{ $eleve->classe->niveau->nom }} - {{ $eleve->classe->nom }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('inscriptions.nouvelle') }}" method="POST">
                @csrf
                <input type="hidden" name="eleve_id" value="{{ $eleve->id }}">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Classe d'inscription</label>
                        <select class="form-select @error('classe_id') is-invalid @enderror" name="classe_id" id="classeSelect" required>
                            <option value="">-- Sélectionner une classe --</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" data-frais="{{ $classe->niveau->frais_inscription }}" data-examen="{{ $classe->niveau->est_niveau_examen ? $classe->niveau->frais_examen : 0 }}">
                                    {{ $classe->niveau->nom }} - {{ $classe->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('classe_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date d'inscription</label>
                        <input type="date" class="form-control @error('date_inscription') is-invalid @enderror" name="date_inscription" value="{{ date('Y-m-d') }}" required>
                        @error('date_inscription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Paiement des frais</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Frais d'inscription</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="fraisInscription" value="0" readonly>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Frais d'examen</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="fraisExamen" value="0" readonly>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Total à payer</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="totalFrais" value="0" readonly>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Montant payé</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('montant_paye') is-invalid @enderror" name="montant_paye" id="montantPaye" min="0" value="0" required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('montant_paye')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Reste à payer</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="resteAPayer" value="0" readonly>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('inscriptions.eleves') }}" class="btn btn-secondary me-2">
                        <span class="material-icons">arrow_back</span> Annuler
                    </a>
                    <button type="submit" class="btn btn-success">
                        <span class="material-icons">save</span> Enregistrer l'inscription
                    </button>
                </div>
            </form>
        @elseif($searchIne)
            <div class="alert alert-warning">
                <div class="d-flex align-items-center">
                    <span class="material-icons me-2">warning</span>
                    <p class="mb-0">Aucun élève trouvé avec l'INE: {{ $searchIne }}</p>
                </div>
                <p class="mt-2">Veuillez vérifier l'INE ou importer l'élève en premier.</p>
                <a href="{{ route('inscriptions.import') }}" class="btn btn-primary mt-2">
                    <span class="material-icons">upload</span> Importer des élèves
                </a>
            </div>
        @else
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <span class="material-icons me-2">info</span>
                    <p class="mb-0">Recherchez un élève par son INE pour procéder à l'inscription.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classeSelect = document.getElementById('classeSelect');
        const fraisInscription = document.getElementById('fraisInscription');
        const fraisExamen = document.getElementById('fraisExamen');
        const totalFrais = document.getElementById('totalFrais');
        const montantPaye = document.getElementById('montantPaye');
        const resteAPayer = document.getElementById('resteAPayer');
        
        // Formatage des nombres
        const formatter = new Intl.NumberFormat('fr-FR');
        
        // Calculer les frais lors du changement de classe
        classeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                const fraisInscriptionValue = parseFloat(selectedOption.dataset.frais);
                const fraisExamenValue = parseFloat(selectedOption.dataset.examen);
                const totalValue = fraisInscriptionValue + fraisExamenValue;
                
                fraisInscription.value = formatter.format(fraisInscriptionValue);
                fraisExamen.value = formatter.format(fraisExamenValue);
                totalFrais.value = formatter.format(totalValue);
                
                // Mettre à jour le montant payé et le reste à payer
                montantPaye.max = totalValue;
                montantPaye.value = totalValue;
                resteAPayer.value = formatter.format(0);
            } else {
                fraisInscription.value = '0';
                fraisExamen.value = '0';
                totalFrais.value = '0';
                montantPaye.value = '0';
                resteAPayer.value = '0';
            }
        });
        
        // Calculer le reste à payer lors du changement du montant payé
        montantPaye.addEventListener('input', function() {
            const total = parseFloat(totalFrais.value.replace(/\s/g, '').replace(',', '.'));
            const paye = parseFloat(this.value) || 0;
            const reste = Math.max(0, total - paye);
            
            resteAPayer.value = formatter.format(reste);
        });
    });
</script>
@endsection