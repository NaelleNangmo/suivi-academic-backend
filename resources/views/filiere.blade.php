<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des Filières - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --success: #10b981;
            --success-hover: #059669;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --warning: #f59e0b;
            --warning-hover: #d97706;
            --background: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .dark {
            --background: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --border: #334155;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color 0.2s, color 0.2s, border-color 0.2s;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            padding: 1.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            border: none;
            font-size: 0.875rem;
            transition: all 0.2s;
            box-shadow: var(--shadow);
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: var(--success-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: var(--danger-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background-color: var(--card-bg);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background-color: var(--border);
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-body {
            padding: 1.5rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-icon {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 0.5rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background-color: var(--card-bg);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .dark .table tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(20px);
            transition: transform 0.3s;
        }

        .modal-overlay.active .modal {
            transform: translateY(0);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
        }

        .modal-close:hover {
            background-color: var(--border);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            background-color: var(--card-bg);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-control:disabled {
            background-color: rgba(0, 0, 0, 0.05);
            color: var(--text-secondary);
            cursor: not-allowed;
        }

        .dark .form-control:disabled {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .form-text {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .form-error {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.75rem;
            color: var(--danger);
        }

        .actions-container {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .theme-toggle {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background-color: var(--card-bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow);
            z-index: 100;
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .actions-container {
                width: 100%;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .table th, .table td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>
<body class="dark">
    <div class="container">
        <div class="header">
            <h1 class="page-title">Gestion des Filières</h1>
            <div class="actions-container">
                <a href="{{ route('filieres.index') }}" class="btn btn-primary">
                    <span>↻</span> Actualiser
                </a>
                <button onclick="openCreateModal()" class="btn btn-success">
                    + Nouvelle Filière
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <span class="alert-icon">✓</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <span class="alert-icon">⚠</span>
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <span class="alert-icon">⚠</span>
                <div>
                    <strong>Erreurs de validation :</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Liste des filières</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Libellé</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($filieres as $filiere)
                                <tr>
                                    <td>{{ $filiere->code_filiere }}</td>
                                    <td>{{ $filiere->label_filiere }}</td>
                                    <td class="text-secondary">
                                        {{ Str::limit($filiere->desc_filiere ?? 'Aucune description', 50) }}
                                    </td>
                                    <td>
                                        <div class="actions-container">
                                            <button onclick="openEditModal({{ json_encode($filiere) }})"
                                                    class="btn btn-primary btn-sm">
                                                Modifier
                                            </button>
                                            <form action="{{ route('filieres.destroy', $filiere->code_filiere) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette filière ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty-state">
                                        <div class="empty-state-icon">📂</div>
                                        <p>Aucune filière trouvée. Créez-en une nouvelle !</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de création -->
    <div id="createModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Nouvelle Filière</h2>
                <button class="modal-close" onclick="closeCreateModal()">×</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('filieres.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Code Filière *</label>
                        <input type="text"
                               name="code_filiere"
                               required
                               minlength="5"
                               class="form-control"
                               value="{{ old('code_filiere') }}"
                               placeholder="Ex: INFO-01">
                        @error('code_filiere')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Libellé *</label>
                        <input type="text"
                               name="label_filiere"
                               required
                               minlength="5"
                               class="form-control"
                               value="{{ old('label_filiere') }}"
                               placeholder="Ex: Informatique">
                        @error('label_filiere')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="desc_filiere"
                                  rows="3"
                                  class="form-control"
                                  placeholder="Description de la filière...">{{ old('desc_filiere') }}</textarea>
                        @error('desc_filiere')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="actions-container" style="justify-content: flex-end; margin-top: 2rem;">
                        <button type="button"
                                onclick="closeCreateModal()"
                                class="btn btn-secondary">
                            Annuler
                        </button>
                        <button type="submit"
                                class="btn btn-success">
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'édition -->
    <div id="editModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Modifier la Filière</h2>
                <button class="modal-close" onclick="closeEditModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Code Filière</label>
                        <input type="text"
                               id="edit_code_filiere"
                               disabled
                               class="form-control">
                        <p class="form-text">Le code ne peut pas être modifié</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Libellé *</label>
                        <input type="text"
                               id="edit_label_filiere"
                               name="label_filiere"
                               required
                               minlength="5"
                               class="form-control"
                               placeholder="Ex: Informatique">
                        @error('label_filiere')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea id="edit_desc_filiere"
                                  name="desc_filiere"
                                  rows="3"
                                  class="form-control"
                                  placeholder="Description de la filière..."></textarea>
                        @error('desc_filiere')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="actions-container" style="justify-content: flex-end; margin-top: 2rem;">
                        <button type="button"
                                onclick="closeEditModal()"
                                class="btn btn-secondary">
                            Annuler
                        </button>
                        <button type="submit"
                                class="btn btn-primary">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bouton de basculement de thème -->
    <div class="theme-toggle" onclick="toggleTheme()">
        <span id="theme-icon">🌙</span>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(filiere) {
            document.getElementById('edit_code_filiere').value = filiere.code_filiere;
            document.getElementById('edit_label_filiere').value = filiere.label_filiere;
            document.getElementById('edit_desc_filiere').value = filiere.desc_filiere || '';
            document.getElementById('editForm').action = `/filieres/${filiere.code_filiere}`;
            document.getElementById('editModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Fermer les modals en cliquant en dehors
        window.onclick = function(event) {
            const createModal = document.getElementById('createModal');
            const editModal = document.getElementById('editModal');
            if (event.target == createModal) {
                closeCreateModal();
            }
            if (event.target == editModal) {
                closeEditModal();
            }
        }

        // Basculer entre les thèmes clair et sombre
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');

            if (body.classList.contains('dark')) {
                body.classList.remove('dark');
                themeIcon.textContent = '☀️';
            } else {
                body.classList.add('dark');
                themeIcon.textContent = '🌙';
            }
        }
    </script>
</body>
</html>
