@extends('layouts.app')

@section('title', 'Rapports')
@section('page-title', 'Génération de Rapports')

@section('content')
<div class="row">
    <!-- Rapports de stock -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rapport d'état du stock</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('matieres.genererRapport') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="type_rapport" value="stock">
                    
                    <div class="mb-3">
                        <label for="categorie_id" class="form-label">Catégorie</label>
                        <select class="form-select" id="categorie_id" name="categorie_id">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Sélectionnez une catégorie ou laissez vide pour toutes les catégories.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">État du stock</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etat_stock" id="etat_stock_all" value="all" checked>
                            <label class="form-check-label" for="etat_stock_all">
                                Tous les articles
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etat_stock" id="etat_stock_alerte" value="alerte">
                            <label class="form-check-label" for="etat_stock_alerte">
                                Articles en alerte uniquement
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="etat_stock" id="etat_stock_rupture" value="rupture">
                            <label class="form-check-label" for="etat_stock_rupture">
                                Articles en rupture uniquement
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Format de sortie</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="format" id="format_pdf" value="pdf" checked>
                                <label class="form-check-label" for="format_pdf">
                                    PDF
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="format_excel" value="excel">
                                <label class="form-check-label" for="format_excel">
                                    Excel
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-download me-1"></i> Générer le rapport
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Rapports de mouvements -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rapport de mouvements</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('matieres.genererRapport') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="type_rapport" value="mouvements">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_debut" class="form-label">Date début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="date_fin" class="form-label">Date fin</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_mouvement" class="form-label">Type de mouvement</label>
                        <select class="form-select" id="type_mouvement" name="type_mouvement">
                            <option value="">Tous les mouvements</option>
                            <option value="entrée">Entrées uniquement</option>
                            <option value="sortie">Sorties uniquement</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="article_id" class="form-label">Article</label>
                        <select class="form-select" id="article_id" name="article_id">
                            <option value="">Tous les articles</option>
                            @foreach($articles as $article)
                                <option value="{{ $article->id }}">{{ $article->designation }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Format de sortie</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="format" id="format_pdf_mouvements" value="pdf" checked>
                                <label class="form-check-label" for="format_pdf_mouvements">
                                    PDF
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="format_excel_mouvements" value="excel">
                                <label class="form-check-label" for="format_excel_mouvements">
                                    Excel
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-download me-1"></i> Générer le rapport
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Rapport des fournisseurs -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rapport des fournisseurs</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('matieres.genererRapport') }}" method="POST" target="_blank">
                    @csrf
                    <input type="hidden" name="type_rapport" value="fournisseurs">
                    
                    <div class="mb-3">
                        <label for="fournisseur_id" class="form-label">Fournisseur</label>
                        <select class="form-select" id="fournisseur_id" name="fournisseur_id">
                            <option value="">Tous les fournisseurs</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Inclure les transactions</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="inclure_transactions" name="inclure_transactions" value="1" checked>
                            <label class="form-check-label" for="inclure_transactions">
                                Inclure l'historique des transactions
                            </label>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_debut_fournisseur" class="form-label">Date début (optionnel)</label>
                            <input type="date" class="form-control" id="date_debut_fournisseur" name="date_debut">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="date_fin_fournisseur" class="form-label">Date fin (optionnel)</label>
                            <input type="date" class="form-control" id="date_fin_fournisseur" name="date_fin">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Format de sortie</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="format" id="format_pdf_fournisseurs" value="pdf" checked>
                                <label class="form-check-label" for="format_pdf_fournisseurs">
                                    PDF
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="format_excel_fournisseurs" value="excel">
                                <label class="form-check-label" for="format_excel_fournisseurs">
                                    Excel
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-download me-1"></i> Générer le rapport
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Instructions -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Instructions</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Les rapports générés seront téléchargés automatiquement.
                </div>
                
                <h5 class="mt-3">Types de rapports disponibles :</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item">
                        <strong>Rapport d'état du stock</strong> - Affiche l'état actuel du stock avec possibilité de filtrer par catégorie et état.
                    </li>
                    <li class="list-group-item">
                        <strong>Rapport de mouvements</strong> - Liste les entrées et sorties de stock sur une période donnée.
                    </li>
                    <li class="list-group-item">
                        <strong>Rapport des fournisseurs</strong> - Présente les informations sur les fournisseurs et leurs transactions éventuelles.
                    </li>
                </ul>
                
                <h5>Astuces :</h5>
                <ul>
                    <li>Pour un inventaire complet, générez un rapport d'état du stock sans filtre.</li>
                    <li>Pour un suivi des entrées/sorties mensuel, utilisez le rapport de mouvements avec les dates correspondantes.</li>
                    <li>Pour la préparation des commandes, filtrez les articles en alerte ou en rupture.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser la date de début et de fin par défaut (mois courant)
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        // Formater les dates en YYYY-MM-DD
        const formatDate = (date) => {
            const d = new Date(date);
            let month = '' + (d.getMonth() + 1);
            let day = '' + d.getDate();
            const year = d.getFullYear();
            
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            
            return [year, month, day].join('-');
        };
        
        document.getElementById('date_debut').value = formatDate(firstDay);
        document.getElementById('date_fin').value = formatDate(lastDay);
    });
</script>
@endsection