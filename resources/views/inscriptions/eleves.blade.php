@extends('layouts.app')

@section('title', 'Liste des Élèves')
@section('page-title', 'Liste des Élèves')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="card-title">Gestion des Élèves</h2>
            <a href="{{ route('inscriptions.import') }}" class="btn btn-primary">
                <span class="material-icons">upload</span> Importer des élèves
            </a>
        </div>
        
        <form action="{{ route('inscriptions.eleves') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="INE, nom ou prénom...">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Classe</label>
                    <select class="form-select" name="classe_id">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->niveau->nom }} - {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select" name="statut">
                        <option value="">Tous les statuts</option>
                        <option value="Nouveau" {{ request('statut') == 'Nouveau' ? 'selected' : '' }}>Nouveau</option>
                        <option value="Ancien" {{ request('statut') == 'Ancien' ? 'selected' : '' }}>Ancien</option>
                        <option value="Redoublant" {{ request('statut') == 'Redoublant' ? 'selected' : '' }}>Redoublant</option>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <span class="material-icons">search</span> Filtrer
                    </button>
                </div>
            </div>
        </form>
        
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
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eleves as $eleve)
                        <tr>
                            <td>{{ $eleve->ine }}</td>
                            <td>{{ $eleve->nom }} {{ $eleve->prenom }}</td>
                            <td>{{ $eleve->sexe }}</td>
                            <td>{{ $eleve->date_naissance->format('d/m/Y') }}</td>
                            <td>{{ $eleve->classe->niveau->nom }} - {{ $eleve->classe->nom }}</td>
                            <td>
                                <span class="badge {{ 
                                    $eleve->statut === 'Nouveau' ? 'bg-success' : 
                                    ($eleve->statut === 'Ancien' ? 'bg-primary' : 'bg-warning')
                                }}">
                                    {{ $eleve->statut }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('inscriptions.nouvelle', ['ine' => $eleve->ine]) }}" class="btn btn-sm btn-primary" title="Inscrire">
                                        <span class="material-icons">how_to_reg</span>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-info view-eleve-btn" data-eleve-id="{{ $eleve->id }}" title="Voir détails">
                                        <span class="material-icons">visibility</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-eleve-btn" data-eleve-id="{{ $eleve->id }}" title="Supprimer">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucun élève trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                Affichage de {{ $eleves->firstItem() ?? 0 }} à {{ $eleves->lastItem() ?? 0 }} sur {{ $eleves->total() }} élèves
            </div>
            
            <div>
                {{ $eleves->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal détails élève -->
<div class="modal fade" id="eleveDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de l'élève</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="border p-2 rounded">
                            <img src="#" alt="Photo" id="elevePhoto" class="img-fluid rounded" style="max-height: 150px;" onerror="this.src='https://via.placeholder.com/150?text=Photo'">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4 id="eleveNomComplet"></h4>
                        <p id="eleveClasse" class="mb-1"></p>
                        <p id="eleveStatut" class="mb-3"></p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>INE:</strong> <span id="eleveIne"></span></p>
                                <p class="mb-1"><strong>Sexe:</strong> <span id="eleveSexe"></span></p>
                                <p class="mb-1"><strong>Date de naissance:</strong> <span id="eleveDateNaissance"></span></p>
                                <p class="mb-1"><strong>Lieu de naissance:</strong> <span id="eleveLieuNaissance"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Extrait de naissance:</strong> <span id="eleveExtrait"></span></p>
                                <p class="mb-1"><strong>Contact parent:</strong> <span id="eleveContact"></span></p>
                                <p class="mb-1"><strong>Adresse:</strong> <span id="eleveAdresse"></span></p>
                                <p class="mb-1"><strong>Motif d'entrée:</strong> <span id="eleveMotif"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <h5>Historique des inscriptions</h5>
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
                        <tbody id="inscriptionsTableBody">
                            <!-- Données chargées dynamiquement -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnInscrire" class="btn btn-primary">Inscrire</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal confirmation suppression -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet élève ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteEleveForm" action="" method="POST">
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
        // Initialiser les modals
        const eleveDetailsModal = new bootstrap.Modal(document.getElementById('eleveDetailsModal'));
        const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        
        // Gestionnaire pour le bouton de détails
        document.querySelectorAll('.view-eleve-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const eleveId = this.dataset.eleveId;
                fetchEleveDetails(eleveId);
            });
        });
        
        // Gestionnaire pour le bouton de suppression
        document.querySelectorAll('.delete-eleve-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const eleveId = this.dataset.eleveId;
                document.getElementById('deleteEleveForm').action = `/inscriptions/eleves/${eleveId}`;
                deleteConfirmModal.show();
            });
        });
        
        // Fonction pour récupérer les détails de l'élève
        function fetchEleveDetails(eleveId) {
            fetch(`/inscriptions/eleves/${eleveId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la récupération des détails de l\'élève');
                    }
                    return response.json();
                })
                .then(data => {
                    displayEleveDetails(data.eleve, data.inscriptions);
                    eleveDetailsModal.show();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la récupération des détails de l\'élève.');
                });
        }
        
        // Fonction pour afficher les détails de l'élève dans le modal
        function displayEleveDetails(eleve, inscriptions) {
            // Informations de base
            document.getElementById('eleveNomComplet').textContent = `${eleve.nom} ${eleve.prenom}`;
            document.getElementById('eleveClasse').textContent = `Classe: ${eleve.classe.niveau.nom} - ${eleve.classe.nom}`;
            
            // Statut avec badge
            const statutBadge = eleve.statut === 'Nouveau' ? 'bg-success' : 
                              (eleve.statut === 'Ancien' ? 'bg-primary' : 'bg-warning');
            document.getElementById('eleveStatut').innerHTML = `Statut: <span class="badge ${statutBadge}">${eleve.statut}</span>`;
            
            // Autres informations
            document.getElementById('eleveIne').textContent = eleve.ine;
            document.getElementById('eleveSexe').textContent = eleve.sexe;
            document.getElementById('eleveDateNaissance').textContent = new Date(eleve.date_naissance).toLocaleDateString();
            document.getElementById('eleveLieuNaissance').textContent = eleve.lieu_naissance;
            document.getElementById('eleveExtrait').textContent = eleve.existence_extrait ? 'Oui' : 'Non';
            document.getElementById('eleveContact').textContent = eleve.contact_parent || 'Non renseigné';
            document.getElementById('eleveAdresse').textContent = eleve.adresse || 'Non renseignée';
            document.getElementById('eleveMotif').textContent = eleve.motif_entre || 'Non renseigné';
            
            // Photo
            if (eleve.photo_path) {
                document.getElementById('elevePhoto').src = eleve.photo_path;
            } else {
                document.getElementById('elevePhoto').src = 'https://via.placeholder.com/150?text=Photo';
            }
            
            // Lien d'inscription
            document.getElementById('btnInscrire').href = `/inscriptions/nouvelle?ine=${eleve.ine}`;
            
            // Historique des inscriptions
            const inscriptionsTableBody = document.getElementById('inscriptionsTableBody');
            inscriptionsTableBody.innerHTML = '';
            
            if (inscriptions.length > 0) {
                inscriptions.forEach(inscription => {
                    const row = document.createElement('tr');
                    
                    // Statut de paiement avec couleur
                    const statutClass = inscription.statut_paiement === 'Complet' ? 'success' :
                                      (inscription.statut_paiement === 'Partiel' ? 'warning' : 'danger');
                    
                    row.innerHTML = `
                        <td>${inscription.annee_scolaire}</td>
                        <td>${inscription.classe.niveau.nom} - ${inscription.classe.nom}</td>
                        <td>${new Date(inscription.date_inscription).toLocaleDateString()}</td>
                        <td>${inscription.montant_paye.toLocaleString()} FCFA</td>
                        <td><span class="badge bg-${statutClass}">${inscription.statut_paiement}</span></td>
                    `;
                    
                    inscriptionsTableBody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="5" class="text-center">Aucune inscription trouvée</td>';
                inscriptionsTableBody.appendChild(row);
            }
        }
    });
</script>
@endsection