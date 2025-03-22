<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SYNCHROGEST-EDUCATION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .module-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-header.inscription { background-color: #0d6efd; color: white; }
        .card-header.finance { background-color: #198754; color: white; }
        .card-header.matiere { background-color: #6f42c1; color: white; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold">SYNCHROGEST-EDUCATION</h1>
            <p class="lead">Système de Gestion Intégré pour Établissements Scolaires</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card h-100 module-card">
                    <div class="card-header inscription py-3">
                        <h3 class="card-title mb-0">Module Inscriptions</h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Gérez les inscriptions des élèves, importez des listes, et générez des reçus et des rapports.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">Accéder</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 module-card">
                    <div class="card-header finance py-3">
                        <h3 class="card-title mb-0">Module Finances</h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Gérez les finances de l'établissement, suivez les entrées et sorties, et générez des rapports financiers.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('login') }}" class="btn btn-success w-100">Accéder</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 module-card">
                    <div class="card-header matiere py-3">
                        <h3 class="card-title mb-0">Module Matières</h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Gérez les ressources matérielles, suivez les stocks, et planifiez les approvisionnements.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('login') }}" class="btn btn-purple w-100" style="background-color: #6f42c1; color: white;">Accéder</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>