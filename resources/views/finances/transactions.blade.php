@extends('layouts.app')

@section('title', 'Transactions Financières')
@section('page-title', 'Gestion des Transactions')

@section('content')
<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Transactions financières</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
        <i class="fas fa-plus"></i> Nouvelle transaction
    </button>
</div>

<!-- Alert de confirmation ou d'erreur -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Filtres de recherche -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtres de recherche</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('finances.transactions') }}" method="GET">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="type" class="form-label">Type de transaction</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tous les types</option>
                        <option value="recette" {{ request('type') == 'recette' ? 'selected' : '' }}>Recettes</option>
                        <option value="depense" {{ request('type') == 'depense' ? 'selected' : '' }}>Dépenses</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="categorie_id" class="form-label">Catégorie</label>
                    <select class="form-select" id="categorie_id" name="categorie_id">
                        <option value="">Toutes les catégories</option>
                        <optgroup label="Recettes">
                            @foreach($categories->where('type', 'recette') as $categorie)
                                <option value="{{ $categorie->id }}" {{ request('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                    {{ $categorie->nom }}
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Dépenses">
                            @foreach($categories->where('type', 'depense') as $categorie)
                                <option value="{{ $categorie->id }}" {{ request('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                    {{ $categorie->nom }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="date_from" class="form-label">Date début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="date_to" class="form-label">Date fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tableau des transactions -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Liste des transactions</h6>
        
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-download"></i> Exporter
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Imprimer</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        @if($transactions->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Aucune transaction trouvée pour les critères spécifiés.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="transactionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th>Référence</th>
                            <th>Montant</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->type == 'recette' ? 'success' : 'danger' }} rounded-pill">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->categorie->nom }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->reference ?: 'N/A' }}</td>
                                <td class="text-end">{{ number_format($transaction->montant, 0, ',', ' ') }} FCFA</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info view-transaction-btn" data-transaction-id="{{ $transaction->id }}" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning edit-transaction-btn" data-transaction-id="{{ $transaction->id }}" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-transaction-btn" data-transaction-id="{{ $transaction->id }}" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total:</th>
                            <th class="text-end">
                                {{ number_format($transactions->sum('montant'), 0, ',', ' ') }} FCFA
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $transactions->firstItem() ?? 0 }} à {{ $transactions->lastItem() ?? 0 }} sur {{ $transactions->total() }} transactions
                </div>
                <div>
                    {{ $transactions->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Ajouter Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addTransactionModalLabel">Ajouter une transaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('finances.ajouterTransaction') }}" method="POST" id="addTransactionForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type de transaction <span class="text-danger">*</span></label>
                            <select class="form-select" id="transaction_type" name="type" required>
                                <option value="">-- Sélectionner un type --</option>
                                <option value="recette">Recette</option>
                                <option value="depense">Dépense</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="categorie_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select" id="transaction_categorie_id" name="categorie_id" required disabled>
                                <option value="">-- Sélectionner d'abord un type --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="transaction_date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="montant" class="form-label">Montant <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="transaction_montant" name="montant" min="0" step="1" required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="transaction_description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" class="form-control" id="transaction_reference" name="reference" placeholder="N° Facture, Reçu, etc.">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="addTransactionSubmitBtn">
                        <i class="fas fa-save me-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Voir Transaction Modal -->
<div class="modal fade" id="viewTransactionModal" tabindex="-1" aria-labelledby="viewTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewTransactionModalLabel">Détails de la transaction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
                
                <div id="transactionDetails" style="display: none;">
                    <div class="mb-4 text-center">
                        <span class="badge bg-primary fs-6 p-2" id="viewTransactionType"></span>
                    </div>
                    
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="35%">Date</th>
                                <td id="viewTransactionDate"></td>
                            </tr>
                            <tr>
                                <th>Catégorie</th>
                                <td id="viewTransactionCategorie"></td>
                            </tr>
                            <tr>
                                <th>Montant</th>
                                <td id="viewTransactionMontant"></td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td id="viewTransactionDescription"></td>
                            </tr>
                            <tr>
                                <th>Référence</th>
                                <td id="viewTransactionReference"></td>
                            </tr>
                            <tr>
                                <th>Enregistré par</th>
                                <td id="viewTransactionUser"></td>
                            </tr>
                            <tr>
                                <th>Date d'enregistrement</th>
                                <td id="viewTransactionCreatedAt"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="printTransactionBtn">
                    <i class="fas fa-print me-2"></i> Imprimer
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation de suppression Modal -->
<div class="modal fade" id="deleteTransactionModal" tabindex="-1" aria-labelledby="deleteTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteTransactionModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                    <div>
                        <p>Êtes-vous sûr de vouloir supprimer cette transaction ? Cette action est irréversible.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form id="deleteTransactionForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du type de transaction et catégories associées
        const typeSelect = document.getElementById('transaction_type');
        const categorieSelect = document.getElementById('transaction_categorie_id');
        
        // Données des catégories
        const categories = {
            recette: [
                @foreach($categories->where('type', 'recette') as $categorie)
                    { id: {{ $categorie->id }}, nom: "{{ $categorie->nom }}" },
                @endforeach
            ],
            depense: [
                @foreach($categories->where('type', 'depense') as $categorie)
                    { id: {{ $categorie->id }}, nom: "{{ $categorie->nom }}" },
                @endforeach
            ]
        };
        
        // Mise à jour des catégories selon le type sélectionné
        typeSelect.addEventListener('change', function() {
            const type = this.value;
            
            // Réinitialisation
            categorieSelect.innerHTML = '';
            categorieSelect.disabled = true;
            
            if (!type) {
                categorieSelect.innerHTML = '<option value="">-- Sélectionner d\'abord un type --</option>';
                return;
            }
            
            categorieSelect.disabled = false;
            
            // Option par défaut
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '-- Sélectionner une catégorie --';
            categorieSelect.appendChild(defaultOption);
            
            // Ajouter les catégories correspondantes
            if (categories[type] && categories[type].length > 0) {
                categories[type].forEach(categorie => {
                    const option = document.createElement('option');
                    option.value = categorie.id;
                    option.textContent = categorie.nom;
                    categorieSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Aucune catégorie disponible';
                option.disabled = true;
                categorieSelect.appendChild(option);
            }
        });
        
        // Voir les détails d'une transaction
        const viewTransactionModal = new bootstrap.Modal(document.getElementById('viewTransactionModal'));
        const deleteTransactionModal = new bootstrap.Modal(document.getElementById('deleteTransactionModal'));
        
        document.querySelectorAll('.view-transaction-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const transactionId = this.getAttribute('data-transaction-id');
                
                // TODO: Remplacer par une requête AJAX réelle
                // Pour l'instant, nous simulons des données
                setTimeout(() => {
                    document.querySelector('#viewTransactionModal .spinner-border').style.display = 'none';
                    document.getElementById('transactionDetails').style.display = 'block';
                    
                    // Simuler des données
                    document.getElementById('viewTransactionType').textContent = 'Recette';
                    document.getElementById('viewTransactionType').className = 'badge bg-success fs-6 p-2';
                    document.getElementById('viewTransactionDate').textContent = '01/01/2024';
                    document.getElementById('viewTransactionCategorie').textContent = 'Scolarité';
                    document.getElementById('viewTransactionMontant').textContent = '150 000 FCFA';
                    document.getElementById('viewTransactionDescription').textContent = 'Paiement scolarité élève XYZ';
                    document.getElementById('viewTransactionReference').textContent = 'REF-2024-001';
                    document.getElementById('viewTransactionUser').textContent = 'Admin';
                    document.getElementById('viewTransactionCreatedAt').textContent = '01/01/2024 08:30';
                }, 500);
                
                viewTransactionModal.show();
            });
        });
        
        // Supprimer une transaction
        document.querySelectorAll('.delete-transaction-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const transactionId = this.getAttribute('data-transaction-id');
                document.getElementById('deleteTransactionForm').action = `/finances/transactions/${transactionId}`;
                deleteTransactionModal.show();
            });
        });
        
        // Validation du formulaire d'ajout de transaction
        document.getElementById('addTransactionForm').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                alert('Veuillez remplir tous les champs obligatoires.');
            } else {
                document.getElementById('addTransactionSubmitBtn').disabled = true;
                document.getElementById('addTransactionSubmitBtn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...';
            }
            
            this.classList.add('was-validated');
        });
        
        // Imprimer la transaction
        document.getElementById('printTransactionBtn').addEventListener('click', function() {
            const printContent = document.getElementById('transactionDetails').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = `
                <html>
                <head>
                    <title>Détails de la transaction</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .badge { 
                            display: inline-block;
                            padding: 0.5em 1em;
                            font-size: 1.25em;
                            font-weight: 700;
                            color: white;
                            text-align: center;
                            border-radius: 0.25em;
                        }
                        .bg-success { background-color: #28a745; }
                        .bg-danger { background-color: #dc3545; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .text-center { text-align: center; }
                        @media print {
                            @page { margin: 1cm; }
                        }
                    </style>
                </head>
                <body>
                    <h1 class="text-center">Détails de la transaction</h1>
                    ${printContent}
                </body>
                </html>
            `;
            
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        });
    });
</script>
@endsection