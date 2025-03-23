@extends('layouts.app')

@section('title', 'Reçu de Transaction')
@section('page-title', 'Reçu de Transaction')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="card-title">Reçu de Transaction</h2>
            <div>
                <button id="printBtn" class="btn btn-primary me-2">
                    <i class="fas fa-print me-2"></i> Imprimer
                </button>
                <a href="{{ route('finances.transactions') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour
                </a>
            </div>
        </div>
        
        <div id="printArea" class="border rounded p-4">
            <div class="text-center mb-4 pb-3 border-bottom">
                <h1 class="h3 text-primary">{{ $parametres->nom_etablissement ?? 'SYNCHROGEST-ÉDUCATION' }}</h1>
                @if(isset($parametres->adresse) && $parametres->adresse)
                    <p class="mb-1">{{ $parametres->adresse }}</p>
                @endif
                @if(isset($parametres->telephone) && $parametres->telephone)
                    <p class="mb-1">Tél: {{ $parametres->telephone }}</p>
                @endif
                @if(isset($parametres->email) && $parametres->email)
                    <p class="mb-1">{{ $parametres->email }}</p>
                @endif
                
                <h2 class="h4 mt-4">REÇU DE {{ $transaction->type == 'recette' ? 'RECETTE' : 'DÉPENSE' }}</h2>
                <p>Année scolaire: {{ $parametres->annee_scolaire ?? '' }}</p>
            </div>
            
            <div class="text-end mb-4">
                <p class="text-primary fw-bold">N° {{ $transaction->reference ?: 'TR-'.str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p>Date: {{ $transaction->date->format('d/m/Y') }}</p>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Informations générales</h5>
                            <p class="mb-1"><strong>Type:</strong> 
                                <span class="badge bg-{{ $transaction->type == 'recette' ? 'success' : 'danger' }} rounded-pill">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </p>
                            <p class="mb-1"><strong>Catégorie:</strong> {{ $transaction->categorie->nom }}</p>
                            <p class="mb-1"><strong>Description:</strong> {{ $transaction->description }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card border-0 {{ $transaction->type == 'recette' ? 'bg-success' : 'bg-danger' }} bg-opacity-10">
                        <div class="card-body">
                            <h5 class="card-title {{ $transaction->type == 'recette' ? 'text-success' : 'text-danger' }}">Détails financiers</h5>
                            <p class="mb-1"><strong>Montant:</strong> <span class="fs-4 fw-bold">{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</span></p>
                            <p class="mb-1"><strong>Référence:</strong> {{ $transaction->reference ?: 'N/A' }}</p>
                            <p class="mb-1"><strong>Enregistré par:</strong> {{ $transaction->user->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 d-flex justify-content-between">
                <div class="border-top border-dark" style="width: 200px;">
                    <p class="text-center mt-1">Signature du payeur</p>
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
                        <title>Reçu de transaction - {{ $transaction->reference ?: 'TR-'.str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                padding: 20px;
                            }
                            .text-primary {
                                color: {{ $transaction->type == 'recette' ? '#28a745' : '#dc3545' }};
                            }
                            .text-success {
                                color: #28a745;
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
                            .badge {
                                display: inline-block;
                                padding: 0.25em 0.4em;
                                font-size: 75%;
                                font-weight: 700;
                                line-height: 1;
                                text-align: center;
                                white-space: nowrap;
                                vertical-align: baseline;
                                border-radius: 0.25rem;
                                color: white;
                            }
                            .bg-success {
                                background-color: #28a745;
                            }
                            .bg-danger {
                                background-color: #dc3545;
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
                            .pb-3 {
                                padding-bottom: 1rem;
                            }
                            .p-4 {
                                padding: 1.5rem;
                            }
                            .fs-4 {
                                font-size: 1.5rem;
                            }
                            .row {
                                display: flex;
                                flex-wrap: wrap;
                                margin-right: -15px;
                                margin-left: -15px;
                            }
                            .col-md-6 {
                                width: 50%;
                                padding-right: 15px;
                                padding-left: 15px;
                            }
                            .card {
                                position: relative;
                                display: flex;
                                flex-direction: column;
                                min-width: 0;
                                word-wrap: break-word;
                                background-color: #fff;
                                background-clip: border-box;
                                border: 1px solid rgba(0, 0, 0, 0.125);
                                border-radius: 0.25rem;
                                margin-bottom: 15px;
                            }
                            .card-body {
                                flex: 1 1 auto;
                                padding: 1.25rem;
                            }
                            .card-title {
                                margin-bottom: 0.75rem;
                            }
                            .bg-light {
                                background-color: #f8f9fa !important;
                            }
                            .bg-opacity-10 {
                                opacity: 0.1;
                            }
                            .d-flex {
                                display: flex;
                            }
                            .justify-content-between {
                                justify-content: space-between;
                            }
                            .rounded {
                                border-radius: 0.25rem;
                            }
                            .border {
                                border: 1px solid #dee2e6;
                            }
                            .small {
                                font-size: 80%;
                            }
                            .text-muted {
                                color: #6c757d;
                            }
                            .h3 {
                                font-size: 1.75rem;
                                margin-top: 0;
                                margin-bottom: 0.5rem;
                                font-weight: 500;
                                line-height: 1.2;
                            }
                            .h4 {
                                font-size: 1.5rem;
                                margin-top: 0;
                                margin-bottom: 0.5rem;
                                font-weight: 500;
                                line-height: 1.2;
                            }
                            .h5 {
                                font-size: 1.25rem;
                                margin-top: 0;
                                margin-bottom: 0.5rem;
                                font-weight: 500;
                                line-height: 1.2;
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