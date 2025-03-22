import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { toast } from 'react-hot-toast';

export default function Parametres({ parametres, niveaux }) {
    const [isEditing, setIsEditing] = useState(false);
    
    const { data, setData, post, processing, errors } = useForm({
        nom_etablissement: parametres?.nom_etablissement || '',
        adresse: parametres?.adresse || '',
        telephone: parametres?.telephone || '',
        email: parametres?.email || '',
        annee_scolaire: parametres?.annee_scolaire || '',
        niveaux: niveaux || [],
    });
    
    // Pour gérer l'ajout d'un nouveau niveau
    const [nouveauNiveau, setNouveauNiveau] = useState({
        nom: '',
        frais_inscription: 0,
        frais_scolarite: 0,
        est_niveau_examen: false,
        frais_examen: 0,
        classes: [],
    });
    
    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('inscriptions.parametres'), {
            onSuccess: () => {
                toast.success('Paramètres enregistrés avec succès');
                setIsEditing(false);
            },
        });
    };
    
    const handleNiveauChange = (index, field, value) => {
        const updatedNiveaux = [...data.niveaux];
        updatedNiveaux[index][field] = value;
        setData('niveaux', updatedNiveaux);
    };
    
    const handleClasseChange = (niveauIndex, classeIndex, field, value) => {
        const updatedNiveaux = [...data.niveaux];
        updatedNiveaux[niveauIndex].classes[classeIndex][field] = value;
        setData('niveaux', updatedNiveaux);
    };
    
    const ajouterClasse = (niveauIndex) => {
        const updatedNiveaux = [...data.niveaux];
        updatedNiveaux[niveauIndex].classes.push({
            nom: '',
            capacite: 50,
        });
        setData('niveaux', updatedNiveaux);
    };
    
    const supprimerClasse = (niveauIndex, classeIndex) => {
        const updatedNiveaux = [...data.niveaux];
        updatedNiveaux[niveauIndex].classes.splice(classeIndex, 1);
        setData('niveaux', updatedNiveaux);
    };
    
    const ajouterNiveau = () => {
        if (!nouveauNiveau.nom) {
            toast.error('Veuillez spécifier un nom pour le niveau');
            return;
        }
        
        const updatedNiveaux = [...data.niveaux, { ...nouveauNiveau }];
        setData('niveaux', updatedNiveaux);
        
        // Réinitialiser le formulaire de nouveau niveau
        setNouveauNiveau({
            nom: '',
            frais_inscription: 0,
            frais_scolarite: 0,
            est_niveau_examen: false,
            frais_examen: 0,
            classes: [],
        });
    };
    
    const supprimerNiveau = (index) => {
        const updatedNiveaux = [...data.niveaux];
        updatedNiveaux.splice(index, 1);
        setData('niveaux', updatedNiveaux);
    };
    
    return (
        <AppLayout title="Paramètres - Module Inscription">
            <Head title="Paramètres - Module Inscription" />
            
            <div className="bg-white shadow rounded-lg p-6">
                <div className="flex justify-between items-center mb-6">
                    <h2 className="text-xl font-semibold">Paramètres Généraux</h2>
                    <button
                        type="button"
                        onClick={() => setIsEditing(!isEditing)}
                        className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        {isEditing ? 'Annuler' : 'Modifier'}
                    </button>
                </div>
                
                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                Nom de l'établissement
                            </label>
                            <input
                                type="text"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.nom_etablissement}
                                onChange={(e) => setData('nom_etablissement', e.target.value)}
                                disabled={!isEditing}
                                required
                            />
                            {errors.nom_etablissement && (
                                <div className="text-red-500 text-xs mt-1">{errors.nom_etablissement}</div>
                            )}
                        </div>
                        
                        <div>
                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                Adresse
                            </label>
                            <input
                                type="text"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.adresse}
                                onChange={(e) => setData('adresse', e.target.value)}
                                disabled={!isEditing}
                            />
                        </div>
                        
                        <div>
                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                Téléphone
                            </label>
                            <input
                                type="text"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.telephone}
                                onChange={(e) => setData('telephone', e.target.value)}
                                disabled={!isEditing}
                            />
                        </div>
                        
                        <div>
                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                Email
                            </label>
                            <input
                                type="email"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                disabled={!isEditing}
                            />
                        </div>
                        
                        <div>
                            <label className="block text-gray-700 text-sm font-bold mb-2">
                                Année Scolaire
                            </label>
                            <input
                                type="text"
                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value={data.annee_scolaire}
                                onChange={(e) => setData('annee_scolaire', e.target.value)}
                                placeholder="ex: 2024-2025"
                                disabled={!isEditing}
                                required
                            />
                            {errors.annee_scolaire && (
                                <div className="text-red-500 text-xs mt-1">{errors.annee_scolaire}</div>
                            )}
                        </div>
                    </div>
                    
                    <div className="mt-8">
                        <h2 className="text-xl font-semibold mb-4">Niveaux et Classes</h2>
                        
                        {data.niveaux.map((niveau, niveauIndex) => (
                            <div key={niveauIndex} className="bg-gray-50 p-4 rounded-lg mb-4">
                                <div className="flex justify-between items-center mb-2">
                                    <h3 className="text-lg font-medium">{niveau.nom || 'Nouveau niveau'}</h3>
                                    {isEditing && (
                                        <button
                                            type="button"
                                            onClick={() => supprimerNiveau(niveauIndex)}
                                            className="text-red-500 hover:text-red-700"
                                        >
                                            <span className="material-icons">delete</span>
                                        </button>
                                    )}
                                </div>
                                
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label className="block text-gray-700 text-sm font-bold mb-2">
                                            Nom du niveau
                                        </label>
                                        <input
                                            type="text"
                                            className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            value={niveau.nom}
                                            onChange={(e) => handleNiveauChange(niveauIndex, 'nom', e.target.value)}
                                            disabled={!isEditing}
                                            required
                                        />
                                    </div>
                                    
                                    <div>
                                        <label className="block text-gray-700 text-sm font-bold mb-2">
                                            Frais d'inscription
                                        </label>
                                        <input
                                            type="number"
                                            className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            value={niveau.frais_inscription}
                                            onChange={(e) => handleNiveauChange(niveauIndex, 'frais_inscription', parseFloat(e.target.value))}
                                            disabled={!isEditing}
                                            min="0"
                                            required
                                        />
                                    </div>
                                    
                                    <div>
                                        <label className="block text-gray-700 text-sm font-bold mb-2">
                                            Frais de scolarité
                                        </label>
                                        <input
                                            type="number"
                                            className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            value={niveau.frais_scolarite}
                                            onChange={(e) => handleNiveauChange(niveauIndex, 'frais_scolarite', parseFloat(e.target.value))}
                                            disabled={!isEditing}
                                            min="0"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <div className="flex items-center mb-4">
                                    <label className="flex items-center">
                                        <input
                                            type="checkbox"
                                            className="form-checkbox h-5 w-5 text-blue-600"
                                            checked={niveau.est_niveau_examen}
                                            onChange={(e) => handleNiveauChange(niveauIndex, 'est_niveau_examen', e.target.checked)}
                                            disabled={!isEditing}
                                        />
                                        <span className="ml-2 text-gray-700">Niveau d'examen</span>
                                    </label>
                                    
                                    {niveau.est_niveau_examen && (
                                        <div className="ml-6">
                                            <label className="block text-gray-700 text-sm font-bold">
                                                Frais d'examen
                                            </label>
                                            <input
                                                type="number"
                                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                value={niveau.frais_examen}
                                                onChange={(e) => handleNiveauChange(niveauIndex, 'frais_examen', parseFloat(e.target.value))}
                                                disabled={!isEditing}
                                                min="0"
                                                required
                                            />
                                        </div>
                                    )}
                                </div>
                                
                                <div className="mt-4">
                                    <h4 className="text-md font-medium mb-2">Classes</h4>
                                    
                                    {niveau.classes && niveau.classes.map((classe, classeIndex) => (
                                        <div key={classeIndex} className="flex items-center space-x-4 mb-2">
                                            <div className="flex-1">
                                                <label className="block text-gray-700 text-sm font-bold mb-1">
                                                    Nom de la classe
                                                </label>
                                                <input
                                                    type="text"
                                                    className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                    value={classe.nom}
                                                    onChange={(e) => handleClasseChange(niveauIndex, classeIndex, 'nom', e.target.value)}
                                                    disabled={!isEditing}
                                                    required
                                                />
                                            </div>
                                            
                                            <div className="flex-1">
                                                <label className="block text-gray-700 text-sm font-bold mb-1">
                                                    Capacité
                                                </label>
                                                <input
                                                    type="number"
                                                    className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                    value={classe.capacite}
                                                    onChange={(e) => handleClasseChange(niveauIndex, classeIndex, 'capacite', parseInt(e.target.value))}
                                                    disabled={!isEditing}
                                                    min="1"
                                                    required
                                                />
                                            </div>
                                            
                                            {isEditing && (
                                                <button
                                                    type="button"
                                                    onClick={() => supprimerClasse(niveauIndex, classeIndex)}
                                                    className="text-red-500 hover:text-red-700 mt-6"
                                                >
                                                    <span className="material-icons">remove_circle</span>
                                                </button>
                                            )}
                                        </div>
                                    ))}
                                    
                                    {isEditing && (
                                        <button
                                            type="button"
                                            onClick={() => ajouterClasse(niveauIndex)}
                                            className="mt-2 px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center"
                                        >
                                            <span className="material-icons mr-1">add</span>
                                            Ajouter une classe
                                        </button>
                                    )}
                                </div>
                            </div>
                        ))}
                        
                        {isEditing && (
                            <div className="bg-gray-50 p-4 rounded-lg mb-4">
                                <h3 className="text-lg font-medium mb-2">Ajouter un niveau</h3>
                                
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label className="block text-gray-700 text-sm font-bold mb-2">
                                            Nom du niveau
                                        </label>
                                        <input
                                            type="text"
                                            className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            value={nouveauNiveau.nom}
                                            onChange={(e) => setNouveauNiveau({...nouveauNiveau, nom: e.target.value})}
                                            required
                                        />
                                    </div>
                                    
                                    <div>
                                        <label className="block text-gray-700 text-sm font-bold mb-2">
                                            Frais d'inscription
                                        </label>
                                        <input
                                            type="number"
                                            className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            value={nouveauNiveau.frais_inscription}
                                            onChange={(e) => setNouveauNiveau({...nouveauNiveau, frais_inscription: parseFloat(e.target.value)})}
                                            min="0"
                                            required
                                        />
                                    </div>
                                    
                                    <div>
                                        <label className="block text-gray-700 text-sm font-bold mb-2">
                                            Frais de scolarité
                                        </label>
                                        <input
                                            type="number"
                                            className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            value={nouveauNiveau.frais_scolarite}
                                            onChange={(e) => setNouveauNiveau({...nouveauNiveau, frais_scolarite: parseFloat(e.target.value)})}
                                            min="0"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <div className="flex items-center mb-4">
                                    <label className="flex items-center">
                                        <input
                                            type="checkbox"
                                            className="form-checkbox h-5 w-5 text-blue-600"
                                            checked={nouveauNiveau.est_niveau_examen}
                                            onChange={(e) => setNouveauNiveau({...nouveauNiveau, est_niveau_examen: e.target.checked})}
                                        />
                                        <span className="ml-2 text-gray-700">Niveau d'examen</span>
                                    </label>
                                    
                                    {nouveauNiveau.est_niveau_examen && (
                                        <div className="ml-6">
                                            <label className="block text-gray-700 text-sm font-bold">
                                                Frais d'examen
                                            </label>
                                            <input
                                                type="number"
                                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                value={nouveauNiveau.frais_examen}
                                                onChange={(e) => setNouveauNiveau({...nouveauNiveau, frais_examen: parseFloat(e.target.value)})}
                                                min="0"
                                                required
                                            />
                                        </div>
                                    )}
                                </div>
                                
                                <button
                                    type="button"
                                    onClick={ajouterNiveau}
                                    className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                                >
                                    Ajouter ce niveau
                                </button>
                            </div>
                        )}
                    </div>
                    
                    {isEditing && (
                        <div className="mt-6 flex justify-end">
                            <button
                                type="submit"
                                className="px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                                disabled={processing}
                            >
                                Enregistrer les paramètres
                            </button>
                        </div>
                    )}
                </form>
            </div>
        </AppLayout>
    );
}