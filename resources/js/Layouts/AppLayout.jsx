import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

const AppLayout = ({ children, title }) => {
    const { auth } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(true);
    
    // Déterminer le module actif basé sur le profil de l'utilisateur
    let moduleLinks = [];
    let moduleColor = 'bg-blue-600';
    
    if (auth.user) {
        switch (auth.user.profil) {
            case 'inscription':
                moduleLinks = [
                    { name: 'Tableau de bord', href: route('inscriptions.dashboard'), icon: 'dashboard' },
                    { name: 'Paramètres', href: route('inscriptions.parametres'), icon: 'settings' },
                    { name: 'Importation', href: route('inscriptions.import'), icon: 'upload' },
                    { name: 'Élèves', href: route('inscriptions.eleves'), icon: 'people' },
                    { name: 'Nouvelle inscription', href: route('inscriptions.nouvelle'), icon: 'add_circle' },
                    { name: 'Rapports', href: route('inscriptions.rapports'), icon: 'assessment' },
                ];
                moduleColor = 'bg-blue-600';
                break;
            case 'finance':
                moduleLinks = [
                    { name: 'Tableau de bord', href: route('finances.dashboard'), icon: 'dashboard' },
                    { name: 'Paramètres', href: route('finances.parametres'), icon: 'settings' },
                    // Autres liens du module finance
                ];
                moduleColor = 'bg-green-600';
                break;
            case 'matiere':
                moduleLinks = [
                    { name: 'Tableau de bord', href: route('matieres.dashboard'), icon: 'dashboard' },
                    { name: 'Paramètres', href: route('matieres.parametres'), icon: 'settings' },
                    // Autres liens du module matière
                ];
                moduleColor = 'bg-purple-600';
                break;
            default:
                moduleLinks = [];
        }
    }
    
    return (
        <div className="min-h-screen bg-gray-100">
            {/* Barre de navigation supérieure */}
            <nav className={`${moduleColor} shadow-md fixed w-full z-10`}>
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            <button
                                onClick={() => setSidebarOpen(!sidebarOpen)}
                                className="text-white focus:outline-none px-2"
                            >
                                <span className="material-icons">menu</span>
                            </button>
                            <div className="flex-shrink-0 flex items-center">
                                <span className="text-white text-xl font-bold">SYNCHROGEST-EDUCATION</span>
                            </div>
                        </div>
                        <div className="flex items-center">
                            {auth.user && (
                                <div className="ml-3 relative">
                                    <div className="flex items-center text-white">
                                        <span className="material-icons mr-1">person</span>
                                        <span>{auth.user.name}</span>
                                        <Link
                                            href={route('logout')}
                                            method="post"
                                            as="button"
                                            className="ml-4 text-white hover:text-gray-200"
                                        >
                                            <span className="material-icons">logout</span>
                                        </Link>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </nav>

            {/* Barre latérale */}
            <div
                className={`fixed inset-0 flex z-40 lg:inset-y-0 transform ${
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                } transition-transform duration-300 ease-in-out lg:translate-x-0`}
                style={{ top: '64px' }}
            >
                <div className="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-xl">
                    <div className="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div className="px-2 space-y-1">
                            {moduleLinks.map((item) => (
                                <Link
                                    key={item.name}
                                    href={item.href}
                                    className="group flex items-center px-2 py-2 text-base font-medium rounded-md hover:bg-gray-100"
                                >
                                    <span className="material-icons mr-3 text-gray-500">{item.icon}</span>
                                    {item.name}
                                </Link>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            {/* Contenu principal */}
            <main
                className={`flex-1 relative z-0 overflow-y-auto focus:outline-none transition-all duration-300 ease-in-out ${
                    sidebarOpen ? 'ml-64' : 'ml-0'
                }`}
                style={{ marginTop: '64px' }}
            >
                <div className="py-6">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <h1 className="text-2xl font-semibold text-gray-900">{title}</h1>
                    </div>
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-4">
                        {children}
                    </div>
                </div>
            </main>
        </div>
    );
};

export default AppLayout;