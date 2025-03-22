@extends('layouts.app')

@section('title', 'Gestion des Élèves')
@section('page-title', 'Gestion des Élèves')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Liste des élèves</h5>
        <a href="{{ route('inscriptions.import') }}" class="btn btn-primary">
            <i class="material-icons">upload_file</i> Importer des élèves
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('inscriptions.eleves') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="INE, nom ou prénom...">
                </div>
                
                <div class="col-md-3 mb-3">
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
                
                <div class="col-md-3 mb-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select class="form-select" id="statut" name="statut">
                        <option value="">Tous les statuts</option>
                        <option value="Nouveau" {{ request('statut') == 'Nouveau' ? 'selected' : '' }}>Nouveau</option>
                        <option value="Ancien" {{ request('statut') == 'Ancien' ? 'selected' : '' }}>Ancien</option>
                        <option value="Redoublant" {{ request('statut') == 'Redoublant' ? 'selected' : '' }}>Redoublant</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="material-icons">search</i> Rechercher
                    </button>
                </div>
            </div>
        </form>

        @if($eleves->isEmpty())
            <div class="alert alert-info">
                <i class="material-icons">info</i> Aucun élève trouvé. Veuillez importer des élèves ou ajuster vos critères de recherche.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>INE</th>
                            <th>Nom & Prénom</th>
                            <th>Sexe</th>
                            <th>Date de naissance</th>
                            <th>Classe</th>
                            <th>Statut</th>
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
                                    <span class="badge bg-{{ $eleve->statut === 'Nouveau' ? 'success' : ($eleve->statut === 'Ancien' ? 'primary' : 'warning') }}">
                                        {{ $eleve->statut }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('inscriptions.nouvelle', ['ine' => $eleve->ine]) }}" class="btn btn-sm btn-primary" title="Inscrire">
                                            <i class="material-icons">how_to_reg</i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info view-eleve-btn" data-eleve-id="{{ $eleve->id }}" title="Voir détails">
                                            <i class="material-icons">visibility</i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-eleve-btn" data-eleve-id="{{ $eleve->id }}" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Supprimer">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Affichage de {{ $eleves->firstItem() ?? 0 }} à {{ $eleves->lastItem() ?? 0 }} sur {{ $eleves->total() }} élèves
                </div>
                <div>
                    {{ $eleves->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de détails élève -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Détails de l'élève</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <img id="elevePhoto" src="" alt="Photo" class="img-fluid rounded" 
                                 onerror="this.src='https://via.placeholder.com/150?text=Photo'">
                        </div>
                        <div class="col-md-9">
                            <h4 id="eleveNomComplet"></h4>
                            <p id="eleveClasse" class="mb-1"></p>
                            <p id="eleveStatut" class="mb-1"></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>INE:</strong> <span id="eleveIne"></span></p>
                            <p><strong>Sexe:</strong> <span id="eleveSexe"></span></p>
                            <p><strong>Date de naissance:</strong> <span id="eleveDateNaissance"></span></p>
                            <p><strong>Lieu de naissance:</strong> <span id="eleveLieuNaissance"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Extrait de naissance:</strong> <span id="eleveExtrait"></span></p>
                            <p><strong>Contact parent:</strong> <span id="eleveContact"></span></p>
                            <p><strong>Adresse:</strong> <span id="eleveAdresse"></span></p>
                            <p><strong>Motif d'entrée:</strong> <span id="eleveMotif"></span></p>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Historique des inscriptions</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Année scolaire</th>
                                    <th>Classe</th>
                                    <th>Date d'inscription</th>
                                    <th>Montant payé</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody id="inscriptionsList">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnInscrire" class="btn btn-primary">Inscrire</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
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
                <p>Êtes-vous sûr de vouloir supprimer cet élève ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Voir les détails d'un élève
        const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
        
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
                        document.getElementById('eleveClasse').textContent = `Classe: ${eleve.classe.niveau.nom} - ${eleve.classe.nom}`;
                        
                        const statutClass = eleve.statut === 'Nouveau' ? 'bg-success' : 
                                           (eleve.statut === 'Ancien' ? 'bg-primary' : 'bg-warning');
                        document.getElementById('eleveStatut').innerHTML = `Statut: <span class="badge ${statutClass}">${eleve.statut}</span>`;
                        
                        document.getElementById('eleveIne').textContent = eleve.ine;
                        document.getElementById('eleveSexe').textContent = eleve.sexe;
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
                                    <td><span class="badge ${statutClass}">${inscription.statut_paiement}</span></td>
                                `;
                                
                                inscriptionsList.appendChild(row);
                            });
                        } else {
                            inscriptionsList.innerHTML = '<tr><td colspan="5" class="text-center">Aucune inscription trouvée</td></tr>';
                        }
                        
                        // Mettre à jour le lien d'inscription
                        document.getElementById('btnInscrire').href = `/inscriptions/nouvelle?ine=${eleve.ine}`;
                        
                        // Cacher le spinner et afficher les détails
                        document.querySelector('#detailsModal .spinner-border').style.display = 'none';
                        document.getElementById('eleveDetails').style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        document.querySelector('#detailsModal .spinner-border').style.display = 'none';
                        document.getElementById('eleveDetails').innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des détails</div>';
                        document.getElementById('eleveDetails').style.display = 'block';
                    });
            });
        });
        
        // Configurer le formulaire de suppression
        document.querySelectorAll('.delete-eleve-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const eleveId = this.getAttribute('data-eleve-id');
                document.getElementById('deleteForm').action = `/inscriptions/eleves/${eleveId}`;
            });
        });
    });
</script>
@endsection