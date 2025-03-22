import React from 'react';
import { Head, Link } from '@inertiajs/react';

export default function Welcome() {
    return (
        <>
            <Head title="SYNCHROGEST-EDUCATION" />
            <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
                <div className="text-center mb-10">
                    <h1 className="text-4xl font-bold text-gray-900">SYNCHROGEST-EDUCATION</h1>
                    <p className="mt-2 text-gray-600">Système de Gestion Intégré pour Établissements Scolaires</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div className="p-6 bg-blue-600 border-b border-gray-200">
                            <h2 className="text-2xl font-bold text-white">Module Inscriptions</h2>
                        </div>
                        <div className="p-6 bg-white border-b border-gray-200">
                            <p className="text-gray-700 mb-4">
                                Gérez les inscriptions des élèves, importez des listes, et générez des reçus et des rapports.
                            </p>
                            <Link
                                href={route('login')}
                                className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition"
                            >
                                Accéder
                            </Link>
                        </div>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div className="p-6 bg-green-600 border-b border-gray-200">
                            <h2 className="text-2xl font-bold text-white">Module Finances</h2>
                        </div>
                        <div className="p-6 bg-white border-b border-gray-200">
                            <p className="text-gray-700 mb-4">
                                Gérez les finances de l'établissement, suivez les entrées et sorties, et générez des rapports financiers.
                            </p>
                            <Link
                                href={route('login')}
                                className="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition"
                            >
                                Accéder
                            </Link>
                        </div>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                        <div className="p-6 bg-purple-600 border-b border-gray-200">
                            <h2 className="text-2xl font-bold text-white">Module Matières</h2>
                        </div>
                        <div className="p-6 bg-white border-b border-gray-200">
                            <p className="text-gray-700 mb-4">
                                Gérez les ressources matérielles, suivez les stocks, et planifiez les approvisionnements.
                            </p>
                            <Link
                                href={route('login')}
                                className="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition"
                            >
                                Accéder
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}