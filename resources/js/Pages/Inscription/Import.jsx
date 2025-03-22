import React, { useState, useRef } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { toast } from 'react-hot-toast';
import * as XLSX from 'xlsx';

export default function Import({ niveaux }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        file: null,
        classe_id: '',
    });
    
    const fileInputRef = useRef(null);
    const [previewData, setPreviewData] = useState([]);
    const [columns, setColumns] = useState([]);
    const [fileName, setFileName] = useState('');
    const [isPreviewReady, setIsPreviewReady] = useState(false);
    
    const handleFileChange = (e) => {
        const file = e.target.files[0];
        
        if (file) {
            setFileName(file.name);
            setData('file', file);
            
            // Lire le fichier Excel pour prévisualisation
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const data = e.target.result;
                    const workbook = XLSX.read(data, { type: 'array' });
                    const sheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[sheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    
                    if (jsonData.length > 0) {
                        // Extraire les en-têtes (première ligne)
                        const headers = jsonData[0];
                        setColumns(headers);
                        
                        // Extraire les données (à partir de la deuxième ligne)
                        const rows = jsonData.slice(1).map(row => {
                            const rowData = {};
                            headers.forEach((header, idx) => {
                                rowData[header] = row[idx] || '';
                            });
                            return rowData;
                        });
                        
                        setPreviewData(rows.slice(0, 10)); // Afficher les 10 premières lignes
                        setIsPreviewReady(true);
                    }
                } catch (error) {
                    console.error('Erreur lors de la lecture du fichier:', error);
                    toast.error('Erreur lors de la lecture du fichier');
                }
            };
            reader.readAsArrayBuffer(file);
        }
    };
    
    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (!data.file || !data.classe_id) {
            toast.error('Veuillez sélectionner un fichier et une classe');
            return;
        }
        
        // Préparer les données pour l'envoi
        const formData = new FormData();
        formData.append('file', data.file);
        formData.append('classe_id', data.classe_id);
        
        post(route('inscriptions.import'), {
            onSuccess: (response) => {
                if (response?.data?.success) {
                    // Traiter les données du fichier pour les envoyer au format JSON
                    processFileForImport(data.file, data.classe_id);
                } else {
                    toast.error('Erreur lors du téléchargement du fichier');
                }
            },
        });
    };
    
    const processFileForImport = async (file, classeId) => {
        try {
            const reader = new FileReader();
            
            reader.onload = async (e) => {
                const data = e.target.result;
                const workbook = XLSX.read(data, { type: 'array' });
                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet);
                
                // Transformer les données pour correspondre à la structure attendue par le backend
                const eleves = jsonData.map(row => ({
                    ine: row.INE || row.IEN || row.ine || row.Ine || '',
                    prenom: row.Prénom || row.Prenom || row.prénom || row.prenom || row['Prénom(s)'] || '',
                    nom: row.Nom || row.nom || '',
                    sexe: (row.Sexe || row.sexe || '').toUpperCase() === 'F' ? 'F' : 'M',
                    date_naissance: formatDate(row['Date de Naissance'] || row['date de naissance'] || row.Date_Naissance || ''),
                    lieu_naissance: row['Lieu de Naissance'] || row['lieu de naissance'] || row.Lieu_Naissance || '',
                    existence_extrait: (row['Existence extrait'] || row['existence extrait'] || '').toLowerCase() === 'oui',
                    classe_id: classeId,
                    motif_entre: row["Motif d'entré"] || row["motif d'entrée"] || row.Motif_Entre || '',
                    statut: row.Statut || row.statut || 'Nouveau',
                }));
                
                // Envoyer les données transformées au backend
                const response = await fetch(route('inscriptions.saveImportedData'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ eleves }),
                });
                
                const result = await response.json();
                
                if (result.success) {
                    toast.success('Importation réussie');
                    // Réinitialiser le formulaire
                    reset();
                    setPreviewData([]);
                    setColumns([]);
                    setFileName('');
                    setIsPreviewReady(false);
                    if (fileInputRef.current) {
                        fileInputRef.current.value = '';
                    }
                } else {
                    toast.error(`Erreur lors de l'importation: ${result.message}`);
                }
            };
            
            reader.readAsArrayBuffer(file);
        } catch (error) {
            console.error('Erreur lors du traitement du fichier:', error);
            toast.error('Erreur lors du traitement du fichier');
        }
    };
    
    // Fonction pour formater les dates (peut nécessiter des ajustements selon le format d'entrée)
    const formatDate = (dateString) => {
        if (!dateString) return '';
        
        // Essayer plusieurs formats courants
        try {
            // Si c'est déjà au format YYYY-MM-DD
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
                return dateString;
            }
            
            // Format DD/MM/YYYY
            if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString)) {
                const [day, month, year] = dateString.split('/');
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }
            
            // Format DD-MM-YYYY
            if (/^\d{1,2}-\d{1,2}-\d{4}$/.test(dateString)) {
                const [day, month, year] = dateString.split('-');
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }
            
            // Essayer de parser avec Date
            const date = new Date(dateString);
            if (!isNaN(date.getTime())) {
                return date.toISOString().split('T')[0];
            }
            
            return '';
        } catch (error) {
            console.error('Erreur lors du formatage de la date:', error);
            return '';
        }
    };
    
    return (
        <AppLayout title="Importation des Élèves">
            <Head title="Importation des Élèves" />
            
            <div className="bg-white shadow rounded-lg p-6">
                <h2 className="text-xl font-semibold mb-6">Importation des listes d'élèves</h2>
                
                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                Sélectionner une classe
                            </label>
                            <select
                                className="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.classe_id}
                                onChange={(e) => setData('classe_id', e.target.value)}
                                required
                            >
                                <option value="">-- Sélectionner une classe --</option>
                                {niveaux.map((niveau) => (
                                    <optgroup key={niveau.id} label={niveau.nom}>
                                        {niveau.classes.map((classe) => (
                                            <option key={classe.id} value={classe.id}>
                                                {classe.nom}
                                            </option>
                                        ))}
                                    </optgroup>
                                ))}
                            </select>
                            {errors.classe_id && (
                                <div className="text-red-500 text-xs mt-1">{errors.classe_id}</div>
                            )}
                        </div>
                        
                        <div>
                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                Fichier Excel (XLSX, XLS, CSV)
                            </label>
                            <div className="flex items-center">
                                <input
                                    ref={fileInputRef}
                                    type="file"
                                    className="hidden"
                                    onChange={handleFileChange}
                                    accept=".xlsx,.xls,.csv"
                                />
                                <button
                                    type="button"
                                    onClick={() => fileInputRef.current.click()}
                                    className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                >
                                    Sélectionner un fichier
                                </button>
                                <span className="ml-3 text-gray-600">{fileName || 'Aucun fichier sélectionné'}</span>
                            </div>
                            {errors.file && (
                                <div className="text-red-500 text-xs mt-1">{errors.file}</div>
                            )}
                        </div>
                    </div>
                    
                    {isPreviewReady && previewData.length > 0 && (
                        <div className="mb-6">
                            <h3 className="text-lg font-medium mb-2">Aperçu des données</h3>
                            <div className="overflow-x-auto">
                                <table className="min-w-full bg-white border border-gray-200">
                                    <thead>
                                        <tr>
                                            {columns.map((column, index) => (
                                                <th key={index} className="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {column}
                                                </th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {previewData.map((row, rowIndex) => (
                                            <tr key={rowIndex} className={rowIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                                                {columns.map((column, colIndex) => (
                                                    <td key={colIndex} className="py-2 px-4 border-b border-gray-200 text-sm">
                                                        {row[column] || ''}
                                                    </td>
                                                ))}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            {previewData.length > 0 && (
                                <p className="text-sm text-gray-600 mt-2">
                                    Affichage de {previewData.length} lignes sur {previewData.length} au total.
                                </p>
                            )}
                        </div>
                    )}
                    
                    <div className="flex justify-end">
                        <button
                            type="submit"
                            className="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline"
                            disabled={processing || !data.file || !data.classe_id}
                        >
                            Importer
                        </button>
                    </div>
                </form>
                
                <div className="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h3 className="text-lg font-medium mb-2">Instructions</h3>
                    <p className="text-gray-700 mb-2">
                        Veuillez préparer votre fichier Excel avec les colonnes suivantes:
                    </p>
                    <ul className="list-disc pl-5 space-y-1 text-gray-700">
                        <li>IEN (ou INE) - Identifiant unique de l'élève</li>
                        <li>Prénom(s) - Prénom(s) de l'élève</li>
                        <li>Nom - Nom de famille de l'élève</li>
                        <li>Sexe - "M" pour Masculin, "F" pour Féminin</li>
                        <li>Date de Naissance - Format JJ/MM/AAAA</li>
                        <li>Lieu de Naissance - Ville/lieu de naissance</li>
                        <li>Existence extrait - "Oui" ou "Non"</li>
                        <li>Motif d'entrée - Raison de l'inscription</li>
                        <li>Statut - "Nouveau", "Ancien" ou "Redoublant"</li>
                    </ul>
                </div>
            </div>
        </AppLayout>
    );
}