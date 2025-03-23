// resources/views/matieres/rapports/stock_pdf.blade.php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $parametres->nom_etablissement }}</h1>
        <p>{{ $titre }}</p>
        <p>Date de génération : {{ $dateGeneration }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Désignation</th>
                <th>Catégorie</th>
                <th>Quantité</th>
                <th>Seuil d'alerte</th>
                <th>Prix unitaire</th>
                <th>Valeur stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr>
                <td>{{ $article->code }}</td>
                <td>{{ $article->designation }}</td>
                <td>{{ $article->categorie->nom }}</td>
                <td>{{ $article->quantite_stock }} {{ $article->unite_mesure }}</td>
                <td>{{ $article->seuil_alerte }} {{ $article->unite_mesure }}</td>
                <td>{{ number_format($article->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($article->quantite_stock * $article->prix_unitaire, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6">Total</th>
                <th>{{ number_format($valeurTotale, 0, ',', ' ') }} FCFA</th>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p>Nombre d'articles: {{ $totalArticles }}</p>
        <p>SYNCHROGEST-EDUCATION - Tous droits réservés</p>
    </div>
</body>
</html>