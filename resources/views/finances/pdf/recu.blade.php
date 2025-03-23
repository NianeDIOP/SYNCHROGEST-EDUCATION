<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Transaction</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
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
            color: #0d6efd;
        }
        .header h2 {
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 5px;
        }
        .receipt-number {
            text-align: right;
            margin-bottom: 20px;
        }
        .receipt-number p {
            margin: 5px 0;
        }
        .receipt-number .number {
            font-weight: bold;
            color: #0d6efd;
        }
        .info-section {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
        }
        .info-box {
            width: 48%;
            padding: 15px;
            border-radius: 5px;
        }
        .info-general {
            background-color: #f8f9fa;
        }
        .info-financial {
            background-color: {{ $transaction->type == 'recette' ? '#d4edda' : '#f8d7da' }};
        }
        .info-box h5 {
            margin-top: 0;
            margin-bottom: 10px;
            color: {{ $transaction->type == 'recette' ? '#28a745' : '#dc3545' }};
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            background-color: {{ $transaction->type == 'recette' ? '#28a745' : '#dc3545' }};
            border-radius: 10px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            margin-bottom: 30px;
        }
        .signature-box {
            width: 40%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 10px;
            color: #6c757d;
        }
        .mb-1 {
            margin-bottom: 5px;
        }
        .fs-4 {
            font-size: 16px;
        }
        .fw-bold {
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $parametres->nom_etablissement ?? 'SYNCHROGEST-ÉDUCATION' }}</h1>
            @if(isset($parametres->adresse) && $parametres->adresse)
                <p class="mb-1">{{ $parametres->adresse }}</p>
            @endif
            @if(isset($parametres->telephone) && $parametres->telephone)
                <p class="mb-1">Tél: {{ $parametres->telephone }}</p>
            @endif
            @if(isset($parametres->email) && $parametres->email)
                <p class="mb-1">{{ $parametres->email }}</p>
            @endif
            
            <h2>REÇU DE {{ $transaction->type == 'recette' ? 'RECETTE' : 'DÉPENSE' }}</h2>
            <p>Année scolaire: {{ $parametres->annee_scolaire ?? '' }}</p>
        </div>
        
        <div class="receipt-number">
            <p class="number">N° {{ $transaction->reference ?: 'TR-'.str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p>Date: {{ $transaction->date->format('d/m/Y') }}</p>
        </div>
        
        <div class="info-section">
            <div class="info-box info-general">
                <h5>Informations générales</h5>
                <p class="mb-1"><strong>Type:</strong> 
                    <span class="badge">
                        {{ ucfirst($transaction->type) }}
                    </span>
                </p>
                <p class="mb-1"><strong>Catégorie:</strong> {{ $transaction->categorie->nom }}</p>
                <p class="mb-1"><strong>Description:</strong> {{ $transaction->description }}</p>
            </div>
            
            <div class="info-box info-financial">
                <h5>Détails financiers</h5>
                <p class="mb-1"><strong>Montant:</strong> <span class="fs-4 fw-bold">{{ number_format($transaction->montant, 0, ',', ' ') }} {{ $parametres->devise ?? 'FCFA' }}</span></p>
                <p class="mb-1"><strong>Référence:</strong> {{ $transaction->reference ?: 'N/A' }}</p>
                <p class="mb-1"><strong>Enregistré par:</strong> {{ $transaction->user->name }}</p>
            </div>
        </div>
        
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Signature du payeur</p>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Cachet et signature</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Ce reçu est un document officiel. Veuillez le conserver soigneusement.</p>
            <p>SYNCHROGEST-ÉDUCATION - Système de Gestion Intégré pour Établissements Scolaires</p>
        </div>
    </div>
</body>
</html>