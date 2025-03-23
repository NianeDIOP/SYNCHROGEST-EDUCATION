<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SYNCHROGEST-EDUCATION')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome (pour les icônes) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #2e59d9;
            --secondary: #6c757d;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --topbar-height: 60px;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--sidebar-width);
            padding-top: var(--topbar-height);
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 10;
            transition: all 0.3s;
        }
        
        .sidebar-collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-collapsed .sidebar .nav-item .nav-link span,
        .sidebar-collapsed .sidebar .sidebar-brand-text,
        .sidebar-collapsed .sidebar .sidebar-heading {
            display: none;
        }
        
        .sidebar-collapsed .sidebar .nav-item .nav-link {
            text-align: center;
            padding: 15px 0;
        }
        
        .sidebar-collapsed .sidebar .nav-item .nav-link i {
            margin-right: 0;
            font-size: 1.2rem;
        }
        
        /* Content Wrapper */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 20px;
            transition: all 0.3s;
        }
        
        .sidebar-collapsed .content-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Topbar */
        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: var(--topbar-height);
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 11;
            padding: 0 20px;
        }
        
        .topbar .dropdown-menu {
            right: 0;
            left: auto;
        }
        
        /* Navigation */
        .sidebar .nav-item {
            position: relative;
        }
        
        .sidebar .nav-item .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .sidebar .nav-item .nav-link i {
            margin-right: 12px;
        }
        
        .sidebar .nav-item .nav-link:hover,
        .sidebar .nav-item .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 5px;
            margin: 0 10px;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 1rem 10px;
        }
        
        .sidebar-heading {
            padding: 0 20px;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            font-weight: 700;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 20px;
        }
        
        .card .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-header .card-title {
            margin-bottom: 0;
            font-weight: 700;
        }
        
        /* Border Cards */
        .border-left-primary {
            border-left: 0.25rem solid var(--primary) !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid var(--success) !important;
        }
        
        .border-left-info {
            border-left: 0.25rem solid var(--info) !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid var(--warning) !important;
        }
        
        .border-left-danger {
            border-left: 0.25rem solid var(--danger) !important;
        }
        
        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }
        
        /* Pagination */
        .pagination {
            margin-bottom: 0;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .page-link {
            color: var(--primary);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
            }
            
            .content-wrapper {
                margin-left: var(--sidebar-collapsed-width);
            }
            
            .sidebar .nav-item .nav-link span,
            .sidebar .sidebar-brand-text,
            .sidebar .sidebar-heading {
                display: none;
            }
            
            .sidebar .nav-item .nav-link {
                text-align: center;
                padding: 15px 0;
            }
            
            .sidebar .nav-item .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    @auth
    <!-- Topbar -->
    <nav class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button id="sidebarToggleBtn" class="btn">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="h5 mb-0 ms-3">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="d-flex align-items-center">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                    <i class="fas fa-user-circle fa-lg"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                        Profil
                    </a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                        Paramètres
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand text-center text-white mb-4">
            <div class="sidebar-brand-icon">
                <i class="fas fa-school fa-2x"></i>
            </div>
            <div class="sidebar-brand-text mt-2">SYNCHROGEST</div>
        </div>
        
        <hr class="sidebar-divider">
        
        @if(Auth::user()->profil == 'inscription')
            <div class="sidebar-heading">DASHBOARD</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.dashboard') ? 'active' : '' }}" href="{{ route('inscriptions.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tableau de bord</span>
                    </a>
            </div>
            
            <hr class="sidebar-divider">
            
            <div class="sidebar-heading">CONFIGURATION</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.parametres') ? 'active' : '' }}" href="{{ route('inscriptions.parametres') }}">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Paramètres</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.niveaux') ? 'active' : '' }}" href="{{ route('inscriptions.niveaux') }}">
                    <i class="fas fa-fw fa-school"></i>
                    <span>Niveaux & Classes</span>
                </a>
            </div>
            
            <hr class="sidebar-divider">
            
            <div class="sidebar-heading">GESTION ÉLÈVES</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.import') ? 'active' : '' }}" href="{{ route('inscriptions.import') }}">
                    <i class="fas fa-fw fa-file-upload"></i>
                    <span>Importation</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.eleves') ? 'active' : '' }}" href="{{ route('inscriptions.eleves') }}">
                    <i class="fas fa-fw fa-user-graduate"></i>
                    <span>Élèves</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.nouvelle') ? 'active' : '' }}" href="{{ route('inscriptions.nouvelle') }}">
                    <i class="fas fa-fw fa-user-plus"></i>
                    <span>Nouvelle inscription</span>
                </a>
            </div>
            
            <hr class="sidebar-divider">
            
            <div class="sidebar-heading">RAPPORTS</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.rapports') ? 'active' : '' }}" href="{{ route('inscriptions.rapports') }}">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Rapports</span>
                </a>
            </div>
        @endif
        
        @if(Auth::user()->profil == 'finance')
            <div class="sidebar-heading">DASHBOARD</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('finances.dashboard') ? 'active' : '' }}" href="{{ route('finances.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tableau de bord</span>
                </a>
            </div>
            
            <hr class="sidebar-divider">
            
            <div class="sidebar-heading">CONFIGURATION</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('finances.parametres') ? 'active' : '' }}" href="{{ route('finances.parametres') }}">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Paramètres</span>
                </a>
            </div>
        @endif
        
        @if(Auth::user()->profil == 'matiere')
            <div class="sidebar-heading">DASHBOARD</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('matieres.dashboard') ? 'active' : '' }}" href="{{ route('matieres.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tableau de bord</span>
                </a>
            </div>
            
            <hr class="sidebar-divider">
            
            <div class="sidebar-heading">CONFIGURATION</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('matieres.parametres') ? 'active' : '' }}" href="{{ route('matieres.parametres') }}">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Paramètres</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    @else
    <div class="container mt-4">
        @yield('content')
    </div>
    @endauth

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle
            const body = document.body;
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            
            // Check if sidebar state exists in localStorage
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                body.classList.add('sidebar-collapsed');
            }
            
            // Toggle sidebar
            sidebarToggleBtn.addEventListener('click', function() {
                body.classList.toggle('sidebar-collapsed');
                
                // Save state to localStorage
                localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapsed'));
            });
            
            // Auto-close alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const closeButton = alert.querySelector('.btn-close');
                    if (closeButton) {
                        closeButton.click();
                    }
                }, 5000);
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>