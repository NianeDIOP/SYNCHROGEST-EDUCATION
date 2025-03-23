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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $parametres->nom_etablissement ?? 'SYNCHROGEST-ÉDUCATION' }}</h1>
        <p>{{ $data['titre'] }}</p>
        <p>Année scolaire: {{ $data['annee_scolaire'] }}</p>
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
            
        @elseif($data['type_rapport'] === 'mensuel')
            <h2>Rapport pour la période du {{ \Carbon\Carbon::parse($data['periode']['debut'])->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($data['periode']['fin'])->format('d/m/Y') }}</h2>
            
            @if(count($data['transactions']) > 0)
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
                        @foreach($data['transactions'] as $transaction)
                            <tr>
                                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>{{ $transaction->categorie->nom }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td class="text-right">{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th class="text-right">{{ number_format($data['transactions']->sum('montant'), 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</th>
                        </tr>
                    </tfoot>
                </table>
            @else
                <p>Aucune transaction trouvée pour cette période.</p>
            @endif
            
        @elseif($data['type_rapport'] === 'categorie')
            <h2>Rapport pour la catégorie: {{ $data['categorie']->nom }}</h2>
            
            @if(count($data['transactions']) > 0)
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
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>