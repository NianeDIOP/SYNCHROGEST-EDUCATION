<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SYNCHROGEST-EDUCATION')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 60px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            padding-top: 80px;
        }
        .navbar {
            position: fixed;
            width: 100%;
            z-index: 2;
        }
        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: #343a40;
            transition: all 0.3s;
        }
        .nav-link:hover {
            background-color: #f8f9fa;
        }
        .nav-link .material-icons {
            margin-right: 10px;
        }
        .active {
            background-color: #e9ecef;
            font-weight: bold;
        }
    </style>
    @yield('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SYNCHROGEST-EDUCATION</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <span class="material-icons">person</span>
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <span class="material-icons">logout</span> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar bg-white" style="width: 250px;">
        <div class="list-group list-group-flush">
        @if(Auth::user()->profil == 'inscription')
            <a href="{{ route('inscriptions.dashboard') }}" class="nav-link {{ request()->routeIs('inscriptions.dashboard') ? 'active' : '' }}">
                <span class="material-icons">dashboard</span> Tableau de bord
            </a>
            <a href="{{ route('inscriptions.parametres') }}" class="nav-link {{ request()->routeIs('inscriptions.parametres') ? 'active' : '' }}">
                <span class="material-icons">settings</span> Paramètres généraux
            </a>
            <a href="{{ route('inscriptions.niveaux') }}" class="nav-link {{ request()->routeIs('inscriptions.niveaux') ? 'active' : '' }}">
                <span class="material-icons">school</span> Niveaux et classes
            </a>
            <a href="{{ route('inscriptions.import') }}" class="nav-link {{ request()->routeIs('inscriptions.import') ? 'active' : '' }}">
                <span class="material-icons">upload</span> Importation
            </a>
            <a href="{{ route('inscriptions.eleves') }}" class="nav-link {{ request()->routeIs('inscriptions.eleves') ? 'active' : '' }}">
                <span class="material-icons">people</span> Élèves
            </a>
            <a href="{{ route('inscriptions.nouvelle') }}" class="nav-link {{ request()->routeIs('inscriptions.nouvelle') ? 'active' : '' }}">
                <span class="material-icons">add_circle</span> Nouvelle inscription
            </a>
            <a href="{{ route('inscriptions.rapports') }}" class="nav-link {{ request()->routeIs('inscriptions.rapports') ? 'active' : '' }}">
                <span class="material-icons">assessment</span> Rapports
            </a>
        @endif
        </div>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h1 class="mb-4">@yield('page-title')</h1>
            
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
    </div>
    @else
    <div class="container mt-4">
        @yield('content')
    </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')
</body>
</html>