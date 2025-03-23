@extends('layouts.app')

@section('title', 'Gestion des Élèves')
@section('page-title', 'Gestion des Élèves')

@section('content')
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Liste des élèves</h6>
        <a href="{{ route('inscriptions.import') }}" class="btn btn-primary">
            <i class="fas fa-file-upload me-2"></i> Importer des élèves
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('inscriptions.eleves') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="INE, nom ou prénom...">
                    </div>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="classe_id" class="form-label">Classe</label>
                    <select class="form-select" id="classe_id" name="classe_id">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->niveau->nom }} - {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="niveau_id" class="form-label">Niveau</label>
                    <select class="form-select" id="niveau_id" name="niveau_id">
                        <option value="">Tous les niveaux</option>
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau->id }}" {{ request('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                {{ $niveau->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select class="form-select" id="statut" name="statut">
                        <option value="">Tous les statuts</option>
                        <option value="Nouveau" {{ request('statut') == 'Nouveau' ? 'selected' : '' }}>Nouveau</option>
                        <option value="Ancien" {{ request('statut') == 'Ancien' ? 'selected' : '' }}>Ancien</option>
                        <option value="Redoublant" {{ request('statut') == 'Redoublant' ? 'selected' : '' }}>Redoublant</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="inscription" class="form-label">Inscription</label>
                    <select class="form-select" id="inscription" name="inscription">
                        <option value="">Tous</option>
                        <option value="inscrits" {{ request('inscription') == 'inscrits' ? 'selected' : '' }}>Inscrits</option>
                        <option value="non_inscrits" {{ request('inscription') == 'non_inscrits' ? 'selected' : '' }}>Non inscrits</option>
                    </select>
                </div>
                
                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>

        @if($eleves->isEmpty())
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3 fa-2x"></i>
                    <div>
                        <h5 class="mb-1">Aucun élève trouvé</h5>
                        <p class="mb-0">Veuillez importer des élèves ou ajuster vos critères de recherche.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>INE</th>
                            <th>Nom & Prénom</th>
                            <th>Sexe</th>
                            <th>Date de naissance</th>
                            <th>Classe</th>
                            <th>Statut</th>
                            <th>Inscription</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eleves as $eleve)
                            <tr>
                                <td>{{ $eleve->ine }}</td>
                                <td>{{ $eleve->nom }} {{ $eleve->prenom }}</td>
                                <td>{{ $eleve->sexe }}</td>
                                <td>{{ $eleve->date_naissance->format('d/m/Y') }}</td>
                                <td>{{ $eleve->classe->niveau->nom }} - {{ $eleve->classe->nom }}</td>
                                <td>
                                    <span class="badge bg-{{ $eleve->statut === 'Nouveau' ? 'success' : ($eleve->statut === 'Ancien' ? 'primary' : 'warning') }} rounded-pill">
                                        {{ $eleve->statut }}
                                    </span>
                                </td>
                                <td>
                                    @if($eleve->estInscrit)
                                        <span class="badge bg-success rounded-pill">Inscrit</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill">Non inscrit</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        @if(!$eleve->estInscrit)
                                            <a href="{{ route('inscriptions.nouvelle', ['ine' => $eleve->ine]) }}" class="btn btn-sm btn-primary" title="Inscrire">
                                                <i class="fas fa-user-plus"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('inscriptions.recu', ['id' => $eleve->derniere_inscription_id]) }}" class="btn btn-sm btn-success" title="Voir reçu">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-info view-eleve-btn" data-eleve-id="{{ $eleve->id }}" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-eleve-btn" data-eleve-id="{{ $eleve->id }}" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $eleves->firstItem() ?? 0 }} à {{ $eleves->lastItem() ?? 0 }} sur {{ $eleves->total() }} élèves
                </div>
                <div>
                    <ul class="pagination">
                        <!-- First Page Link -->
                        <li class="page-item {{ $eleves->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $eleves->url(1) }}" aria-label="First">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                        
                        <!-- Previous Page Link -->
                        <li class="page-item {{ $eleves->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $eleves->previousPageUrl() }}" aria-label="Previous">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                        
                        <!-- Pagination Elements -->
                        @php
                            $start = max($eleves->currentPage() - 2, 1);
                            $end = min($start + 4, $eleves->lastPage());
                            $start = max(min($start, $end - 4), 1);
                        @endphp
                        
                        @for ($i = $start; $i <= $end; $i++)
                            <li class="page-item {{ $i == $eleves->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $eleves->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        
                        <!-- Next Page Link -->
                        <li class="page-item {{ $eleves->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $eleves->nextPageUrl() }}" aria-label="Next">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                        
                        <!-- Last Page Link -->
                        <li class="page-item {{ $eleves->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $eleves->url($eleves->lastPage()) }}" aria-label="Last">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de détails élève -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailsModalLabel">Détails de l'élève</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
                
                <div id="eleveDetails" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <img id="elevePhoto" src="" alt="Photo" class="img-fluid rounded border" 
                                 onerror="this.src='https://via.placeholder.com/150?text=Photo'">
                        </div>
                        <div class="col-md-9">
                            <h4 id="eleveNomComplet" class="text-primary"></h4>
                            <p id="eleveClasse" class="mb-1"></p>
                            <p id="eleveStatut" class="mb-1"></p>
                            <p id="eleveInscription" class="mb-1"></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-id-card me-2 text-primary"></i>INE:</strong> <span id="eleveIne"></span></p>
                            <p><strong><i class="fas fa-venus-mars me-2 text-primary"></i>Sexe:</strong> <span id="eleveSexe"></span></p>
                            <p><strong><i class="fas fa-birthday-cake me-2 text-primary"></i>Date de naissance:</strong> <span id="eleveDateNaissance"></span></p>
                            <p><strong><i class="fas fa-map-marker-alt me-2 text-primary"></i>Lieu de naissance:</strong> <span id="eleveLieuNaissance"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-file-alt me-2 text-primary"></i>Extrait de naissance:</strong> <span id="eleveExtrait"></span></p>
                            <p><strong><i class="fas fa-phone me-2 text-primary"></i>Contact parent:</strong> <span id="eleveContact"></span></p>
                            <p><strong><i class="fas fa-home me-2 text-primary"></i>Adresse:</strong> <span id="eleveAdresse"></span></p>
                            <p><strong><i class="fas fa-info-circle me-2 text-primary"></i>Motif d'entrée:</strong> <span id="eleveMotif"></span></p>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3 border-bottom pb-2"><i class="fas fa-history me-2"></i>Historique des inscriptions</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Année scolaire</th>
                                    <th>Classe</th>
                                    <th>Date d'inscription</th>
                                    <th>Montant payé</th>
                                    <th>Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inscriptionsList">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnInscrire" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Inscrire
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                    <div>
                        <p>Êtes-vous sûr de vouloir supprimer cet élève ? Cette action est irréversible.</p>
                        <p class="mb-0"><strong>Attention:</strong> Toutes les données associées seront également supprimées.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Supprimer
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
        // Filtrage par niveau qui met à jour les classes disponibles
        const niveauSelect = document.getElementById('niveau_id');
        const classeSelect = document.getElementById('classe_id');
        const classeOptions = Array.from(classeSelect.options).slice(1); // Garder une copie des options originales (sans l'option "Toutes les classes")
        
        niveauSelect.addEventListener('change', function() {
            const niveauId = this.value;
            
            // Réinitialiser le select des classes
            classeSelect.innerHTML = '<option value="">Toutes les classes</option>';
            
            // Si un niveau est sélectionné, filtrer les classes
            if (niveauId) {
                classeOptions.forEach(option => {
                    const classeNiveauId = option.getAttribute('data-niveau-id');
                    if (classeNiveauId === niveauId) {
                        classeSelect.appendChild(option.cloneNode(true));
                    }
                });
            } else {
                // Si aucun niveau n'est sélectionné, ajouter toutes les classes
                classeOptions.forEach(option => {
                    classeSelect.appendChild(option.cloneNode(true));
                });
            }
        });
        
        // Voir les détails d'un élève
        const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        document.querySelectorAll('.view-eleve-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const eleveId = this.getAttribute('data-eleve-id');
                
                // Afficher le spinner et cacher les détails
                document.querySelector('#detailsModal .spinner-border').style.display = 'inline-block';
                document.getElementById('eleveDetails').style.display = 'none';
                
                detailsModal.show();
                
                // Charger les détails de l'élève via AJAX
                fetch(`/inscriptions/eleves/${eleveId}`)
                    .then(response => response.json())
                    .then(data => {
                        const eleve = data.eleve;
                        const inscriptions = data.inscriptions;
                        
                        // Remplir les informations de l'élève
                        document.getElementById('eleveNomComplet').textContent = `${eleve.nom} ${eleve.prenom}`;
                        document.getElementById('eleveClasse').innerHTML = `<i class="fas fa-graduation-cap me-2 text-primary"></i><strong>Classe:</strong> ${eleve.classe.niveau.nom} - ${eleve.classe.nom}`;
                        
                        const statutClass = eleve.statut === 'Nouveau' ? 'bg-success' : 
                                           (eleve.statut === 'Ancien' ? 'bg-primary' : 'bg-warning');
                        document.getElementById('eleveStatut').innerHTML = `<i class="fas fa-user-tag me-2 text-primary"></i><strong>Statut:</strong> <span class="badge ${statutClass} rounded-pill">${eleve.statut}</span>`;
                        
                        // Afficher le statut d'inscription
                        const inscriptionClass = eleve.estInscrit ? 'bg-success' : 'bg-danger';
                        const inscriptionText = eleve.estInscrit ? 'Inscrit' : 'Non inscrit';
                        document.getElementById('eleveInscription').innerHTML = `<i class="fas fa-clipboard-check me-2 text-primary"></i><strong>Inscription:</strong> <span class="badge ${inscriptionClass} rounded-pill">${inscriptionText}</span>`;
                        
                        document.getElementById('eleveIne').textContent = eleve.ine;
                        document.getElementById('eleveSexe').textContent = eleve.sexe === 'M' ? 'Masculin' : 'Féminin';
                        document.getElementById('eleveDateNaissance').textContent = new Date(eleve.date_naissance).toLocaleDateString();
                        document.getElementById('eleveLieuNaissance').textContent = eleve.lieu_naissance;
                        document.getElementById('eleveExtrait').textContent = eleve.existence_extrait ? 'Oui' : 'Non';
                        document.getElementById('eleveContact').textContent = eleve.contact_parent || 'Non renseigné';
                        document.getElementById('eleveAdresse').textContent = eleve.adresse || 'Non renseignée';
                        document.getElementById('eleveMotif').textContent = eleve.motif_entre || 'Non renseigné';
                        
                        // Remplir le tableau des inscriptions
                        const inscriptionsList = document.getElementById('inscriptionsList');
                        inscriptionsList.innerHTML = '';
                        
                        if (inscriptions.length > 0) {
                            inscriptions.forEach(inscription => {
                                const row = document.createElement('tr');
                                
                                const statutClass = inscription.statut_paiement === 'Complet' ? 'bg-success' : 
                                                  (inscription.statut_paiement === 'Partiel' ? 'bg-warning' : 'bg-danger');
                                
                                row.innerHTML = `
                                    <td>${inscription.annee_scolaire}</td>
                                    <td>${inscription.classe.niveau.nom} - ${inscription.classe.nom}</td>
                                    <td>${new Date(inscription.date_inscription).toLocaleDateString()}</td>
                                    <td>${inscription.montant_paye.toLocaleString()} FCFA</td>
                                    <td><span class="badge ${statutClass} rounded-pill">${inscription.statut_paiement}</span></td>
                                    <td class="text-center">
                                        <a href="/inscriptions/recu/${inscription.id}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                    </td>
                                `;
                                
                                inscriptionsList.appendChild(row);
                            });
                        } else {
                            inscriptionsList.innerHTML = '<tr><td colspan="6" class="text-center">Aucune inscription trouvée</td></tr>';
                        }
                        
                        // Mettre à jour le bouton d'inscription
                        const btnInscrire = document.getElementById('btnInscrire');
                        btnInscrire.href = `/inscriptions/nouvelle?ine=${eleve.ine}`;
                        
                        // Afficher ou masquer le bouton d'inscription selon l'état d'inscription
                        if (eleve.estInscrit) {
                            btnInscrire.style.display = 'none';
                        } else {
                            btnInscrire.style.display = 'inline-block';
                        }
                        
                        // Cacher le spinner et afficher les détails
                        document.querySelector('#detailsModal .spinner-border').style.display = 'none';
                        document.getElementById('eleveDetails').style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        document.querySelector('#detailsModal .spinner-border').style.display = 'none';
                        document.getElementById('eleveDetails').innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Erreur lors du chargement des détails</div>';
                        document.getElementById('eleveDetails').style.display = 'block';
                    });
            });
        });
        
        // Configurer le formulaire de suppression
        document.querySelectorAll('.delete-eleve-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const eleveId = this.getAttribute('data-eleve-id');
                document.getElementById('deleteForm').action = `/inscriptions/eleves/${eleveId}`;
                deleteModal.show();
            });
        });
    });
</script>
@endsection