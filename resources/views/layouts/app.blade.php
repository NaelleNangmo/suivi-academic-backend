<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'GestAcad'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #3730a3;
            --accent: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,.08), 0 2px 4px -1px rgba(0,0,0,.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.08), 0 4px 6px -2px rgba(0,0,0,.04);
            --radius: 12px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Instrument Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        /* Navbar */
        .navbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary);
            text-decoration: none;
        }
        .navbar-brand .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1rem; font-weight: 700;
        }
        .navbar-nav {
            display: flex; align-items: center; gap: .5rem;
        }
        .nav-link {
            padding: .5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: .875rem;
            font-weight: 500;
            transition: all .15s;
        }
        .nav-link:hover { background: #f1f5f9; color: var(--text); }
        .nav-link.active { background: #ede9fe; color: var(--primary); }
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1.25rem;
            border-radius: 8px;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all .15s;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
        .btn-outline:hover { background: #ede9fe; }
        /* Main */
        main { padding: 2rem; max-width: 1280px; margin: 0 auto; }
        /* Footer */
        footer {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            font-size: .8rem;
            border-top: 1px solid var(--border);
            margin-top: 4rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar">
        <a href="{{ url('/') }}" class="navbar-brand">
            <div class="logo-icon">G</div>
            GestAcad
        </a>
        <div class="navbar-nav">
            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Accueil</a>
            <a href="{{ url('/filieres') }}" class="nav-link {{ request()->is('filieres*') ? 'active' : '' }}">Filières</a>
            <a href="{{ url('/niveaux') }}" class="nav-link {{ request()->is('niveaux*') ? 'active' : '' }}">Niveaux</a>
            <a href="{{ url('/ues') }}" class="nav-link {{ request()->is('ues*') ? 'active' : '' }}">UE</a>
            <a href="{{ url('/ecs') }}" class="nav-link {{ request()->is('ecs*') ? 'active' : '' }}">EC</a>
            <a href="{{ url('/personnels') }}" class="nav-link {{ request()->is('personnels*') ? 'active' : '' }}">Personnel</a>
            <a href="{{ url('/salles') }}" class="nav-link {{ request()->is('salles*') ? 'active' : '' }}">Salles</a>
            <a href="{{ url('/programmations') }}" class="nav-link {{ request()->is('programmations*') ? 'active' : '' }}">Programmation</a>
            <a href="{{ url('/enseignes') }}" class="nav-link {{ request()->is('enseignes*') ? 'active' : '' }}">Affectations</a>
            <a href="{{ url('/docs') }}" class="nav-link {{ request()->is('docs*') ? 'active' : '' }}">📄 API Docs</a>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} GestAcad — Système de gestion académique
    </footer>

    @stack('scripts')
</body>
</html>
