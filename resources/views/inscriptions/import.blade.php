@extends('layouts.app')

@section('title', 'Importation des Élèves')
@section('page-title', 'Importation des listes d\'élèves')

@section('content')
<div class="card">
    <div class="card-body">
        <h2 class="card-title mb-4">Importer une liste d'élèves</h2>
        
        <form action="{{ route('inscriptions.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Sélectionner une classe</label>
                    <select class="form-select @error('classe_id') is-invalid @enderror" name="classe_id" required>
                        <option value="">-- Sélectionner une classe --</option>
                        @foreach($niveaux as $niveau)
                            <optgroup label="{{ $niveau->nom }}">
                                @foreach($niveau->classes as $classe)
                                    <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('classe_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Fichier Excel (XLSX, XLS, CSV)</label>
                    <div class="input-group">
                        <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" id="fileInput" accept=".xlsx,.xls,.csv" required>
                        <button class="btn btn-outline-secondary" type="button" id="clearFileBtn">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text" id="fileInfo"></div>
                </div>
            </div>
            
            <div id="previewContainer" style="display: none;">
                <h3 class="mb-3">Aperçu des données</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="previewTable">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
                <p class="text-muted mt-2" id="previewInfo"></p>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="button" id="previewBtn" class="btn btn-secondary me-2" disabled>
                    <span class="material-icons">visibility</span> Aperçu
                </button>
                
                <button type="submit" id="importBtn" class="btn btn-success" disabled>
                    <span class="material-icons">upload</span> Importer
                </button>
            </div>
        </form>
        
        <div class="mt-5 p-4 bg-light rounded">
            <h3 class="mb-3">Instructions</h3>
            <p>Veuillez préparer votre fichier Excel avec les colonnes suivantes:</p>
            <ul class="list-group mb-3">
                <li class="list-group-item">IEN (ou INE) - Identifiant unique de l'élève</li>
                <li class="list-group-item">Prénom(s) - Prénom(s) de l'élève</li>
                <li class="list-group-item">Nom - Nom de famille de l'élève</li>
                <li class="list-group-item">Sexe - "M" pour Masculin, "F" pour Féminin</li>
                <li class="list-group-item">Date de Naissance - Format JJ/MM/AAAA</li>
                <li class="list-group-item">Lieu de Naissance - Ville/lieu de naissance</li>
                <li class="list-group-item">Existence extrait - "Oui" ou "Non"</li>
                <li class="list-group-item">Motif d'entrée - Raison de l'inscription</li>
                <li class="list-group-item">Statut - "Nouveau", "Ancien" ou "Redoublant"</li>
            </ul>
            
            <div class="alert alert-warning">
                <strong>Note:</strong> Assurez-vous que les identifiants (INE) sont uniques pour éviter les doublons.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const clearFileBtn = document.getElementById('clearFileBtn');
        const fileInfo = document.getElementById('fileInfo');
        const previewBtn = document.getElementById('previewBtn');
        const importBtn = document.getElementById('importBtn');
        const previewContainer = document.getElementById('previewContainer');
        const previewTable = document.getElementById('previewTable');
        const previewInfo = document.getElementById('previewInfo');
        
        let fileData = null;
        
        // Gérer le changement de fichier
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                fileInfo.textContent = `Fichier sélectionné: ${file.name} (${formatFileSize(file.size)})`;
                previewBtn.disabled = false;
                importBtn.disabled = false;
                readFile(file);
            } else {
                resetFileInput();
            }
        });
        
        // Gérer le bouton d'effacement
        clearFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            resetFileInput();
        });
        
        // Gérer le bouton d'aperçu
        previewBtn.addEventListener('click', function() {
            if (fileData) {
                showPreview(fileData);
            }
        });
        
        // Lire le fichier Excel
        function readFile(file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                    
                    if (jsonData.length > 0) {
                        fileData = jsonData;
                        previewBtn.disabled = false;
                    } else {
                        alert('Le fichier semble être vide.');
                        resetFileInput();
                    }
                } catch (error) {
                    console.error('Erreur lors de la lecture du fichier:', error);
                    alert('Erreur lors de la lecture du fichier. Assurez-vous qu\'il s\'agit d\'un fichier Excel valide.');
                    resetFileInput();
                }
            };
            
            reader.onerror = function() {
                alert('Erreur lors de la lecture du fichier.');
                resetFileInput();
            };
            
            reader.readAsArrayBuffer(file);
        }
        
        // Afficher l'aperçu des données
        function showPreview(data) {
            if (data.length <= 1) {
                alert('Le fichier ne contient pas assez de données.');
                return;
            }
            
            const headers = data[0];
            const rows = data.slice(1, 11); // Afficher les 10 premières lignes
            
            // Construire le tableau d'en-tête
            let headerHtml = '<tr>';
            headers.forEach(header => {
                headerHtml += `<th>${header}</th>`;
            });
            headerHtml += '</tr>';
            
            // Construire les lignes de données
            let rowsHtml = '';
            rows.forEach(row => {
                rowsHtml += '<tr>';
                headers.forEach((_, index) => {
                    rowsHtml += `<td>${row[index] || ''}</td>`;
                });
                rowsHtml += '</tr>';
            });
            
            // Mettre à jour le tableau
            previewTable.querySelector('thead').innerHTML = headerHtml;
            previewTable.querySelector('tbody').innerHTML = rowsHtml;
            
            // Afficher les informations
            previewInfo.textContent = `Affichage de ${rows.length} lignes sur ${data.length - 1} au total.`;
            
            // Afficher le conteneur
            previewContainer.style.display = 'block';
        }
        
        // Réinitialiser le champ de fichier
        function resetFileInput() {
            fileInfo.textContent = '';
            previewBtn.disabled = true;
            importBtn.disabled = true;
            previewContainer.style.display = 'none';
            fileData = null;
        }
        
        // Formater la taille du fichier
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    });
</script>
@endsection