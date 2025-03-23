@extends('layouts.app')

@section('title', 'Tableau de Bord - Finances')
@section('page-title', 'Tableau de Bord Financier')

@section('content')
<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Aperçu financier {{ $parametres->annee_scolaire ?? 'Non définie' }}</h1>
    <button class="btn btn-primary" id="refreshBtn">
        <i class="fas fa-sync-alt"></i> Actualiser
    </button>
</div>

<!-- Totaux Cards -->
<div class="row">
    <!-- Recettes Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            RECETTES TOTALES</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['totalRecettes'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dépenses Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            DÉPENSES TOTALES</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['totalDepenses'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Solde Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            SOLDE</div>
                        <div class="h5 mb-0 font-weight-bold text-{{ $stats['solde'] >= 0 ? 'success' : 'danger' }}">
                            {{ number_format($stats['solde'], 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inscriptions Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            RECETTES INSCRIPTIONS</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['totalInscriptions'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Charts -->
<div class="row">
    <!-- Recettes/Dépenses Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Évolution des finances</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <li><h6 class="dropdown-header">Options:</h6></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>Exporter en PDF</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Exporter en Excel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('finances.rapports') }}"><i class="fas fa-chart-bar me-2"></i>Voir tous les rapports</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="financesLineChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition des dépenses Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Répartition des dépenses</h6>
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
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="depensesPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="me-2">
                        <i class="fas fa-circle text-primary"></i> Personnel
                    </span>
                    <span class="me-2">
                        <i class="fas fa-circle text-success"></i> Matériel
                    </span>
                    <span class="me-2">
                        <i class="fas fa-circle text-info"></i> Services
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Tables -->
<div class="row">
    <!-- Dernières transactions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dernières transactions</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($transactions) && count($transactions) > 0)
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type == 'recette' ? 'success' : 'danger' }} rounded-pill">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($transaction->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $transaction->description }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">Aucune transaction récente</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('finances.transactions') }}" class="btn btn-primary btn-sm">
                        Voir toutes les transactions
                        <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- À payer / À recouvrer -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Montants à recouvrer (Scolarité)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Niveau</th>
                                <th>Nombre d'élèves</th>
                                <th>Payé</th>
                                <th>Restant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($recouvrements) && count($recouvrements) > 0)
                                @foreach($recouvrements as $recouvrement)
                                <tr>
                                    <td>{{ $recouvrement->niveau_nom }}</td>
                                    <td>{{ $recouvrement->nb_eleves }}</td>
                                    <td>{{ number_format($recouvrement->montant_paye, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ number_format($recouvrement->montant_restant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">Aucune donnée disponible</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('finances.rapports') }}" class="btn btn-primary btn-sm">
                        Voir le rapport complet
                        <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
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
        
        // Line Chart - Finances Evolution
        const financesCtx = document.getElementById('financesLineChart').getContext('2d');
        const financesChart = new Chart(financesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [
                    {
                        label: 'Recettes',
                        lineTension: 0.3,
                        backgroundColor: "rgba(40, 167, 69, 0.05)",
                        borderColor: "rgba(40, 167, 69, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(40, 167, 69, 1)",
                        pointBorderColor: "rgba(40, 167, 69, 1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(40, 167, 69, 1)",
                        pointHoverBorderColor: "rgba(40, 167, 69, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        // Données fictives pour le moment - sera remplacé par des données réelles
                        data: [0, 10000, 20000, 15000, 25000, 30000, 25000, 20000, 30000, 40000, 35000, 50000],
                    },
                    {
                        label: 'Dépenses',
                        lineTension: 0.3,
                        backgroundColor: "rgba(220, 53, 69, 0.05)",
                        borderColor: "rgba(220, 53, 69, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(220, 53, 69, 1)",
                        pointBorderColor: "rgba(220, 53, 69, 1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(220, 53, 69, 1)",
                        pointHoverBorderColor: "rgba(220, 53, 69, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        // Données fictives pour le moment - sera remplacé par des données réelles
                        data: [0, 5000, 10000, 12000, 15000, 20000, 15000, 10000, 18000, 25000, 20000, 30000],
                    }
                ],
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
                            callback: function(value) {
                                return value.toLocaleString() + ' FCFA';
                            },
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)",
                            borderDash: [2],
                            drawBorder: false,
                            zeroLineColor: "rgba(0, 0, 0, 0.1)"
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
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
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
        
        // Pie Chart - Répartition des dépenses
        const depensesCtx = document.getElementById('depensesPieChart').getContext('2d');
        const depensesChart = new Chart(depensesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Personnel', 'Matériel', 'Services'],
                datasets: [{
                    // Données fictives pour le moment - sera remplacé par des données réelles
                    data: [55, 30, 15],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
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
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection