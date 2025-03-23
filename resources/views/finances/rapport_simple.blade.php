<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['titre'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: center;
            font-size: 0.8em;
            color: #666;
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .text-right {
            text-align: right;
        }
        .summary {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .text-success {
            color: #28a745;
        }
        .text-danger {
            color: #dc3545;
        }
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 15px;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            z-index: 9999;
        }
        .print-button:hover {
            background-color: #2e59d9;
        }
        @media print {
            .print-button {
                display: none;
            }
            @page {
                size: A4;
                margin: 1cm;
            }
            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Imprimer / Enregistrer en PDF</button>
    
    <div class="header">
        <h1>{{ $parametres->nom_etablissement ?? 'SYNCHROGEST-ÉDUCATION' }}</h1>
        <p>{{ $data['titre'] }}</p>
        <p>Année scolaire: {{ $data['anneeScolaire'] ?? ($parametres->annee_scolaire ?? 'Non définie') }}</p>
        <p>Généré le: {{ $data['date_generation'] }}</p>
    </div>
    
    <div class="content">
        @if($data['type_rapport'] === 'general')
            <div class="summary">
                <h2>Résumé financier</h2>
                <table>
                    <tr>
                        <th>Total des recettes</th>
                        <td class="text-right">{{ number_format($data['totalRecettes'], 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                    </tr>
                    <tr>
                        <th>Total des dépenses</th>
                        <td class="text-right">{{ number_format($data['totalDepenses'], 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                    </tr>
                    <tr>
                        <th>Solde</th>
                        <td class="text-right {{ $data['solde'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($data['solde'], 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}
                        </td>
                    </tr>
                </table>
            </div>
            
            @if(isset($data['transactions']) && count($data['transactions']) > 0)
                <h2>Liste des transactions</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th>Référence</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['transactions'] as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>{{ $transaction->categorie->nom }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->reference ?: 'N/A' }}</td>
                                <td class="text-right">{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
        @elseif($data['type_rapport'] === 'mensuel')
            <h2>Rapport pour la période du {{ \Carbon\Carbon::parse($data['periode']['debut'])->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($data['periode']['fin'])->format('d/m/Y') }}</h2>
            
            <div class="summary">
                <h3>Résumé de la période</h3>
                <table>
                    <tr>
                        <th>Total des recettes</th>
                        <td class="text-right">{{ number_format($data['totalRecettes'], 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                    </tr>
                    <tr>
                        <th>Total des dépenses</th>
                        <td class="text-right">{{ number_format($data['totalDepenses'], 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                    </tr>
                    <tr>
                        <th>Solde</th>
                        <td class="text-right {{ $data['solde'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($data['solde'], 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}
                        </td>
                    </tr>
                </table>
            </div>
            
            @if(isset($data['transactions']) && count($data['transactions']) > 0)
                <h3>Détail des transactions</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th>Référence</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['transactions'] as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>{{ $transaction->categorie->nom }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->reference ?: 'N/A' }}</td>
                                <td class="text-right">{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Total</th>
                            <th class="text-right">{{ number_format($data['transactions']->sum('montant'), 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</th>
                        </tr>
                    </tfoot>
                </table>
            @else
                <p>Aucune transaction trouvée pour cette période.</p>
            @endif
            
        @elseif($data['type_rapport'] === 'categorie')
            <h2>Rapport pour la catégorie: {{ $data['categorie']->nom }}</h2>
            <p><strong>Type de catégorie:</strong> {{ ucfirst($data['categorie']->type) }}</p>
            @if($data['categorie']->description)
                <p><strong>Description:</strong> {{ $data['categorie']->description }}</p>
            @endif
            
            @if(isset($data['transactions']) && count($data['transactions']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Référence</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['transactions'] as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->reference ?: 'N/A' }}</td>
                                <td class="text-right">{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total</th>
                            <th class="text-right">{{ number_format($data['transactions']->sum('montant'), 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</th>
                        </tr>
                    </tfoot>
                </table>
            @else
                <p>Aucune transaction trouvée pour cette catégorie.</p>
            @endif
        @endif
    </div>
    
    <div class="footer">
        <p>SYNCHROGEST-ÉDUCATION - Système de Gestion Intégré pour Établissements Scolaires</p>
        <p>Document généré automatiquement. Page 1/1</p>
    </div>
    
    <script>
        // S'exécute quand la page est complètement chargée
        window.onload = function() {
            // Demande automatiquement l'impression après un court délai
            // pour permettre au contenu de se charger complètement
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>