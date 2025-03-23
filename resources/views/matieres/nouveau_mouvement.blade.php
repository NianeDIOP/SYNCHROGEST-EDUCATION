@extends('layouts.app')

@section('title', 'Nouveau Mouvement de Stock')
@section('page-title', 'Enregistrer un mouvement de stock')

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Détails du mouvement</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('matieres.nouveauMouvement') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="article_id" class="form-label">Article <span class="text-danger">*</span></label>
                    <select class="form-select @error('article_id') is-invalid @enderror" id="article_id" name="article_id" required>
                        <option value="">-- Sélectionner un article --</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}" 
                                    data-unite="{{ $article->unite_mesure }}"
                                    data-stock="{{ $article->quantite_stock }}"
                                    {{ request('article_id') == $article->id ? 'selected' : (old('article_id') == $article->id ? 'selected' : '') }}>
                                {{ $article->designation }} (Stock: {{ $article->quantite_stock }} {{ $article->unite_mesure }})
                            </option>
                        @endforeach
                    </select>
                    @error('article_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="type_mouvement" class="form-label">Type de mouvement <span class="text-danger">*</span></label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="type_mouvement" id="type_entree" value="entrée" {{ request('type_mouvement') == 'entrée' ? 'checked' : (old('type_mouvement', 'entrée') == 'entrée' ? 'checked' : '') }} required>
                        <label class="btn btn-outline-success" for="type_entree">Entrée</label>
                        
                        <input type="radio" class="btn-check" name="type_mouvement" id="type_sortie" value="sortie" {{ request('type_mouvement') == 'sortie' ? 'checked' : (old('type_mouvement') == 'sortie' ? 'checked' : '') }} required>
                        <label class="btn btn-outline-danger" for="type_sortie">Sortie</label>
                    </div>
                    @error('type_mouvement')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="quantite" class="form-label">Quantité <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control @error('quantite') is-invalid @enderror" id="quantite" name="quantite" value="{{ old('quantite') }}" step="0.01" min="0.01" required>
                        <span class="input-group-text" id="unite-mesure">Unité</span>
                    </div>
                    <div id="stock-dispo" class="form-text"></div>
                    @error('quantite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="date_mouvement" class="form-label">Date du mouvement <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date_mouvement') is-invalid @enderror" id="date_mouvement" name="date_mouvement" value="{{ old('date_mouvement', date('Y-m-d')) }}" required>
                    @error('date_mouvement')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="motif" class="form-label">Motif <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('motif') is-invalid @enderror" id="motif" name="motif" rows="2" required>{{ old('motif') }}</textarea>
                    @error('motif')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="reference_document" class="form-label">Référence document</label>
                    <input type="text" class="form-control @error('reference_document') is-invalid @enderror" id="reference_document" name="reference_document" value="{{ old('reference_document') }}" placeholder="ex: BON-001, FACT-123, etc.">
                    @error('reference_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3" id="fournisseur-container">
                    <label for="fournisseur_id" class="form-label">Fournisseur</label>
                    <select class="form-select @error('fournisseur_id') is-invalid @enderror" id="fournisseur_id" name="fournisseur_id">
                        <option value="">-- Sélectionner un fournisseur --</option>
                        @foreach($fournisseurs as $fournisseur)
                            <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                {{ $fournisseur->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('fournisseur_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <a href="{{ route('matieres.nouveauFournisseur') }}" target="_blank">Ajouter un nouveau fournisseur</a>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3" id="destinataire-container" style="display: none;">
                    <label for="destinataire" class="form-label">Destinataire</label>
                    <input type="text" class="form-control @error('destinataire') is-invalid @enderror" id="destinataire" name="destinataire" value="{{ old('destinataire') }}" placeholder="ex: Classe 3ème A, Salle des profs, etc.">
                    @error('destinataire')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('matieres.mouvements') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary" id="submit-btn">
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
        const articleSelect = document.getElementById('article_id');
        const typeEntree = document.getElementById('type_entree');
        const typeSortie = document.getElementById('type_sortie');
        const uniteMesure = document.getElementById('unite-mesure');
        const stockDispo = document.getElementById('stock-dispo');
        const fournisseurContainer = document.getElementById('fournisseur-container');
        const destinataireContainer = document.getElementById('destinataire-container');
        const quantiteInput = document.getElementById('quantite');
        const submitBtn = document.getElementById('submit-btn');
        
        // Initialiser l'unité de mesure et le stock disponible
        function updateArticleInfo() {
            if (articleSelect.value) {
                const selectedOption = articleSelect.options[articleSelect.selectedIndex];
                const unite = selectedOption.dataset.unite;
                const stock = parseFloat(selectedOption.dataset.stock);
                
                uniteMesure.textContent = unite;
                
                if (typeSortie.checked) {
                    stockDispo.textContent = `Stock disponible: ${stock} ${unite}`;
                    quantiteInput.max = stock;
                    
                    // Vérifier si le stock est suffisant
                    if (stock <= 0) {
                        stockDispo.classList.add('text-danger');
                        stockDispo.classList.remove('text-muted');
                        stockDispo.textContent = `Stock insuffisant (${stock} ${unite})`;
                        submitBtn.disabled = true;
                    } else {
                        stockDispo.classList.remove('text-danger');
                        stockDispo.classList.add('text-muted');
                        submitBtn.disabled = false;
                    }
                } else {
                    stockDispo.textContent = '';
                    quantiteInput.max = '';
                    submitBtn.disabled = false;
                }
            } else {
                uniteMesure.textContent = 'Unité';
                stockDispo.textContent = '';
            }
        }
        
        // Mettre à jour les champs selon le type de mouvement
        function updateMouvementType() {
            if (typeEntree.checked) {
                fournisseurContainer.style.display = 'block';
                destinataireContainer.style.display = 'none';
            } else {
                fournisseurContainer.style.display = 'none';
                destinataireContainer.style.display = 'block';
            }
            
            updateArticleInfo();
        }
        
        // Événements
        articleSelect.addEventListener('change', updateArticleInfo);
        typeEntree.addEventListener('change', updateMouvementType);
        typeSortie.addEventListener('change', updateMouvementType);
        
        // Initialisation
        updateMouvementType();
    });
</script>
@endsection