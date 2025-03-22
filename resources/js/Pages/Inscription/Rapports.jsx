import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { toast } from 'react-hot-toast';

export default function Rapports({ niveaux, parametres }) {
    const [reportType, setReportType] = useState('general');
    const [niveauId, setNiveauId] = useState('');
    const [classeId, setClasseId] = useState('');
    const [statut, setStatut] = useState('tous');
    const [format, setFormat] = useState('pdf');
    const [isGenerating, setIsGenerating] = useState(false);
    
    // Filtrer les classes selon le niveau sélectionné
    const filteredClasses = niveauId 
        ? niveaux.find(n => n.id === parseInt(niveauId))?.classes || []
        : [];
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        
        setIsGenerating(true);
        
        try {
            const response = await fetch(route('inscriptions.genererRapport'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    type: reportType,
                    niveau_id: niveauId,
                    classe_id: classeId,
                    statut,
                    format,
                }),
            });
            
            const result = await response.json();
            
            if (result.success) {
                toast.success('Rapport généré avec succès');
                
                // Si le rapport est prêt à être téléchargé
                if (result.file_url) {
                    window.open(result.file_url, '_blank');
                }
            } else {
                toast.error(`Erreur lors de la génération du rapport: ${result.message}`);
            }
        } catch (error) {
            console.error('Erreur lors de la génération du rapport:', error);
            toast.error('Erreur lors de la génération du rapport');
        } finally {
            setIsGenerating(false);
        }
    };
    
    return (
        <AppLayout title="Rapports d'Inscription">
            <Head title="Rapports d'Inscription" />
            
            <div className="bg-white shadow rounded-lg p-6">
                <h2 className="text-xl font-semibold mb-6">Générer des Rapports</h2>
                
                <form onSubmit={handleSubmit}>
                    <div className="mb-6">
                        <label className="block text-gray-700 text-sm font-bold mb-2">
                            Type de rapport
                        </label>
                        <div className="flex space-x-4">
                            <label className="inline-flex items-center">
                                <input
                                    type="radio"
                                    className="form-radio"
                                    name="reportType"
                                    value="general"
                                    checked={reportType === 'general'}
                                    onChange={() => setReportType('general')}
                                />
                                <span className="ml-2">Général (tous les niveaux)</span>
                            </label>
                            <label className="inline-flex items-center">
                                <input
                                    type="radio"
                                    className="form-radio"
                                    name="reportType"
                                    value="niveau"
                                    checked={reportType === 'niveau'}
                                    onChange={() => setReportType('niveau')}
                                />
                                <span className="ml-2">Par niveau</span>
                            </label>
                            <label className="inline-flex items-center">
                                <input
                                    type="radio"
                                    className="form-radio"
                                    name="reportType"
                                    value="classe"
                                    checked={reportType === 'classe'}
                                    onChange={() => setReportType('classe')}
                                />
                                <span className="ml-2">Par classe</span>
                            </label>
                        </div>
                    </div>
                    
                    {(reportType === 'niveau' || reportType === 'classe') && (
                        <div className="mb-6">
                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                Niveau
                            </label>
                            <select
                                className="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={niveauId}
                                onChange={(e) => {
                                    setNiveauId(e.target.value);
                                    setClasseId(''); // Réinitialiser la classe lors du changement de niveau
                                }}
                                required={reportType === 'niveau' || reportType === 'classe'}
                            >
                                <option value="">-- Sélectionner un niveau --</option>
                                {niveaux.map((niveau) => (
                                    <option key={niveau.id} value={niveau.id}>
                                        {niveau.nom}
                                    </option>
                                ))}
                            </select>
                        </div>
                    )}
                    
                    {reportType === 'classe' && niveauId && (
                        <div className="mb-6">
                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                Classe
                            </label>
                            <select
                                className="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={classeId}
                                onChange={(e) => setClasseId(e.target.value)}
                                required={reportType === 'classe'}
                            >
                                <option value="">-- Sélectionner une classe --</option>
                                {filteredClasses.map((classe) => (
                                    <option key={classe.id} value={classe.id}>
                                        {classe.nom}
                                    </option>
                                ))}
                            </select>
                        </div>
                    )}
                    
                    <div className="mb-6">
                        <label className="block text-gray-700 text-sm font-bold mb-2">
                            Statut des élèves
                        </label>
                        <select
                            className="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            value={statut}
                            onChange={(e) => setStatut(e.target.value)}
                        >
                            <option value="tous">Tous les statuts</option>
                            <option value="Nouveau">Nouveaux</option>
                            <option value="Ancien">Anciens</option>
                            <option value="Redoublant">Redoublants</option>
                        </select>
                    </div>
                    
                    <div className="mb-8">
                        <label className="block text-gray-700 text-sm font-bold mb-2">
                            Format du rapport
                        </label>
                        <div className="flex space-x-4">
                            <label className="inline-flex items-center">
                                <input
                                    type="radio"
                                    className="form-radio"
                                    name="format"
                                    value="pdf"
                                    checked={format === 'pdf'}
                                    onChange={() => setFormat('pdf')}
                                />
                                <span className="ml-2">PDF</span>
                            </label>
                            <label className="inline-flex items-center">
                                <input
                                    type="radio"
                                    className="form-radio"
                                    name="format"
                                    value="excel"
                                    checked={format === 'excel'}
                                    onChange={() => setFormat('excel')}
                                />
                                <span className="ml-2">Excel</span>
                            </label>
                        </div>
                    </div>
                    
                    <div className="flex justify-end">
                        <button
                            type="submit"
                            className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline flex items-center"
                            disabled={isGenerating}
                        >
                            {isGenerating ? (
                                <>
                                    <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Génération en cours...
                                </>
                            ) : (
                                <>
                                    <span className="material-icons mr-2">description</span>
                                    Générer le rapport
                                </>
                            )}
                        </button>
                    </div>
                </form>
                
                <div className="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h3 className="text-lg font-medium mb-2">Types de rapports disponibles</h3>
                    <ul className="list-disc pl-5 space-y-2 text-gray-700">
                        <li>
                            <strong>Rapport général</strong> - Liste de tous les élèves inscrits pour l'année scolaire en cours.
                        </li>
                        <li>
                            <strong>Rapport par niveau</strong> - Liste des élèves inscrits dans un niveau spécifique.
                        </li>
                        <li>
                            <strong>Rapport par classe</strong> - Liste des élèves inscrits dans une classe spécifique.
                        </li>
                    </ul>
                    
                    <p className="mt-4 text-gray-600">
                        Tous les rapports incluent les informations d'inscription, les statuts de paiement et les détails des élèves.
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}