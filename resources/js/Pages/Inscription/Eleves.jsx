import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Eleves({ eleves, classes, filters }) {
    return (
        <AppLayout title="Liste des Élèves">
            <Head title="Liste des Élèves" />
            
            <div className="bg-white shadow rounded-lg p-6">
                <div className="flex justify-between items-center mb-6">
                    <h2 className="text-xl font-semibold">Liste des Élèves</h2>
                    <Link
                        href={route('inscriptions.import')}
                        className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    >
                        Importer des élèves
                    </Link>
                </div>
                
                <div className="mb-6">
                    <form>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label className="block text-gray-700 text-sm font-bold mb-2">
                                    Recherche
                                </label>
                                <input
                                    type="text"
                                    name="search"
                                    value={filters.search || ''}
                                    onChange={(e) => {
                                        window.location.href = route('inscriptions.eleves', {
                                            ...filters,
                                            search: e.target.value,
                                        });
                                    }}
                                    className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    placeholder="INE, nom ou prénom..."
                                />
                            </div>
                            
                            <div>
                                <label className="block text-gray-700 text-sm font-bold mb-2">
                                    Classe
                                </label>
                                <select
                                    name="classe_id"
                                    value={filters.classe_id || ''}
                                    onChange={(e) => {
                                        window.location.href = route('inscriptions.eleves', {
                                            ...filters,
                                            classe_id: e.target.value,
                                        });
                                    }}
                                    className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                >
                                    <option value="">Toutes les classes</option>
                                    {classes.map((classe) => (
                                        <option key={classe.id} value={classe.id}>
                                            {classe.niveau.nom} - {classe.nom}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            
                            <div>
                                <label className="block text-gray-700 text-sm font-bold mb-2">
                                    Statut
                                </label>
                                <select
                                    name="statut"
                                    value={filters.statut || ''}
                                    onChange={(e) => {
                                        window.location.href = route('inscriptions.eleves', {
                                            ...filters,
                                            statut: e.target.value,
                                        });
                                    }}
                                    className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                >
                                    <option value="">Tous les statuts</option>
                                    <option value="Nouveau">Nouveau</option>
                                    <option value="Ancien">Ancien</option>
                                    <option value="Redoublant">Redoublant</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div className="overflow-x-auto">
                    <table className="min-w-full bg-white">
                        <thead>
                            <tr className="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                <th className="py-3 px-6 text-left">INE</th>
                                <th className="py-3 px-6 text-left">Nom & Prénom</th>
                                <th className="py-3 px-6 text-left">Sexe</th>
                                <th className="py-3 px-6 text-left">Date de naissance</th>
                                <th className="py-3 px-6 text-left">Classe</th>
                                <th className="py-3 px-6 text-left">Statut</th>
                                <th className="py-3 px-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="text-gray-600 text-sm">
                            {eleves.data.length > 0 ? (
                                eleves.data.map((eleve) => (
                                    <tr key={eleve.id} className="border-b border-gray-200 hover:bg-gray-50">
                                        <td className="py-3 px-6 text-left whitespace-nowrap">
                                            {eleve.ine}
                                        </td>
                                        <td className="py-3 px-6 text-left">
                                            {eleve.nom} {eleve.prenom}
                                        </td>
                                        <td className="py-3 px-6 text-left">
                                            {eleve.sexe}
                                        </td>
                                        <td className="py-3 px-6 text-left">
                                            {new Date(eleve.date_naissance).toLocaleDateString()}
                                        </td>
                                        <td className="py-3 px-6 text-left">
                                            {eleve.classe.niveau.nom} - {eleve.classe.nom}
                                        </td>
                                        <td className="py-3 px-6 text-left">
                                            <span className={`py-1 px-3 rounded-full text-xs ${
                                                eleve.statut === 'Nouveau' ? 'bg-green-200 text-green-700' :
                                                eleve.statut === 'Ancien' ? 'bg-blue-200 text-blue-700' :
                                                'bg-yellow-200 text-yellow-700'
                                            }`}>
                                                {eleve.statut}
                                            </span>
                                        </td>
                                        <td className="py-3 px-6 text-center">
                                            <div className="flex item-center justify-center">
                                                <Link 
                                                    href={route('inscriptions.nouvelle', { ine: eleve.ine })}
                                                    className="text-blue-500 hover:text-blue-700 mx-2"
                                                    title="Inscrire"
                                                >
                                                    <span className="material-icons">how_to_reg</span>
                                                </Link>
                                                <button
                                                    className="text-green-500 hover:text-green-700 mx-2"
                                                    title="Voir détails"
                                                >
                                                    <span className="material-icons">visibility</span>
                                                </button>
                                                <button
                                                    className="text-red-500 hover:text-red-700 mx-2"
                                                    title="Supprimer"
                                                >
                                                    <span className="material-icons">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="7" className="py-6 text-center text-gray-500">
                                        Aucun élève trouvé
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
                
                <div className="mt-4">
                    <div className="flex items-center justify-between">
                        <div className="text-sm text-gray-600">
                            Affichage de {eleves.data.length} élèves sur {eleves.total}
                        </div>
                        
                        <div className="flex space-x-2">
                            {eleves.links.map((link, i) => (
                                <Link
                                    key={i}
                                    href={link.url}
                                    className={`px-3 py-1 rounded ${
                                        link.active ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'
                                    } ${!link.url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600 hover:text-white'}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}