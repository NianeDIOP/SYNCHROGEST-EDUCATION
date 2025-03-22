@extends('layouts.app')

@section('title', 'Tableau de Bord - Inscriptions')
@section('page-title', 'Tableau de Bord')

@section('content')
<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Aperçu de l'année scolaire {{ $parametres->annee_scolaire ?? '' }}</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#refreshDataModal">
        <i class="material-icons-round">refresh</i> Actualiser
    </button>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Total Élèves Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
            <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total des élèves</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEleves) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="material-icons-round text-gray-300" style="font-size: 2.5rem;">group</i>
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
                            Élèves inscrits</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalInscrits) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="material-icons-round text-gray-300" style="font-size: 2.5rem;">how_to_reg</i>
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
                            Nouveaux élèves</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($nouveauxInscrits) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="material-icons-round text-gray-300" style="font-size: 2.5rem;">person_add</i>
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
                            Taux d'inscription</div>
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
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $tauxRemplissage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="material-icons-round text-gray-300" style="font-size: 2.5rem;">trending_up</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Inscriptions par niveau -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Inscriptions par niveau</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="material-icons-round text-gray-400">more_vert</i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Options:</div>
                        <a class="dropdown-item" href="#">Exporter en PDF</a>
                        <a class="dropdown-item" href="#">Exporter en Excel</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('inscriptions.rapports') }}">Voir tous les rapports</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="inscriptionsParNiveauChart" height="300"></canvas>
                </div>
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
                        <i class="material-icons-round text-gray-400">more_vert</i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink2">
                        <div class="dropdown-header">Options:</div>
                        <a class="dropdown-item" href="#">Exporter en PDF</a>
                        <a class="dropdown-item" href="#">Exporter en Excel</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie">
                    <canvas id="typeElevesChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++)
                            <tr>
                                <td>Elève {{ $i+1 }}</td>
                                <td>6ème A</td>
                                <td>{{ date('d/m/Y', strtotime('-'.$i.' days')) }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="material-icons-round">visibility</i>
                                    </a>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('inscriptions.eleves') }}" class="btn btn-primary btn-sm">
                        Voir toutes les inscriptions
                        <i class="material-icons-round">arrow_forward</i>
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
                @for ($i = 0; $i < 5; $i++)
                <h4 class="small font-weight-bold">
                    6ème A 
                    <span class="float-end">{{ 80 - ($i * 10) }}%</span>
                </h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-{{ $i === 0 ? 'danger' : ($i === 1 ? 'warning' : ($i === 2 ? 'primary' : 'info')) }}" 
                         role="progressbar" style="width: {{ 80 - ($i * 10) }}%"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<!-- Refresh Data Modal -->
<div class="modal fade" id="refreshDataModal" tabindex="-1" aria-labelledby="refreshDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refreshDataModalLabel">Actualiser les données</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous actualiser les données du tableau de bord ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="location.reload();">
                    <i class="material-icons-round">refresh</i> Actualiser
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endsection