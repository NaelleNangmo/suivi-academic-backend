@extends('layouts.app')

@section('title', 'Accueil — GestAcad')

@push('styles')
<style>
    /* Hero */
    .hero {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        border-radius: var(--radius);
        padding: 4rem 3rem;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 300px; height: 300px;
        background: rgba(255,255,255,.08);
        border-radius: 50%;
    }
    .hero::after {
        content: '';
        position: absolute;
        bottom: -80px; right: 120px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
    }
    .hero-content { position: relative; z-index: 1; }
    .hero-badge {
        display: inline-block;
        background: rgba(255,255,255,.2);
        padding: .3rem .9rem;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }
    .hero h1 { font-size: 2.5rem; font-weight: 700; line-height: 1.2; margin-bottom: 1rem; }
    .hero p { font-size: 1.1rem; opacity: .9; max-width: 480px; line-height: 1.6; margin-bottom: 2rem; }
    .hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; }
    .btn-white { background: white; color: var(--primary); font-weight: 600; }
    .btn-white:hover { background: #f1f5f9; }
    .btn-ghost { background: rgba(255,255,255,.15); color: white; border: 1.5px solid rgba(255,255,255,.4); }
    .btn-ghost:hover { background: rgba(255,255,255,.25); }
    .hero-visual {
        position: relative; z-index: 1;
        display: flex; flex-direction: column; gap: .75rem;
        min-width: 220px;
    }
    .hero-stat {
        background: rgba(255,255,255,.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 10px;
        padding: .9rem 1.25rem;
        display: flex; align-items: center; gap: .75rem;
    }
    .hero-stat-icon { font-size: 1.5rem; }
    .hero-stat-label { font-size: .75rem; opacity: .8; }
    .hero-stat-value { font-size: 1.1rem; font-weight: 700; }

    /* Stats grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.25rem;
        margin-bottom: 3rem;
    }
    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .stat-icon.indigo { background: #ede9fe; }
    .stat-icon.cyan   { background: #cffafe; }
    .stat-icon.green  { background: #d1fae5; }
    .stat-icon.amber  { background: #fef3c7; }
    .stat-icon.rose   { background: #ffe4e6; }
    .stat-icon.violet { background: #f3e8ff; }
    .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1; }
    .stat-label { font-size: .8rem; color: var(--text-muted); margin-top: .2rem; }

    /* Section title */
    .section-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.25rem;
    }
    .section-title { font-size: 1.2rem; font-weight: 700; }
    .section-link { font-size: .85rem; color: var(--primary); text-decoration: none; font-weight: 500; }
    .section-link:hover { text-decoration: underline; }

    /* Modules grid */
    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.25rem;
        margin-bottom: 3rem;
    }
    .module-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        box-shadow: var(--shadow);
        text-decoration: none;
        color: var(--text);
        display: flex;
        flex-direction: column;
        gap: .75rem;
        transition: box-shadow .2s, transform .2s, border-color .2s;
    }
    .module-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-3px);
        border-color: var(--primary-light);
    }
    .module-card-header { display: flex; align-items: center; gap: .75rem; }
    .module-icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
    }
    .module-card h3 { font-size: 1rem; font-weight: 600; }
    .module-card p { font-size: .85rem; color: var(--text-muted); line-height: 1.5; }
    .module-card-footer {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: auto;
        padding-top: .75rem;
        border-top: 1px solid var(--border);
        font-size: .8rem;
        color: var(--text-muted);
    }
    .module-arrow { color: var(--primary); font-size: 1rem; }

    /* Hierarchy section */
    .hierarchy {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 2rem;
        box-shadow: var(--shadow);
        margin-bottom: 3rem;
    }
    .hierarchy-flow {
        display: flex;
        align-items: center;
        gap: 0;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }
    .hierarchy-step {
        display: flex; flex-direction: column; align-items: center;
        text-align: center;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: 10px;
        min-width: 110px;
    }
    .hierarchy-step-icon { font-size: 1.8rem; margin-bottom: .4rem; }
    .hierarchy-step-label { font-size: .75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; }
    .hierarchy-step-name { font-size: .9rem; font-weight: 600; color: var(--text); margin-top: .2rem; }
    .hierarchy-arrow {
        font-size: 1.2rem;
        color: var(--text-muted);
        padding: 0 .5rem;
        flex-shrink: 0;
    }

    /* API section */
    .api-section {
        background: #1e293b;
        border-radius: var(--radius);
        padding: 2rem;
        color: #e2e8f0;
        margin-bottom: 3rem;
    }
    .api-section .section-title { color: white; }
    .api-section .section-link { color: #67e8f9; }
    .api-endpoints {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: .75rem;
        margin-top: 1.25rem;
    }
    .api-endpoint {
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 8px;
        padding: .75rem 1rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        font-size: .85rem;
    }
    .method-badge {
        padding: .2rem .55rem;
        border-radius: 5px;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        flex-shrink: 0;
    }
    .method-get    { background: #064e3b; color: #6ee7b7; }
    .method-post   { background: #1e3a5f; color: #93c5fd; }
    .method-put    { background: #451a03; color: #fcd34d; }
    .method-delete { background: #450a0a; color: #fca5a5; }
    .endpoint-path { color: #94a3b8; font-family: monospace; }

    @media (max-width: 768px) {
        .hero { flex-direction: column; padding: 2.5rem 1.5rem; }
        .hero h1 { font-size: 1.8rem; }
        .hero-visual { width: 100%; }
        .hierarchy-flow { gap: .5rem; }
        .hierarchy-arrow { display: none; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="hero">
    <div class="hero-content">
        <span class="hero-badge">🎓 Système académique</span>
        <h1>Gérez votre établissement<br>en toute simplicité</h1>
        <p>GestAcad centralise la gestion des filières, niveaux, unités d'enseignement, cours, personnel et programmations dans une seule plateforme.</p>
        <div class="hero-actions">
            <a href="{{ url('/filieres') }}" class="btn btn-white">Voir les filières</a>
            <a href="{{ url('/programmations') }}" class="btn btn-ghost">Programmation</a>
        </div>
    </div>
    <div class="hero-visual">
        <div class="hero-stat">
            <span class="hero-stat-icon">🏫</span>
            <div>
                <div class="hero-stat-label">Filières actives</div>
                <div class="hero-stat-value">{{ $stats['filieres'] }}</div>
            </div>
        </div>
        <div class="hero-stat">
            <span class="hero-stat-icon">👨‍🏫</span>
            <div>
                <div class="hero-stat-label">Enseignants</div>
                <div class="hero-stat-value">{{ $stats['personnels'] }}</div>
            </div>
        </div>
        <div class="hero-stat">
            <span class="hero-stat-icon">📅</span>
            <div>
                <div class="hero-stat-label">Cours programmés</div>
                <div class="hero-stat-value">{{ $stats['programmations'] }}</div>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon indigo">🏫</div>
        <div>
            <div class="stat-value">{{ $stats['filieres'] }}</div>
            <div class="stat-label">Filières</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan">📚</div>
        <div>
            <div class="stat-value">{{ $stats['niveaux'] }}</div>
            <div class="stat-label">Niveaux</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">📖</div>
        <div>
            <div class="stat-value">{{ $stats['ues'] }}</div>
            <div class="stat-label">Unités d'enseignement</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">📝</div>
        <div>
            <div class="stat-value">{{ $stats['ecs'] }}</div>
            <div class="stat-label">Éléments constitutifs</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon rose">👥</div>
        <div>
            <div class="stat-value">{{ $stats['personnels'] }}</div>
            <div class="stat-label">Personnel</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon violet">🏛️</div>
        <div>
            <div class="stat-value">{{ $stats['salles'] }}</div>
            <div class="stat-label">Salles</div>
        </div>
    </div>
</div>

{{-- Modules --}}
<div class="section-header">
    <h2 class="section-title">Modules de gestion</h2>
</div>
<div class="modules-grid">
    <a href="{{ url('/filieres') }}" class="module-card">
        <div class="module-card-header">
            <div class="module-icon" style="background:#ede9fe;">🏫</div>
            <h3>Filières</h3>
        </div>
        <p>Gérez les programmes d'études, leurs descriptions et leur organisation par niveaux.</p>
        <div class="module-card-footer">
            <span>{{ $stats['filieres'] }} filière(s)</span>
            <span class="module-arrow">→</span>
        </div>
    </a>
    <a href="{{ url('/niveaux') }}" class="module-card">
        <div class="module-card-header">
            <div class="module-icon" style="background:#cffafe;">📚</div>
            <h3>Niveaux</h3>
        </div>
        <p>Définissez les niveaux académiques rattachés à chaque filière (L1, L2, M1…).</p>
        <div class="module-card-footer">
            <span>{{ $stats['niveaux'] }} niveau(x)</span>
            <span class="module-arrow">→</span>
        </div>
    </a>
    <a href="{{ url('/ues') }}" class="module-card">
        <div class="module-card-header">
            <div class="module-icon" style="background:#d1fae5;">📖</div>
            <h3>Unités d'enseignement</h3>
        </div>
        <p>Organisez les UE par niveau et gérez leurs éléments constitutifs associés.</p>
        <div class="module-card-footer">
            <span>{{ $stats['ues'] }} UE</span>
            <span class="module-arrow">→</span>
        </div>
    </a>
    <a href="{{ url('/ecs') }}" class="module-card">
        <div class="module-card-header">
            <div class="module-icon" style="background:#fef3c7;">📝</div>
            <h3>Éléments constitutifs</h3>
        </div>
        <p>Gérez les EC, leurs heures, crédits et supports de cours téléchargeables.</p>
        <div class="module-card-footer">
            <span>{{ $stats['ecs'] }} EC</span>
            <span class="module-arrow">→</span>
        </div>
    </a>
    <a href="{{ url('/personnels') }}" class="module-card">
        <div class="module-card-header">
            <div class="module-icon" style="background:#ffe4e6;">👥</div>
            <h3>Personnel</h3>
        </div>
        <p>Administrez les enseignants et le personnel, leurs affectations et leurs accès.</p>
        <div class="module-card-footer">
            <span>{{ $stats['personnels'] }} membre(s)</span>
            <span class="module-arrow">→</span>
        </div>
    </a>
    <a href="{{ url('/salles') }}" class="module-card">
        <div class="module-card-header">
            <div class="module-icon" style="background:#f0fdf4;">🏛️</div>
            <h3>Salles</h3>
        </div>
        <p>Suivez la disponibilité et la capacité des salles pour les programmations.</p>
        <div class="module-card-footer">
            <span>{{ $stats['salles'] }} salle(s)</span>
            <span class="module-arrow">→</span>
        </div>
    </a>
    <a href="{{ url('/programmations') }}" class="module-card">
        <div class="module-card-header">
            <div class="module-icon" style="background:#f3e8ff;">📅</div>
            <h3>Programmation</h3>
        </div>
        <p>Planifiez les séances de cours en associant EC, salle et enseignant.</p>
        <div class="module-card-footer">
            <span>{{ $stats['programmations'] }} séance(s)</span>
            <span class="module-arrow">→</span>
        </div>
    </a>
    <a href="{{ url('/enseignes') }}" class="module-card">
        <div class="module-card-header">
            <div class="module-icon" style="background:#fef9c3;">🔗</div>
            <h3>Affectations</h3>
        </div>
        <p>Associez les enseignants aux éléments constitutifs qu'ils ont en charge.</p>
        <div class="module-card-footer">
            <span>{{ $stats['enseignes'] }} affectation(s)</span>
            <span class="module-arrow">→</span>
        </div>
    </a>
</div>

{{-- Hierarchy --}}
<div class="hierarchy">
    <div class="section-header">
        <h2 class="section-title">Hiérarchie académique</h2>
    </div>
    <div class="hierarchy-flow">
        <div class="hierarchy-step">
            <div class="hierarchy-step-icon">🏫</div>
            <div class="hierarchy-step-label">Niveau 1</div>
            <div class="hierarchy-step-name">Filière</div>
        </div>
        <div class="hierarchy-arrow">→</div>
        <div class="hierarchy-step">
            <div class="hierarchy-step-icon">📚</div>
            <div class="hierarchy-step-label">Niveau 2</div>
            <div class="hierarchy-step-name">Niveau</div>
        </div>
        <div class="hierarchy-arrow">→</div>
        <div class="hierarchy-step">
            <div class="hierarchy-step-icon">📖</div>
            <div class="hierarchy-step-label">Niveau 3</div>
            <div class="hierarchy-step-name">UE</div>
        </div>
        <div class="hierarchy-arrow">→</div>
        <div class="hierarchy-step">
            <div class="hierarchy-step-icon">📝</div>
            <div class="hierarchy-step-label">Niveau 4</div>
            <div class="hierarchy-step-name">EC</div>
        </div>
        <div class="hierarchy-arrow">→</div>
        <div class="hierarchy-step">
            <div class="hierarchy-step-icon">👨‍🏫</div>
            <div class="hierarchy-step-label">Affectation</div>
            <div class="hierarchy-step-name">Enseigne</div>
        </div>
        <div class="hierarchy-arrow">→</div>
        <div class="hierarchy-step">
            <div class="hierarchy-step-icon">📅</div>
            <div class="hierarchy-step-label">Planning</div>
            <div class="hierarchy-step-name">Programmation</div>
        </div>
    </div>
</div>

{{-- API endpoints --}}
<div class="api-section">
    <div class="section-header">
        <h2 class="section-title">API REST disponible</h2>
        <a href="{{ url('/api') }}" class="section-link">Documentation →</a>
    </div>
    <div class="api-endpoints">
        <div class="api-endpoint">
            <span class="method-badge method-post">POST</span>
            <span class="endpoint-path">/api/login</span>
        </div>
        <div class="api-endpoint">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/filieres</span>
        </div>
        <div class="api-endpoint">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/niveaux</span>
        </div>
        <div class="api-endpoint">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/ues</span>
        </div>
        <div class="api-endpoint">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/ecs</span>
        </div>
        <div class="api-endpoint">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/personnels</span>
        </div>
        <div class="api-endpoint">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/salles</span>
        </div>
        <div class="api-endpoint">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/programmations</span>
        </div>
        <div class="api-endpoint">
            <span class="method-badge method-post">POST</span>
            <span class="endpoint-path">/api/ecs/{code}/support-cours</span>
        </div>
        <div class="api-endpoint">
            <span class="method-badge method-get">GET</span>
            <span class="endpoint-path">/api/logs/{date}/stats</span>
        </div>
    </div>
</div>

@endsection
