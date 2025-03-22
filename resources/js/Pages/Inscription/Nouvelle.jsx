import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { toast } from 'react-hot-toast';

export default function Nouvelle({ eleve, classes, parametres, searchIne }) {
    const { data, setData, post, processing, errors } = useForm({
        eleve_id: eleve ? eleve.id : '',
        classe_id: eleve ? eleve.classe_id : '',
        montant_paye: '',
        date_inscription: new Date().toISOString().split('T')[0],
    });
    
    const [searchedIne, setSearchedIne] = useState(searchIne || '');
    
    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (!eleve) {
            toast.error('Veuillez rechercher un élève');
            return;
        }
        
        post(route('inscriptions.nouvelle'), {
            onSuccess: () => {
                toast.success('Inscription enregistrée avec succès');
            },
        });
    };
    
    const handleSearch = () => {
        if (!searchedIne) {
            toast.error('Veuillez saisir un INE');
            return;
        }
        
        window.location.href = route('inscriptions.nouvelle', { ine: searchedIne });
    };
    
    // Calculer les frais d'inscription basés sur la classe sélectionnée
    const getInscriptionFees = () => {
        if (!data.classe_id) return 0;
        
        const classe = classes.find(c => c.id === parseInt(data.classe_id));
        
        if (!classe || !classe.niveau) return 0;
        
        let total = classe.niveau.frais_inscription;
        
        // Ajouter les frais d'examen si applicable
        if (classe.niveau.est_niveau_examen) {
            total += classe.niveau.frais_examen;
        }
        
        return total;
    };
    
    const totalFees = getInscriptionFees();
    
    return (
        <AppLayout title="Nouvelle Inscription">
            <Head title="Nouvelle Inscription" />
            
            <div className="bg-white shadow rounded-lg p-6">
                <h2 className="text-xl font-semibold mb-6">Nouvelle Inscription</h2>
                
                <div className="mb-8">
                    <div className="bg-gray-50 p-4 rounded-lg">
                        <h3 className="text-lg font-medium mb-4">Rechercher un élève par INE</h3>
                        
                        <div className="flex space-x-4">
                            <input
                                type="text"
                                className="shadow appearance-none border rounded flex-1 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Saisir l'INE..."
                                value={searchedIne}
                                onChange={(e) => setSearchedIne(e.target.value)}
                            />
                            
                            <button
                                type="button"
                                onClick={handleSearch}
                                className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            >
                                Rechercher
                            </button>
                        </div>
                    </div>
                </div>
                
                {eleve ? (
                    <div>
                        <div className="bg-blue-50 p-4 rounded-lg mb-6">
                            <h3 className="text-lg font-medium mb-2">Informations de l'élève</h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p className="text-sm text-gray-600">INE</p>
                                    <p className="font-medium">{eleve.ine}</p>
                                </div>
                                
                                <div>
                                    <p className="text-sm text-gray-600">Nom & Prénom</p>
                                    <p className="font-medium">{eleve.nom} {eleve.prenom}</p>
                                </div>
                                
                                <div>
                                    <p className="text-sm text-gray-600">Sexe</p>
                                    <p className="font-medium">{eleve.sexe}</p>
                                </div>
                                
                                <div>
                                    <p className="text-sm text-gray-600">Date de naissance</p>
                                    <p className="font-medium">{new Date(eleve.date_naissance).toLocaleDateString()}</p>
                                </div>
                                
                                <div>
                                    <p className="text-sm text-gray-600">Lieu de naissance</p>
                                    <p className="font-medium">{eleve.lieu_naissance}</p>
                                </div>
                                
                                <div>
                                    <p className="text-sm text-gray-600">Statut</p>
                                    <p className="font-medium">{eleve.statut}</p>
                                </div>
                                
                                <div>
                                    <p className="text-sm text-gray-600">Classe actuelle</p>
                                    <p className="font-medium">{eleve.classe.niveau.nom} - {eleve.classe.nom}</p>
                                </div>
                            </div>
                        </div>
                        
                        <form onSubmit={handleSubmit}>
                            <input type="hidden" name="eleve_id" value={data.eleve_id} />
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label className="block text-gray-700 text-sm font-bold mb-2">
                                        Classe d'inscription
                                    </label>
                                    <select
                                        className="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        value={data.classe_id}
                                        onChange={(e) => setData('classe_id', e.target.value)}
                                        required
                                    >
                                        <option value="">-- Sélectionner une classe --</option>
                                        {classes.map((classe) => (
                                            <option key={classe.id} value={classe.id}>
                                                {classe.niveau.nom} - {classe.nom}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.classe_id && (
                                        <div className="text-red-500 text-xs mt-1">{errors.classe_id}</div>
                                    )}
                                </div>
                                
                                <div>
                                    <label className="block text-gray-700 text-sm font-bold mb-2">
                                        Date d'inscription
                                    </label>
                                    <input
                                        type="date"
                                        className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        value={data.date_inscription}
                                        onChange={(e) => setData('date_inscription', e.target.value)}
                                        required
                                    />
                                    {errors.date_inscription && (
                                        <div className="text-red-500 text-xs mt-1">{errors.date_inscription}</div>
                                    )}
                                </div>
                            </div>
                            
                            <div className="mb-6">
                                <div className="bg-gray-50 p-4 rounded-lg">
                                    <h3 className="text-lg font-medium mb-2">Paiement des frais</h3>
                                    
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <p className="text-sm text-gray-600">Frais d'inscription</p>
                                            <p className="font-medium">{totalFees.toLocaleString()} FCFA</p>
                                        </div>
                                        
                                        <div>
                                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                                Montant payé
                                            </label>
                                            <input
                                                type="number"
                                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                value={data.montant_paye}
                                                onChange={(e) => setData('montant_paye', e.target.value)}
                                                min="0"
                                                max={totalFees}
                                                required
                                            />
                                            {errors.montant_paye && (
                                                <div className="text-red-500 text-xs mt-1">{errors.montant_paye}</div>
                                            )}
                                        </div>
                                        
                                        <div>
                                            <p className="text-sm text-gray-600">Reste à payer</p>
                                            <p className="font-medium">{(totalFees - (data.montant_paye || 0)).toLocaleString()} FCFA</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="flex justify-end">
                                <button
                                    type="submit"
                                    className="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline"
                                    disabled={processing}
                                >
                                    Enregistrer l'inscription
                                </button>
                            </div>
                        </form>
                    </div>
                ) : searchIne ? (
                    <div className="bg-yellow-50 p-4 rounded-lg">
                        <div className="flex items-center text-yellow-700">
                            <span className="material-icons mr-2">warning</span>
                            <p>Aucun élève trouvé avec l'INE: {searchIne}</p>
                        </div>
                        <p className="mt-2 text-gray-600">
                            Veuillez vérifier l'INE ou importer l'élève en premier.
                        </p>
                    </div>
                ) : (
                    <div className="bg-blue-50 p-4 rounded-lg">
                        <div className="flex items-center text-blue-700">
                            <span className="material-icons mr-2">info</span>
                            <p>Recherchez un élève par son INE pour procéder à l'inscription.</p>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}