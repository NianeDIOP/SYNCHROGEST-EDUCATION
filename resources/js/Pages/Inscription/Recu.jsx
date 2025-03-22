import React, { useRef } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Recu({ inscription, parametres }) {
    const printRef = useRef();
    
    const handlePrint = () => {
        const content = printRef.current;
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>Reçu d'inscription - ${inscription.eleve.prenom} ${inscription.eleve.nom}</title>
                    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
                    <style>
                        body {
                            font-family: 'Roboto', sans-serif;
                            padding: 20px;
                            color: #333;
                        }
                        .container {
                            max-width: 800px;
                            margin: 0 auto;
                            border: 1px solid #ddd;
                            padding: 20px;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 30px;
                            border-bottom: 2px solid #3b82f6;
                            padding-bottom: 10px;
                        }
                        .title {
                            font-size: 24px;
                            font-weight: bold;
                            color: #3b82f6;
                            margin: 10px 0;
                        }
                        .subtitle {
                            font-size: 16px;
                            color: #666;
                            margin: 5px 0;
                        }
                        .info-section {
                            margin-bottom: 20px;
                        }
                        .info-section h3 {
                            font-size: 18px;
                            margin-bottom: 10px;
                            color: #3b82f6;
                        }
                        .info-grid {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 15px;
                        }
                        .info-item {
                            margin-bottom: 10px;
                        }
                        .info-label {
                            font-size: 12px;
                            color: #666;
                        }
                        .info-value {
                            font-weight: 500;
                        }
                        .payment-section {
                            background-color: #f9fafb;
                            padding: 15px;
                            border-radius: 5px;
                            margin-bottom: 20px;
                        }
                        .footer {
                            margin-top: 40px;
                            text-align: center;
                            font-size: 14px;
                            color: #666;
                        }
                        .signature {
                            margin-top: 60px;
                            display: flex;
                            justify-content: space-between;
                        }
                        .signature-box {
                            border-top: 1px solid #ddd;
                            width: 200px;
                            padding-top: 5px;
                            text-align: center;
                            font-weight: 500;
                        }
                        .receipt-number {
                            font-size: 16px;
                            font-weight: bold;
                            color: #3b82f6;
                            text-align: right;
                            margin-bottom: 20px;
                        }
                        @media print {
                            @page { size: auto; margin: 10mm; }
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${content.innerHTML}
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
    
    const totalFrais = inscription.montant_paye + inscription.montant_restant;
    
    return (
        <AppLayout title="Reçu d'Inscription">
            <Head title="Reçu d'Inscription" />
            
            <div className="bg-white shadow rounded-lg p-6">
                <div className="flex justify-between items-center mb-6">
                    <h2 className="text-xl font-semibold">Reçu d'Inscription</h2>
                    <div className="flex space-x-2">
                        <button
                            onClick={handlePrint}
                            className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center"
                        >
                            <span className="material-icons mr-1">print</span>
                            Imprimer
                        </button>
                        <Link
                            href={route('inscriptions.nouvelle')}
                            className="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center"
                        >
                            <span className="material-icons mr-1">arrow_back</span>
                            Retour
                        </Link>
                    </div>
                </div>
                
                <div ref={printRef} className="border border-gray-200 rounded-lg p-6">
                    <div className="text-center mb-8 pb-4 border-b border-blue-500">
                        <h1 className="text-2xl font-bold text-blue-500 mb-2">
                            {parametres?.nom_etablissement || 'SYNCHROGEST-ÉDUCATION'}
                        </h1>
                        <p className="text-gray-600 mb-1">{parametres?.adresse || ''}</p>
                        <p className="text-gray-600 mb-1">Tél: {parametres?.telephone || ''}</p>
                        <p className="text-gray-600">{parametres?.email || ''}</p>
                        
                        <h2 className="text-xl font-bold mt-6">REÇU D'INSCRIPTION</h2>
                        <p className="text-gray-600">Année scolaire: {parametres?.annee_scolaire || ''}</p>
                    </div>
                    
                    <div className="text-right mb-6">
                        <p className="text-blue-500 font-bold">N° {inscription.numero_recu}</p>
                        <p className="text-gray-600">Date: {new Date(inscription.date_inscription).toLocaleDateString()}</p>
                    </div>
                    
                    <div className="mb-6">
                        <h3 className="text-lg font-medium text-blue-500 mb-3">Informations de l'élève</h3>
                        
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <p className="text-sm text-gray-600">INE</p>
                                <p className="font-medium">{inscription.eleve.ine}</p>
                            </div>
                            
                            <div>
                                <p className="text-sm text-gray-600">Nom & Prénom</p>
                                <p className="font-medium">{inscription.eleve.nom} {inscription.eleve.prenom}</p>
                            </div>
                            
                            <div>
                                <p className="text-sm text-gray-600">Date de naissance</p>
                                <p className="font-medium">{new Date(inscription.eleve.date_naissance).toLocaleDateString()}</p>
                            </div>
                            
                            <div>
                                <p className="text-sm text-gray-600">Classe</p>
                                <p className="font-medium">{inscription.classe.niveau.nom} - {inscription.classe.nom}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 className="text-lg font-medium text-blue-500 mb-3">Détails du paiement</h3>
                        
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <p className="text-sm text-gray-600">Total des frais</p>
                                <p className="font-medium">{totalFrais.toLocaleString()} FCFA</p>
                            </div>
                            
                            <div>
                                <p className="text-sm text-gray-600">Montant payé</p>
                                <p className="font-medium">{inscription.montant_paye.toLocaleString()} FCFA</p>
                            </div>
                            
                            <div>
                                <p className="text-sm text-gray-600">Reste à payer</p>
                                <p className="font-medium">{inscription.montant_restant.toLocaleString()} FCFA</p>
                            </div>
                            
                            <div>
                                <p className="text-sm text-gray-600">Statut du paiement</p>
                                <p className={`font-medium ${
                                    inscription.statut_paiement === 'Complet' ? 'text-green-600' :
                                    inscription.statut_paiement === 'Partiel' ? 'text-yellow-600' :
                                    'text-red-600'
                                }`}>
                                    {inscription.statut_paiement}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="mt-12 flex justify-between">
                        <div className="border-t border-gray-300 w-48 pt-1 text-center">
                            <p className="font-medium">Signature de l'élève/parent</p>
                        </div>
                        
                        <div className="border-t border-gray-300 w-48 pt-1 text-center">
                            <p className="font-medium">Cachet et signature</p>
                        </div>
                    </div>
                    
                    <div className="mt-8 text-center text-gray-500 text-sm">
                        <p>Ce reçu est un document officiel. Veuillez le conserver soigneusement.</p>
                        <p className="mt-1">SYNCHROGEST-ÉDUCATION - Système de Gestion Intégré pour Établissements Scolaires</p>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}