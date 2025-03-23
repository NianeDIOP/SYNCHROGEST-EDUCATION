<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SYNCHROGEST-EDUCATION | Système de Gestion Scolaire</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-inscription: #0d6efd;
            --color-finance: #198754;
            --color-matiere: #6f42c1;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            --transition-normal: all 0.3s ease;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            line-height: 1.6;
        }
        
        .bg-gradient-dark {
            background: linear-gradient(135deg, #343a40 0%, #212529 100%);
        }
        
        .hero-section {
            min-height: 40vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(135deg, #0d6efd30 0%, #19875430 50%, #6f42c130 100%);
            transform: rotate(-45deg);
            z-index: 0;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .logo-text {
            font-weight: 700;
            letter-spacing: -1px;
            background: linear-gradient(45deg, #0d6efd, #6f42c1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .module-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: var(--transition-normal);
            box-shadow: var(--shadow-sm);
            height: 100%;
        }
        
        .module-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-md);
        }
        
        .module-card .card-header {
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            height: 110px;
        }
        
        .module-card .card-header::after {
            content: '';
            position: absolute;
            right: -20px;
            top: -20px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .card-header.inscription { 
            background: linear-gradient(135deg, var(--color-inscription) 0%, #0099ff 100%);
            color: white;
        }
        
        .card-header.finance { 
            background: linear-gradient(135deg, var(--color-finance) 0%, #20c997 100%);
            color: white;
        }
        
        .card-header.matiere { 
            background: linear-gradient(135deg, var(--color-matiere) 0%, #9461fb 100%);
            color: white;
        }
        
        .module-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .module-title {
            font-weight: 600;
            margin-bottom: 0;
            position: relative;
            z-index: 2;
        }
        
        .btn-access {
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: var(--transition-normal);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }
        
        .btn-inscription {
            background-color: var(--color-inscription);
            border-color: var(--color-inscription);
        }
        
        .btn-finance {
            background-color: var(--color-finance);
            border-color: var(--color-finance);
        }
        
        .btn-matiere {
            background-color: var(--color-matiere);
            border-color: var(--color-matiere);
        }
        
        .btn-access:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }
        
        .footer {
            background-color: #ffffff;
            padding: 1.5rem 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .feature-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
            color: #fff;
        }
        
        .bg-feature-1 { background-color: #0d6efd; }
        .bg-feature-2 { background-color: #198754; }
        .bg-feature-3 { background-color: #6f42c1; }
        
        .feature-text {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                SYNCHROGEST-EDUCATION
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">À Propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="{{ route('login') }}">Connexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section bg-gradient-dark text-white text-center">
        <div class="container hero-content py-5">
            <h1 class="display-4 fw-bold mb-3 logo-text">SYNCHROGEST-EDUCATION</h1>
            <p class="lead mb-4">Système de Gestion Intégré pour Établissements Scolaires</p>
            <p class="mb-5 col-md-8 mx-auto">Une solution complète pour simplifier la gestion administrative, financière et logistique de votre établissement scolaire.</p>
            <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4 py-2 fw-medium">
                Commencer maintenant <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-icon bg-feature-1">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h5 class="fw-bold">Gestion simplifiée</h5>
                    <p class="feature-text">Gérez efficacement toutes les activités de votre établissement depuis une seule plateforme.</p>
                </div>
                <div class="col-md-4">
                    <div class="feature-icon bg-feature-2">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="fw-bold">Rapports détaillés</h5>
                    <p class="feature-text">Obtenez des analyses et des rapports pertinents pour prendre les meilleures décisions.</p>
                </div>
                <div class="col-md-4">
                    <div class="feature-icon bg-feature-3">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="fw-bold">Sécurité avancée</h5>
                    <p class="feature-text">Vos données sont protégées avec les plus hauts standards de sécurité et confidentialité.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Nos Modules</h2>
                <p class="text-muted col-md-8 mx-auto">Une solution complète qui répond à tous vos besoins de gestion scolaire</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Module Inscriptions -->
                <div class="col-md-4">
                    <div class="card module-card">
                        <div class="card-header inscription">
                            <div class="module-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h3 class="module-title">Module Inscriptions</h3>
                        </div>
                        <div class="card-body p-4">
                            <p class="card-text mb-4">Simplifiez le processus d'inscription des élèves avec des fonctionnalités avancées :</p>
                            <ul class="mb-4">
                                <li>Gestion des dossiers d'élèves</li>
                                <li>Importation de listes</li>
                                <li>Génération de reçus automatiques</li>
                                <li>Suivi des paiements</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4">
                            <a href="{{ route('login') }}" class="btn btn-inscription btn-access text-white w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Accéder
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Module Finances -->
                <div class="col-md-4">
                    <div class="card module-card">
                        <div class="card-header finance">
                            <div class="module-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <h3 class="module-title">Module Finances</h3>
                        </div>
                        <div class="card-body p-4">
                            <p class="card-text mb-4">Gérez efficacement les finances de votre établissement :</p>
                            <ul class="mb-4">
                                <li>Suivi des entrées et sorties</li>
                                <li>Catégorisation des transactions</li>
                                <li>Tableaux de bord financiers</li>
                                <li>Génération de rapports détaillés</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4">
                            <a href="{{ route('login') }}" class="btn btn-finance btn-access text-white w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Accéder
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Module Matières -->
                <div class="col-md-4">
                    <div class="card module-card">
                        <div class="card-header matiere">
                            <div class="module-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h3 class="module-title">Module Matières</h3>
                        </div>
                        <div class="card-body p-4">
                            <p class="card-text mb-4">Optimisez la gestion de vos ressources matérielles :</p>
                            <ul class="mb-4">
                                <li>Gestion d'inventaire</li>
                                <li>Suivi des stocks</li>
                                <li>Planification des approvisionnements</li>
                                <li>Gestion des fournisseurs</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4">
                            <a href="{{ route('login') }}" class="btn btn-matiere btn-access text-white w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Accéder
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© {{ date('Y') }} SYNCHROGEST-EDUCATION. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-decoration-none text-muted me-3">Politique de confidentialité</a>
                    <a href="#" class="text-decoration-none text-muted">Conditions d'utilisation</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>