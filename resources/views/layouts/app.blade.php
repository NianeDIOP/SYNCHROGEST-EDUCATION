<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SYNCHROGEST-EDUCATION')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 250px;
            padding-top: 60px;
            background-color: #4e73df;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 1;
            transition: all 0.3s;
        }
        
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
            padding-top: 80px;
            transition: all 0.3s;
        }
        
        .sidebar-toggled .sidebar {
            width: 100px;
        }
        
        .sidebar-toggled .content-wrapper {
            margin-left: 100px;
        }
        
        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: 60px;
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 2;
            padding: 0 30px;
        }
        
        .nav-item .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .nav-item .nav-link:hover, .nav-item .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 5px;
            margin: 0 10px;
        }
        
        .nav-item .nav-link .material-icons {
            margin-right: 10px;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 1rem 0;
        }
        
        .sidebar-heading {
            padding: 0 20px;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }
        
        .card {
            border: none;
            border-radius: 5px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    @auth
    <!-- Topbar -->
    <nav class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button id="sidebarToggle" class="btn btn-link text-dark">
                <i class="material-icons">menu</i>
            </button>
            <h1 class="h5 mb-0 ml-2">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="dropdown">
            <a class="btn dropdown-toggle d-flex align-items-center" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="mr-2">{{ Auth::user()->name }}</span>
                <i class="material-icons">account_circle</i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">Déconnexion</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center text-white mb-4">
            <h3 class="h5">SYNCHROGEST</h3>
            <p class="small mb-0">EDUCATION</p>
        </div>
        
        <hr class="sidebar-divider">
        
        @if(Auth::user()->profil == 'inscription')
            <div class="sidebar-heading">DASHBOARD</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.dashboard') ? 'active' : '' }}" href="{{ route('inscriptions.dashboard') }}">
                    <i class="material-icons">dashboard</i>
                    <span>Tableau de bord</span>
                </a>
            </div>
            
            <hr class="sidebar-divider">
            
            <div class="sidebar-heading">CONFIGURATION</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.parametres') ? 'active' : '' }}" href="{{ route('inscriptions.parametres') }}">
                    <i class="material-icons">settings</i>
                    <span>Paramètres</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.niveaux') ? 'active' : '' }}" href="{{ route('inscriptions.niveaux') }}">
                    <i class="material-icons">school</i>
                    <span>Niveaux & Classes</span>
                </a>
            </div>
            
            <hr class="sidebar-divider">
            
            <div class="sidebar-heading">GESTION ÉLÈVES</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.import') ? 'active' : '' }}" href="{{ route('inscriptions.import') }}">
                    <i class="material-icons">upload_file</i>
                    <span>Importation</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.eleves') ? 'active' : '' }}" href="{{ route('inscriptions.eleves') }}">
                    <i class="material-icons">people</i>
                    <span>Élèves</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.nouvelle') ? 'active' : '' }}" href="{{ route('inscriptions.nouvelle') }}">
                    <i class="material-icons">person_add</i>
                    <span>Nouvelle inscription</span>
                </a>
            </div>
            
            <hr class="sidebar-divider">
            
            <div class="sidebar-heading">RAPPORTS</div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('inscriptions.rapports') ? 'active' : '' }}" href="{{ route('inscriptions.rapports') }}">
                    <i class="material-icons">assessment</i>
                    <span>Rapports</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                document.body.classList.toggle('sidebar-toggled');
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>