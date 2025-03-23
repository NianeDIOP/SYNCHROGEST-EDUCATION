<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $titre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 5px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .summary {
            margin-bottom: 20px;
            background-color: #f8f8f8;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titre }}</h1>
        <p>Année scolaire : {{ $parametres->annee_scolaire }}</p>
        <p>Généré le : {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if(isset($summary))
    <div class="summary">
        <h3>Résumé Financier</h3>
        <p>Total Recettes : {{ number_format($summary['totalRecettes'], 0, ',', ' ') }} FCFA</p>
        <p>Total Dépenses : {{ number_format($summary['totalDepenses'], 0, ',', ' ') }} FCFA</p>
        <p>Solde : {{ number_format($summary['solde'], 0, ',', ' ') }} FCFA</p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Catégorie</th>
                <th>Description</th>
                <th>Référence</th>
                <th>Montant</th>
                <th>Utilisateur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                <td>{{ ucfirst($transaction->type) }}</td>
                <td>{{ $transaction->categorie->nom }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ $transaction->reference ?: 'N/A' }}</td>
                <td>{{ number_format($transaction->montant, 0, ',', ' ') }} FCFA</td>
                <td>{{ $transaction->user->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $parametres->nom_etablissement }} - Rapport Financier</p>
    </div>
</body>
</html>