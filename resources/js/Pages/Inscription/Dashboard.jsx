import React from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Chart, registerables } from 'chart.js';
import { Bar, Pie } from 'react-chartjs-2';

Chart.register(...registerables);

export default function Dashboard({ parametres, stats }) {
    const inscriptionsParNiveauData = {
        labels: stats.inscriptionsParNiveau.map(item => item.nom),
        datasets: [
            {
                label: 'Nombre d\'inscriptions',
                data: stats.inscriptionsParNiveau.map(item => item.total),
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
                borderWidth: 1,
            },
        ],
    };

    const typeElevesData = {
        labels: ['Nouveaux', 'Anciens/Redoublants'],
        datasets: [
            {
                data: [stats.nouveauxInscrits, stats.totalInscrits - stats.nouveauxInscrits],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                ],
                borderWidth: 1,
            },
        ],
    };

    return (
        <AppLayout title="Tableau de Bord - Inscriptions">
            <Head title="Tableau de Bord - Inscriptions" />
            
            <div className="bg-white shadow rounded-lg p-6 mb-6">
                <h2 className="text-xl font-semibold mb-4">
                    {parametres?.nom_etablissement || 'Établissement'} - Année scolaire: {parametres?.annee_scolaire || 'Non définie'}
                </h2>
                
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div className="bg-blue-50 p-4 rounded-lg shadow">
                        <div className="flex items-center">
                            <div className="p-3 rounded-full bg-blue-500 text-white mr-4">
                                <span className="material-icons">people</span>
                            </div>
                            <div>
                                <p className="text-gray-500">Total Élèves</p>
                                <p className="text-2xl font-bold">{stats.totalEleves}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="bg-green-50 p-4 rounded-lg shadow">
                        <div className="flex items-center">
                            <div className="p-3 rounded-full bg-green-500 text-white mr-4">
                                <span className="material-icons">how_to_reg</span>
                            </div>
                            <div>
                                <p className="text-gray-500">Total Inscrits</p>
                                <p className="text-2xl font-bold">{stats.totalInscrits}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="bg-yellow-50 p-4 rounded-lg shadow">
                        <div className="flex items-center">
                            <div className="p-3 rounded-full bg-yellow-500 text-white mr-4">
                                <span className="material-icons">person_add</span>
                            </div>
                            <div>
                                <p className="text-gray-500">Nouveaux Inscrits</p>
                                <p className="text-2xl font-bold">{stats.nouveauxInscrits}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="bg-white p-4 rounded-lg shadow">
                        <h3 className="text-lg font-semibold mb-3">Inscriptions par Niveau</h3>
                        <div className="h-64">
                            <Bar 
                                data={inscriptionsParNiveauData} 
                                options={{
                                    responsive: true,
                                    maintainAspectRatio: false,
                                }}
                            />
                        </div>
                    </div>
                    
                    <div className="bg-white p-4 rounded-lg shadow">
                        <h3 className="text-lg font-semibold mb-3">Répartition par Type</h3>
                        <div className="h-64">
                            <Pie 
                                data={typeElevesData} 
                                options={{
                                    responsive: true,
                                    maintainAspectRatio: false,
                                }}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}