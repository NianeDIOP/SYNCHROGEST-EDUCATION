@extends('layouts.app')

@section('title', 'Tableau de Bord - Inscriptions')
@section('page-title', 'Tableau de Bord')

@section('styles')
<style>
    .stat-card {
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h2 class="card-title">
            {{ $parametres->nom_etablissement ?? 'Établissement' }} - Année scolaire: {{ $parametres->annee_scolaire ?? 'Non définie' }}
        </h2>
        
        <div class="row g-4 mb-4 mt-2">
            <div class="col-md-4">
                <div class="card bg-light stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-primary text-white me-3">
                                <span class="material-icons">people</span>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Élèves</h6>
                                <h4 class="mb-0 fw-bold">{{ $totalEleves }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-light stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-success text-white me-3">
                                <span class="material-icons">how_to_reg</span>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Inscrits</h6>
                                <h4 class="mb-0 fw-bold">{{ $totalInscrits }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-light stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-warning text-white me-3">
                                <span class="material-icons">person_add</span>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Nouveaux Inscrits</h6>
                                <h4 class="mb-0 fw-bold">{{ $nouveauxInscrits }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Inscriptions par Niveau</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="inscriptionsParNiveauChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Répartition par Type</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="typeElevesChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique inscriptions par niveau
        const niveauCtx = document.getElementById('inscriptionsParNiveauChart').getContext('2d');
        new Chart(niveauCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($inscriptionsParNiveau->pluck('nom')) !!},
                datasets: [{
                    label: 'Nombre d\'inscriptions',
                    data: {!! json_encode($inscriptionsParNiveau->pluck('total')) !!},
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Graphique type d'élèves
        const typeCtx = document.getElementById('typeElevesChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: ['Nouveaux', 'Anciens/Redoublants'],
                datasets: [{
                    data: [{{ $nouveauxInscrits }}, {{ $totalInscrits - $nouveauxInscrits }}],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
</script>
@endsection