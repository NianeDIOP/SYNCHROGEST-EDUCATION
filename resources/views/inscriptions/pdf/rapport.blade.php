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

    <table>
        <thead>
            <tr>
                <th>INE</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Sexe</th>
                <th>Date naissance</th>
                <th>Niveau</th>
                <th>Classe</th>
                <th>Statut</th>
                <th>Date inscription</th>
                <th>Montant payé</th>
                <th>Reste à payer</th>
                <th>Statut paiement</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inscriptions as $inscription)
            <tr>
                <td>{{ $inscription->eleve->ine }}</td>
                <td>{{ $inscription->eleve->nom }}</td>
                <td>{{ $inscription->eleve->prenom }}</td>
                <td>{{ $inscription->eleve->sexe }}</td>
                <td>{{ $inscription->eleve->date_naissance->format('d/m/Y') }}</td>
                <td>{{ $inscription->classe->niveau->nom }}</td>
                <td>{{ $inscription->classe->nom }}</td>
                <td>{{ $inscription->eleve->statut }}</td>
                <td>{{ $inscription->date_inscription->format('d/m/Y') }}</td>
                <td>{{ number_format($inscription->montant_paye, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($inscription->montant_restant, 0, ',', ' ') }} FCFA</td>
                <td>{{ $inscription->statut_paiement }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $parametres->nom_etablissement }} - Rapport d'inscriptions</p>
    </div>
</body>
</html>