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
                    <select class="form-select @error('classe_id') is-invalid @enderror" name="classe_id" id="classeSelect" required>
                        <option value="">-- Sélectionner une classe --</option>
                        @foreach($niveaux as $niveau)
                            <optgroup label="{{ $niveau->nom }}">
                                @foreach($niveau->classes as $classe)
                                    <option value="{{ $classe->id }}" {{ session('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
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
            
            <div class="d-flex justify-content-end mt-4">
                <button type="button" id="previewBtn" class="btn btn-secondary me-2" disabled>
                    <span class="material-icons">visibility</span> Aperçu
                </button>
                
                <button type="submit" id="importBtn" class="btn btn-success" disabled>
                    <span class="material-icons">upload</span> Importer
                </button>
            </div>
        </form>
        
        <div id="previewContainer" style="display: none;" class="mt-4">
            <h3 class="mb-3">Aperçu des données</h3>
            
            <div class="card mb-3">
                <div class="card-body bg-light">
                    <div class="d-flex align-items-center">
                        <span class="material-icons text-primary me-2">info</span>
                        <div>
                            <p class="mb-0">Classe sélectionnée: <strong id="selectedClassInfo">{{ session('niveau_nom') }} - {{ session('classe_nom') }}</strong></p>
                            <p class="mb-0" id="previewInfo"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="previewTable">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <button type="button" id="processImportBtn" class="btn btn-primary">
                    <span class="material-icons">save</span> Enregistrer les élèves
                </button>
            </div>
        </div>
        
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

<!-- Modal de confirmation -->
<div class="modal fade" id="confirmImportModal" tabindex="-1" aria-labelledby="confirmImportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmImportModalLabel">Confirmation d'importation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Vous êtes sur le point d'importer <span id="eleveCount" class="fw-bold"></span> élèves dans la classe <span id="classeInfo" class="fw-bold"></span>.</p>
                <p>Cette action est irréversible. Voulez-vous continuer ?</p>
                
                <div class="form-group mt-3">
                    <label>Après l'importation, aller à:</label>
                    <select id="redirectSelect" class="form-select">
                        <option value="eleves">Liste des élèves</option>
                        <option value="parametres">Paramètres</option>
                        <option value="dashboard">Tableau de bord</option>
                        <option value="niveaux">Niveaux et Classes</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="finalImportBtn">
                    <span class="material-icons">check</span> Confirmer l'importation
                </button>
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
        const processImportBtn = document.getElementById('processImportBtn');
        const confirmImportModal = new bootstrap.Modal(document.getElementById('confirmImportModal'));
        const eleveCount = document.getElementById('eleveCount');
        const classeInfo = document.getElementById('classeInfo');
        const finalImportBtn = document.getElementById('finalImportBtn');
        const selectedClassInfo = document.getElementById('selectedClassInfo');
        const classeSelect = document.querySelector('select[name="classe_id"]');
        const redirectSelect = document.getElementById('redirectSelect');
        
        let fileData = null;
        let processedData = null;
        
        // Mettre à jour les informations de classe
        classeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (this.value) {
                const optgroup = selectedOption.parentNode;
                const niveauNom = optgroup.label;
                const classeNom = selectedOption.textContent;
                selectedClassInfo.textContent = `${niveauNom} - ${classeNom}`;
            } else {
                selectedClassInfo.textContent = '';
            }
        });
        
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
                previewContainer.style.display = 'block';
            }
        });
        
        // Gérer le bouton de traitement de l'importation
        processImportBtn.addEventListener('click', function() {
            if (fileData && classeSelect.value) {
                // Préparer les données pour l'importation
                processedData = prepareDataForImport(fileData);
                
                // Mettre à jour les informations de confirmation
                eleveCount.textContent = processedData.length;
                const selectedOption = classeSelect.options[classeSelect.selectedIndex];
                const optgroup = selectedOption.parentNode;
                classeInfo.textContent = `${optgroup.label} - ${selectedOption.textContent}`;
                
                // Afficher la modal de confirmation
                confirmImportModal.show();
            } else {
                alert('Veuillez sélectionner une classe et un fichier valide.');
            }
        });
        
        // Gérer le bouton de confirmation finale
        finalImportBtn.addEventListener('click', function() {
            if (processedData && classeSelect.value) {
                // Désactiver le bouton pour éviter les clics multiples
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Importation en cours...';
                
                // Récupérer la destination après importation
                const redirectTo = redirectSelect.value;
                
                // Envoyer les données au serveur
                fetch('{{ route("inscriptions.saveImportedData") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        eleves: processedData,
                        redirectTo: redirectTo
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur serveur: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    confirmImportModal.hide();
                    
                    if (data.success) {
                        alert(data.message);
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    } else {
                        alert('Erreur: ' + data.message);
                        finalImportBtn.disabled = false;
                        finalImportBtn.innerHTML = '<span class="material-icons">check</span> Confirmer l\'importation';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de l\'importation. Veuillez réessayer.');
                    finalImportBtn.disabled = false;
                    finalImportBtn.innerHTML = '<span class="material-icons">check</span> Confirmer l\'importation';
                    confirmImportModal.hide();
                });
            }
        });
        
        // Lire le fichier Excel
        function readFile(file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array', dateNF: 'DD/MM/YYYY' });
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
            const rows = data.slice(1, Math.min(11, data.length)); // Afficher les 10 premières lignes ou moins
            
            // Construire le tableau d'en-tête
            let headerHtml = '<tr>';
            headers.forEach(header => {
                headerHtml += `<th>${header || 'Sans titre'}</th>`;
            });
            headerHtml += '</tr>';
            
            // Construire les lignes de données
            let rowsHtml = '';
            rows.forEach(row => {
                rowsHtml += '<tr>';
                headers.forEach((_, index) => {
                    rowsHtml += `<td>${row[index] !== undefined ? row[index] : ''}</td>`;
                });
                rowsHtml += '</tr>';
            });
            
            // Mettre à jour le tableau
            previewTable.querySelector('thead').innerHTML = headerHtml;
            previewTable.querySelector('tbody').innerHTML = rowsHtml;
            
            // Afficher les informations
            previewInfo.textContent = `Affichage de ${rows.length} lignes sur ${data.length - 1} au total.`;
        }
        
        // Préparer les données pour l'importation
        function prepareDataForImport(data) {
            if (data.length <= 1) return [];
            
            const headers = data[0];
            const rows = data.slice(1);
            const classeId = classeSelect.value;
            const result = [];
            
            // Normaliser les en-têtes et trouver les indices
            const headersLower = headers.map(h => (h || '').toString().toLowerCase().trim());
            
            const ineIndex = findColumn(headersLower, ['ine', 'ien', 'identifiant']);
            const prenomIndex = findColumn(headersLower, ['prénom', 'prenom', 'prénoms', 'prenoms']);
            const nomIndex = findColumn(headersLower, ['nom']);
            const sexeIndex = findColumn(headersLower, ['sexe', 'genre']);
            const dateNaissanceIndex = findColumn(headersLower, ['date de naissance', 'date_naissance']);
            const lieuNaissanceIndex = findColumn(headersLower, ['lieu de naissance', 'lieu_naissance']);
            const extraitIndex = findColumn(headersLower, ['existence extrait', 'extrait']);
            const motifIndex = findColumn(headersLower, ['motif d\'entrée', 'motif d\'entré', 'motif_entre', 'motif']);
            const statutIndex = findColumn(headersLower, ['statut']);
            
            // Vérifier les colonnes obligatoires
            if (ineIndex === -1 || prenomIndex === -1 || nomIndex === -1) {
                alert('Colonnes obligatoires manquantes: INE, Prénom et Nom sont requis.');
                return [];
            }
            
            // Traiter les lignes
            rows.forEach(row => {
                if (row.length === 0) return; // Ignorer les lignes vides
                
                if (row[ineIndex]) { // Vérifier que l'INE existe
                    try {
                        const eleve = {
                            ine: row[ineIndex],
                            prenom: row[prenomIndex] || '',
                            nom: row[nomIndex] || '',
                            sexe: sexeIndex !== -1 ? row[sexeIndex] || 'M' : 'M',
                            date_naissance: dateNaissanceIndex !== -1 ? formatDate(row[dateNaissanceIndex]) : new Date().toISOString().split('T')[0],
                            lieu_naissance: lieuNaissanceIndex !== -1 ? row[lieuNaissanceIndex] || '' : '',
                            existence_extrait: extraitIndex !== -1 ? (row[extraitIndex] === 'Oui') : false,
                            classe_id: classeId,
                            motif_entre: motifIndex !== -1 ? row[motifIndex] || '' : '',
                            statut: statutIndex !== -1 ? row[statutIndex] || 'Nouveau' : 'Nouveau'
                        };
                        
                        result.push(eleve);
                    } catch (e) {
                        console.error("Erreur lors du traitement d'une ligne:", e);
                    }
                }
            });
            
            return result;
        }
        
        // Fonction auxiliaire pour trouver une colonne
        function findColumn(headers, possibleNames) {
            for (const name of possibleNames) {
                const index = headers.findIndex(h => h.includes(name));
                if (index !== -1) return index;
            }
            return -1;
        }
        
        // Formater la date au format YYYY-MM-DD
        function formatDate(dateValue) {
            if (!dateValue) return new Date().toISOString().split('T')[0];
            
            try {
                // Si c'est une chaîne de type DD/MM/YYYY
                if (typeof dateValue === 'string') {
                    const parts = dateValue.split(/[\/\-\.]/);
                    if (parts.length === 3) {
                        // Format DD/MM/YYYY
                        if (parseInt(parts[0]) <= 31) {
                            return `${parts[2].padStart(4, '20')}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                        }
                        // Format YYYY/MM/DD
                        else {
                            return `${parts[0]}-${parts[1].padStart(2, '0')}-${parts[2].padStart(2, '0')}`;
                        }
                    }
                }
                
                // Essayer de créer un objet Date
                const date = new Date(dateValue);
                if (!isNaN(date.getTime())) {
                    return date.toISOString().split('T')[0];
                }
            } catch (e) {
                console.error("Erreur de formatage de date:", e);
            }
            
            // Par défaut
            return new Date().toISOString().split('T')[0];
        }
        
        // Réinitialiser le champ de fichier
        function resetFileInput() {
            fileInfo.textContent = '';
            previewBtn.disabled = true;
            importBtn.disabled = true;
            previewContainer.style.display = 'none';
            fileData = null;
            processedData = null;
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