@extends('layouts.app')

@section('title', 'Tableau de Bord - Inscriptions')
@section('page-title', 'Tableau de Bord')

@section('content')
<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Aperçu de l'année scolaire {{ $parametres->annee_scolaire ?? 'Non définie' }}</h1>
    <button class="btn btn-primary" id="refreshBtn">
        <i class="fas fa-sync-alt"></i> Actualiser
    </button>
</div>

<!-- Content Row - Statistics Cards -->
<div class="row">
    <!-- Total Élèves Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            TOTAL DES ÉLÈVES</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEleves) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Inscrits Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            ÉLÈVES INSCRITS</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalInscrits) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nouveaux inscrits Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            NOUVEAUX ÉLÈVES</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($nouveauxInscrits) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Taux de remplissage Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            TAUX D'INSCRIPTION</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                    @php
                                        $tauxRemplissage = $totalEleves > 0 ? ($totalInscrits / $totalEleves) * 100 : 0;
                                    @endphp
                                    {{ number_format($tauxRemplissage, 1) }}%
                                </div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(100, $tauxRemplissage) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Charts -->
<div class="row">
    <!-- Inscriptions par niveau -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Inscriptions par niveau</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <li><h6 class="dropdown-header">Options:</h6></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>Exporter en PDF</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Exporter en Excel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('inscriptions.rapports') }}"><i class="fas fa-chart-bar me-2"></i>Voir tous les rapports</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                @if(isset($inscriptionsParNiveau) && $inscriptionsParNiveau->count() > 0)
                    <div class="chart-bar">
                        <canvas id="inscriptionsParNiveauChart" height="300"></canvas>
                    </div>
                @else
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>Aucune donnée disponible sur les inscriptions par niveau.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Répartition par type -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Répartition par statut</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink2" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="dropdownMenuLink2">
                        <li><h6 class="dropdown-header">Options:</h6></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>Exporter en PDF</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Exporter en Excel</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                @if(isset($totalInscrits) && $totalInscrits > 0)
                    <div class="chart-pie">
                        <canvas id="typeElevesChart" height="300"></canvas>
                    </div>
                @else
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>Aucune donnée disponible sur la répartition des élèves.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Tables -->
<div class="row">
    <!-- Dernières inscriptions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dernières inscriptions</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Élève</th>
                                <th>Classe</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($dernieresInscriptions) && $dernieresInscriptions->count() > 0)
                                @foreach($dernieresInscriptions as $inscription)
                                <tr>
                                    <td>{{ $inscription->eleve->nom }} {{ $inscription->eleve->prenom }}</td>
                                    <td>{{ $inscription->classe->niveau->nom }} - {{ $inscription->classe->nom }}</td>
                                    <td>{{ $inscription->date_inscription->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('inscriptions.recu', $inscription->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">Aucune inscription récente</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('inscriptions.eleves') }}" class="btn btn-primary btn-sm">
                        Voir toutes les inscriptions
                        <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Classes à forte demande -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Classes à forte demande</h6>
            </div>
            <div class="card-body">
                @if(isset($classesFortes) && $classesFortes->count() > 0)
                    @foreach($classesFortes as $index => $classe)
                        <h4 class="small font-weight-bold">
                            {{ $classe->niveau_nom }} - {{ $classe->nom }}
                            <span class="float-end">{{ number_format($classe->taux_remplissage, 1) }}% ({{ $classe->inscrits }}/{{ $classe->capacite }})</span>
                        </h4>
                        <div class="progress mb-4">
                            @php
                                $color = '';
                                if ($classe->taux_remplissage >= 90) {
                                    $color = 'danger';
                                } elseif ($classe->taux_remplissage >= 75) {
                                    $color = 'warning';
                                } elseif ($classe->taux_remplissage >= 50) {
                                    $color = 'primary';
                                } else {
                                    $color = 'info';
                                }
                            @endphp
                            <div class="progress-bar bg-{{ $color }}" 
                                role="progressbar" style="width: {{ min(100, $classe->taux_remplissage) }}%"></div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>Aucune donnée disponible sur les classes.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Actualisation du tableau de bord
        document.getElementById('refreshBtn').addEventListener('click', function() {
            location.reload();
        });
        
        @if(isset($inscriptionsParNiveau) && $inscriptionsParNiveau->count() > 0)
        // Bar Chart - Inscriptions par niveau
        const niveauCtx = document.getElementById('inscriptionsParNiveauChart').getContext('2d');
        const niveauChart = new Chart(niveauCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($inscriptionsParNiveau->pluck('nom')) !!},
                datasets: [{
                    label: 'Nombre d\'inscriptions',
                    data: {!! json_encode($inscriptionsParNiveau->pluck('total')) !!},
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                    ],
                    borderColor: [
                        'rgba(78, 115, 223, 1)',
                        'rgba(28, 200, 138, 1)',
                        'rgba(54, 185, 204, 1)',
                        'rgba(246, 194, 62, 1)',
                        'rgba(231, 74, 59, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        ticks: {
                            min: 0,
                            precision: 0,
                            padding: 10,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                            drawBorder: false,
                            borderDash: [2],
                            borderDashOffset: [2],
                            zeroLineColor: "rgba(0, 0, 0, 0.15)"
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.formattedValue + ' élève(s)';
                            }
                        }
                    }
                }
            }
        });
        @endif
        
        @if(isset($nouveauxInscrits) && isset($totalInscrits) && $totalInscrits > 0)
        // Pie Chart - Répartition par type
        const typeCtx = document.getElementById('typeElevesChart').getContext('2d');
        const typeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Nouveaux', 'Anciens/Redoublants'],
                datasets: [{
                    data: [{{ $nouveauxInscrits }}, {{ $totalInscrits - $nouveauxInscrits }}],
                    backgroundColor: ['#4e73df', '#1cc88a'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection