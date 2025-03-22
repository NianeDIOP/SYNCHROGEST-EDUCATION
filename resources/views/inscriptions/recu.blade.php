@extends('layouts.app')

@section('title', 'Reçu d\'Inscription')
@section('page-title', 'Reçu d\'Inscription')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="card-title">Reçu d'Inscription</h2>
            <div>
                <button id="printBtn" class="btn btn-primary me-2">
                    <span class="material-icons">print</span> Imprimer
                </button>
                <a href="{{ route('inscriptions.nouvelle') }}" class="btn btn-secondary">
                    <span class="material-icons">arrow_back</span> Retour
                </a>
            </div>
        </div>
        
        <div id="printArea" class="border rounded p-4">
            <div class="text-center mb-4 pb-3 border-bottom">
                <h1 class="h3 text-primary">{{ $parametres->nom_etablissement ?? 'SYNCHROGEST-ÉDUCATION' }}</h1>
                @if($parametres->adresse)
                    <p class="mb-1">{{ $parametres->adresse }}</p>
                @endif
                @if($parametres->telephone)
                    <p class="mb-1">Tél: {{ $parametres->telephone }}</p>
                @endif
                @if($parametres->email)
                    <p class="mb-1">{{ $parametres->email }}</p>
                @endif
                
                <h2 class="h4 mt-4">REÇU D'INSCRIPTION</h2>
                <p>Année scolaire: {{ $parametres->annee_scolaire ?? '' }}</p>
            </div>
            
            <div class="text-end mb-4">
                <p class="text-primary fw-bold">N° {{ $inscription->numero_recu }}</p>
                <p>Date: {{ $inscription->date_inscription->format('d/m/Y') }}</p>
            </div>
            
            <div class="mb-4">
                <h3 class="h5 text-primary mb-3">Informations de l'élève</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>INE:</strong> {{ $inscription->eleve->ine }}</p>
                        <p class="mb-1"><strong>Nom & Prénom:</strong> {{ $inscription->eleve->nom }} {{ $inscription->eleve->prenom }}</p>
                    </div>
                    
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Date de naissance:</strong> {{ $inscription->eleve->date_naissance->format('d/m/Y') }}</p>
                        <p class="mb-1"><strong>Classe:</strong> {{ $inscription->classe->niveau->nom }} - {{ $inscription->classe->nom }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-light p-3 rounded mb-4">
                <h3 class="h5 text-primary mb-3">Détails du paiement</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Total des frais:</strong> {{ number_format($inscription->montant_paye + $inscription->montant_restant, 0, ',', ' ') }} FCFA</p>
                        <p class="mb-1"><strong>Montant payé:</strong> {{ number_format($inscription->montant_paye, 0, ',', ' ') }} FCFA</p>
                    </div>
                    
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Reste à payer:</strong> {{ number_format($inscription->montant_restant, 0, ',', ' ') }} FCFA</p>
                        <p class="mb-1">
                            <strong>Statut du paiement:</strong>
                            <span class="{{ 
                                $inscription->statut_paiement === 'Complet' ? 'text-success' : 
                                ($inscription->statut_paiement === 'Partiel' ? 'text-warning' : 'text-danger')
                            }} fw-bold">
                                {{ $inscription->statut_paiement }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 d-flex justify-content-between">
                <div class="border-top border-dark" style="width: 200px;">
                    <p class="text-center mt-1">Signature de l'élève/parent</p>
                </div>
                
                <div class="border-top border-dark" style="width: 200px;">
                    <p class="text-center mt-1">Cachet et signature</p>
                </div>
            </div>
            
            <div class="mt-5 text-center text-muted">
                <p>Ce reçu est un document officiel. Veuillez le conserver soigneusement.</p>
                <p class="small mt-3">SYNCHROGEST-ÉDUCATION - Système de Gestion Intégré pour Établissements Scolaires</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('printBtn').addEventListener('click', function() {
            const printContents = document.getElementById('printArea').innerHTML;
            const originalContents = document.body.innerHTML;
            
            document.body.innerHTML = `
                <html>
                    <head>
                        <title>Reçu d'inscription - {{ $inscription->eleve->nom }} {{ $inscription->eleve->prenom }}</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                padding: 20px;
                            }
                            .text-primary {
                                color: #0d6efd;
                            }
                            .text-success {
                                color: #198754;
                            }
                            .text-warning {
                                color: #ffc107;
                            }
                            .text-danger {
                                color: #dc3545;
                            }
                            .fw-bold {
                                font-weight: bold;
                            }
                            .text-center {
                                text-align: center;
                            }
                            .text-end {
                                text-align: right;
                            }
                            .border-bottom {
                                border-bottom: 1px solid #dee2e6;
                            }
                            .border-top {
                                border-top: 1px solid #000;
                            }
                            .mb-1 {
                                margin-bottom: 0.25rem;
                            }
                            .mb-3 {
                                margin-bottom: 1rem;
                            }
                            .mb-4 {
                                margin-bottom: 1.5rem;
                            }
                            .mt-1 {
                                margin-top: 0.25rem;
                            }
                            .mt-3 {
                                margin-top: 1rem;
                            }
                            .mt-4 {
                                margin-top: 1.5rem;
                            }
                            .mt-5 {
                                margin-top: 3rem;
                            }
                            .p-3 {
                                padding: 1rem;
                            }
                            .p-4 {
                                padding: 1.5rem;
                            }
                            .row {
                                display: flex;
                                flex-wrap: wrap;
                            }
                            .col-md-6 {
                                width: 50%;
                                flex: 0 0 auto;
                            }
                            .bg-light {
                                background-color: #f8f9fa;
                            }
                            .rounded {
                                border-radius: 0.375rem;
                            }
                            .d-flex {
                                display: flex;
                            }
                            .justify-content-between {
                                justify-content: space-between;
                            }
                            .small {
                                font-size: 0.875em;
                            }
                            .text-muted {
                                color: #6c757d;
                            }
                            .h3 {
                                font-size: 1.75rem;
                                margin-top: 0;
                                margin-bottom: 0.5rem;
                                font-weight: 500;
                            }
                            .h4 {
                                font-size: 1.5rem;
                                margin-top: 0;
                                margin-bottom: 0.5rem;
                                font-weight: 500;
                            }
                            .h5 {
                                font-size: 1.25rem;
                                margin-top: 0;
                                margin-bottom: 0.5rem;
                                font-weight: 500;
                            }
                            @media print {
                                @page {
                                    size: A4;
                                    margin: 10mm;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        ${printContents}
                    </body>
                </html>
            `;
            
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        });
    });
</script>
@endsection