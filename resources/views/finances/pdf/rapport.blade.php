<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            margin: 5px 0;
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
            font-size: 10px;
            color: #666;
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
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
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $parametres->nom_etablissement ?? 'SYNCHROGEST-ÉDUCATION' }}</h1>
        <p>{{ $titre }}</p>
        <p>Année scolaire: {{ $anneeScolaire }}</p>
        <p>Généré le: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    <div class="content">
        @if($type === 'general')
            <div class="summary">
                <h2>Résumé financier</h2>
                <table>
                    <tr>
                        <th>Total des recettes</th>
                        <td class="text-right">{{ number_format($totalRecettes, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                    </tr>
                    <tr>
                        <th>Total des dépenses</th>
                        <td class="text-right">{{ number_format($totalDepenses, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                    </tr>
                    <tr>
                        <th>Solde</th>
                        <td class="text-right {{ $solde >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($solde, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}
                        </td>
                    </tr>
                </table>
            </div>
            
            @if(count($transactions) > 0)
                <h3>Liste des transactions</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>{{ $transaction->categorie->nom }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td class="text-right">{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Aucune transaction enregistrée pour cette période.</p>
            @endif
            
        @elseif($type === 'mensuel')
            <h2>Rapport pour la période du {{ $periode['debut']->format('d/m/Y') }} au {{ $periode['fin']->format('d/m/Y') }}</h2>
            
            <div class="summary">
                <h3>Résumé de la période</h3>
                <table>
                    <tr>
                        <th>Total des recettes</th>
                        <td class="text-right">{{ number_format($totalRecettes, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                    </tr>
                    <tr>
                        <th>Total des dépenses</th>
                        <td class="text-right">{{ number_format($totalDepenses, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                    </tr>
                    <tr>
                        <th>Solde de la période</th>
                        <td class="text-right {{ $solde >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($solde, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}
                        </td>
                    </tr>
                </table>
            </div>
            
            @if(count($transactions) > 0)
                <h3>Liste des transactions</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Catégorie</th>
                            <th>Description</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>{{ $transaction->categorie->nom }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td class="text-right">{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Aucune transaction trouvée pour cette période.</p>
            @endif
            
        @elseif($type === 'categorie')
            <h2>Rapport pour la catégorie: {{ $categorie->nom }}</h2>
            <p><strong>Type de catégorie:</strong> {{ ucfirst($categorie->type) }}</p>
            
            <div class="summary">
                <h3>Résumé</h3>
                <table>
                    <tr>
                        <th>Nombre de transactions</th>
                        <td class="text-right">{{ count($transactions) }}</td>
                    </tr>
                    <tr>
                        <th>Montant total</th>
                        <td class="text-right">{{ number_format($transactions->sum('montant'), 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                    </tr>
                </table>
            </div>
            
            @if(count($transactions) > 0)
                <h3>Liste des transactions</h3>
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
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->reference ?: 'N/A' }}</td>
                                <td class="text-right">{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Aucune transaction trouvée pour cette catégorie.</p>
            @endif
        @endif
    </div>
    
    <div class="footer">
        <p>SYNCHROGEST-ÉDUCATION - Système de Gestion Intégré pour Établissements Scolaires</p>
        <p>Document généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>