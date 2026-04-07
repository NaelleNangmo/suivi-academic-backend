@extends('layouts.app')
@section('title', 'Documentation API — GestAcad')

@push('styles')
<style>
    .docs-layout { display:grid; grid-template-columns:260px 1fr; gap:2rem; align-items:start; }
    .sidebar { position:sticky; top:80px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.25rem; }
    .sidebar h3 { font-size:.75rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.75rem; margin-top:1.25rem; }
    .sidebar h3:first-child { margin-top:0; }
    .sidebar a { display:block; padding:.4rem .75rem; border-radius:6px; font-size:.85rem; color:#475569; text-decoration:none; transition:all .15s; }
    .sidebar a:hover { background:#f1f5f9; color:#1e293b; }
    .sidebar a.active { background:#ede9fe; color:#4f46e5; font-weight:600; }
    .endpoint-group { margin-bottom:2.5rem; }
    .group-title { font-size:1.2rem; font-weight:700; margin-bottom:1rem; display:flex; align-items:center; gap:.5rem; }
    .endpoint { background:#fff; border:1px solid #e2e8f0; border-radius:12px; margin-bottom:1rem; overflow:hidden; }
    .endpoint-head { display:flex; align-items:center; gap:.75rem; padding:1rem 1.25rem; cursor:pointer; user-select:none; }
    .endpoint-head:hover { background:#fafafa; }
    .method { padding:.25rem .65rem; border-radius:6px; font-size:.72rem; font-weight:700; letter-spacing:.05em; min-width:60px; text-align:center; }
    .GET    { background:#d1fae5; color:#065f46; }
    .POST   { background:#dbeafe; color:#1e40af; }
    .PUT    { background:#fef3c7; color:#92400e; }
    .DELETE { background:#fee2e2; color:#991b1b; }
    .endpoint-path { font-family:monospace; font-size:.9rem; color:#1e293b; font-weight:500; }
    .endpoint-summary { margin-left:auto; font-size:.8rem; color:#94a3b8; }
    .endpoint-body { border-top:1px solid #e2e8f0; padding:1.25rem; display:none; }
    .endpoint-body.open { display:block; }
    .endpoint-desc { color:#475569; font-size:.875rem; margin-bottom:1rem; line-height:1.6; }
    .section-label { font-size:.75rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.5rem; margin-top:1rem; }
    .section-label:first-child { margin-top:0; }
    .params-table { width:100%; border-collapse:collapse; font-size:.82rem; }
    .params-table th { background:#f8fafc; padding:.5rem .75rem; text-align:left; font-weight:600; color:#64748b; border-bottom:1px solid #e2e8f0; }
    .params-table td { padding:.5rem .75rem; border-bottom:1px solid #f1f5f9; vertical-align:top; }
    .params-table tr:last-child td { border-bottom:none; }
    .required-badge { background:#fee2e2; color:#991b1b; font-size:.65rem; font-weight:700; padding:.1rem .4rem; border-radius:4px; }
    .optional-badge { background:#f1f5f9; color:#64748b; font-size:.65rem; font-weight:700; padding:.1rem .4rem; border-radius:4px; }
    .code-block { background:#1e293b; color:#e2e8f0; border-radius:8px; padding:1rem; font-family:monospace; font-size:.8rem; overflow-x:auto; line-height:1.6; }
    .code-block .key   { color:#93c5fd; }
    .code-block .str   { color:#86efac; }
    .code-block .num   { color:#fcd34d; }
    .code-block .bool  { color:#f9a8d4; }
    .try-btn { margin-top:1rem; }
    .auth-note { background:#fef3c7; border:1px solid #fde68a; border-radius:8px; padding:.85rem 1rem; font-size:.85rem; color:#92400e; margin-bottom:1.5rem; display:flex; gap:.5rem; align-items:flex-start; }
    .intro-card { background:linear-gradient(135deg,#4f46e5,#06b6d4); color:#fff; border-radius:12px; padding:2rem; margin-bottom:2rem; }
    .intro-card h2 { font-size:1.5rem; font-weight:700; margin-bottom:.5rem; }
    .intro-card p { opacity:.9; line-height:1.6; }
    .base-url { background:rgba(255,255,255,.15); border-radius:8px; padding:.6rem 1rem; font-family:monospace; margin-top:1rem; font-size:.9rem; }
    @media(max-width:768px) { .docs-layout { grid-template-columns:1fr; } .sidebar { position:static; } }
</style>
@endpush

@section('content')
<div class="docs-layout">

  {{-- Sidebar --}}
  <aside class="sidebar">
    <h3>Général</h3>
    <a href="#intro" class="active">Introduction</a>
    <a href="#auth">Authentification</a>
    <h3>Ressources</h3>
    <a href="#filieres">Filières</a>
    <a href="#niveaux">Niveaux</a>
    <a href="#ues">UE</a>
    <a href="#ecs">EC</a>
    <a href="#personnels">Personnel</a>
    <a href="#salles">Salles</a>
    <a href="#enseignes">Affectations</a>
    <a href="#programmations">Programmation</a>
    <a href="#logs">Logs</a>
  </aside>

  {{-- Content --}}
  <div>

    {{-- Intro --}}
    <div class="intro-card" id="intro">
        <h2>📄 Documentation API GestAcad</h2>
        <p>API REST complète pour la gestion académique. Toutes les routes protégées nécessitent un token Sanctum dans le header <code>Authorization: Bearer {token}</code>.</p>
        <div class="base-url">Base URL : {{ url('/api') }}</div>
    </div>

    {{-- Auth --}}
    <div class="endpoint-group" id="auth">
        <div class="group-title">🔐 Authentification</div>

        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method POST">POST</span>
                <span class="endpoint-path">/api/login</span>
                <span class="endpoint-summary">Connexion et obtention du token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">Authentifie un membre du personnel et retourne un token Sanctum valable 1 heure.</p>
                <div class="section-label">Body (JSON)</div>
                <table class="params-table"><thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead><tbody>
                    <tr><td>login_pers</td><td>string</td><td><span class="required-badge">requis</span></td><td>Email du personnel</td></tr>
                    <tr><td>pwd_pers</td><td>string</td><td><span class="required-badge">requis</span></td><td>Mot de passe</td></tr>
                </tbody></table>
                <div class="section-label">Réponse 200</div>
                <div class="code-block"><span class="key">"token"</span>: <span class="str">"1|abc123..."</span>,<br><span class="key">"personnel"</span>: { <span class="key">"code_pers"</span>: <span class="str">"P001"</span>, ... }</div>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method POST">POST</span>
                <span class="endpoint-path">/api/logout</span>
                <span class="endpoint-summary">Révocation du token</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">Révoque le token actuel. Nécessite d'être authentifié.</p>
                <div class="auth-note">🔒 Requiert <code>Authorization: Bearer {token}</code></div>
                <div class="section-label">Réponse 200</div>
                <div class="code-block"><span class="key">"message"</span>: <span class="str">"Déconnexion réussie"</span></div>
            </div>
        </div>

        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method GET">GET</span>
                <span class="endpoint-path">/api/me</span>
                <span class="endpoint-summary">Utilisateur connecté</span>
            </div>
            <div class="endpoint-body">
                <div class="auth-note">🔒 Requiert <code>Authorization: Bearer {token}</code></div>
                <div class="section-label">Réponse 200</div>
                <div class="code-block">{ <span class="key">"code_pers"</span>: <span class="str">"P001"</span>, <span class="key">"nom_pers"</span>: <span class="str">"Dupont"</span>, ... }</div>
            </div>
        </div>
    </div>

    @php
    $resources = [
        ['id'=>'filieres','icon'=>'🏫','title'=>'Filières','base'=>'/api/filieres','key'=>'code_filiere','fields'=>[
            ['code_filiere','string','requis','Code unique (max 20 car.)'],
            ['label_filiere','string','requis','Libellé (max 256 car.)'],
            ['desc_filiere','string','requis','Description'],
        ],'update_fields'=>[
            ['label_filiere','string','optionnel','Libellé'],
            ['desc_filiere','string','optionnel','Description'],
        ],'example'=>'"code_filiere":"INFO","label_filiere":"Informatique","desc_filiere":"Filière info"'],
        ['id'=>'niveaux','icon'=>'📚','title'=>'Niveaux','base'=>'/api/niveaux','key'=>'code_niveau','fields'=>[
            ['label_niveau','string','requis','Libellé (max 256 car.)'],
            ['desc_niveau','string','requis','Description'],
            ['code_filiere','string','requis','Code filière existant'],
        ],'update_fields'=>[
            ['label_niveau','string','optionnel','Libellé'],
            ['desc_niveau','string','optionnel','Description'],
            ['code_filiere','string','optionnel','Code filière'],
        ],'example'=>'"label_niveau":"Licence 1","desc_niveau":"Première année","code_filiere":"INFO"'],
        ['id'=>'ues','icon'=>'📖','title'=>'Unités d\'enseignement','base'=>'/api/ues','key'=>'code_ue','fields'=>[
            ['code_ue','string','requis','Code unique (max 20 car.)'],
            ['label_ue','string','requis','Libellé'],
            ['desc_ue','string','requis','Description'],
            ['code_niveau','integer','requis','ID niveau existant'],
        ],'update_fields'=>[
            ['label_ue','string','optionnel','Libellé'],
            ['desc_ue','string','optionnel','Description'],
            ['code_niveau','integer','optionnel','ID niveau'],
        ],'example'=>'"code_ue":"UE-ALGO","label_ue":"Algorithmique","desc_ue":"...","code_niveau":1'],
        ['id'=>'salles','icon'=>'🏛️','title'=>'Salles','base'=>'/api/salles','key'=>'num_salle','fields'=>[
            ['num_salle','string','requis','Numéro unique'],
            ['contenance','integer','requis','Capacité (min 1)'],
            ['statut','string','requis','DISPONIBLE ou NON DISPONIBLE'],
        ],'update_fields'=>[
            ['contenance','integer','optionnel','Capacité'],
            ['statut','string','optionnel','DISPONIBLE ou NON DISPONIBLE'],
        ],'example'=>'"num_salle":"A101","contenance":50,"statut":"DISPONIBLE"'],
        ['id'=>'personnels','icon'=>'👥','title'=>'Personnel','base'=>'/api/personnels','key'=>'code_pers','fields'=>[
            ['code_pers','string','requis','Code unique'],
            ['nom_pers','string','requis','Nom'],
            ['prenom_pers','string','optionnel','Prénom'],
            ['sexe_pers','string','requis','M ou F'],
            ['phone_pers','string','requis','Téléphone'],
            ['login_pers','email','requis','Email (unique)'],
            ['pwd_pers','string','requis','Mot de passe (min 6 car.)'],
            ['type_pers','string','requis','ENSEIGNANT | RESPONSABLE ACADEMIQUE | RESPONSABLE DISCIPLINE'],
        ],'update_fields'=>[
            ['nom_pers','string','optionnel','Nom'],
            ['prenom_pers','string','optionnel','Prénom'],
            ['sexe_pers','string','optionnel','M ou F'],
            ['phone_pers','string','optionnel','Téléphone'],
            ['login_pers','email','optionnel','Email'],
            ['pwd_pers','string','optionnel','Nouveau mot de passe'],
            ['type_pers','string','optionnel','Type'],
        ],'example'=>'"code_pers":"P001","nom_pers":"Dupont","sexe_pers":"M","phone_pers":"0600000000","login_pers":"p@e.fr","pwd_pers":"secret","type_pers":"ENSEIGNANT"'],
    ];
    @endphp

    @foreach($resources as $r)
    <div class="endpoint-group" id="{{ $r['id'] }}">
        <div class="group-title">{{ $r['icon'] }} {{ $r['title'] }}</div>
        <div class="auth-note">🔒 Toutes ces routes nécessitent <code>Authorization: Bearer {token}</code></div>

        {{-- GET all --}}
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method GET">GET</span>
                <span class="endpoint-path">{{ $r['base'] }}</span>
                <span class="endpoint-summary">Lister tous</span>
            </div>
            <div class="endpoint-body">
                <p class="endpoint-desc">Retourne la liste complète (mise en cache 1h).</p>
                <div class="section-label">Réponse 200</div>
                <div class="code-block">[ { {!! $r['example'] !!} }, ... ]</div>
            </div>
        </div>

        {{-- POST --}}
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method POST">POST</span>
                <span class="endpoint-path">{{ $r['base'] }}</span>
                <span class="endpoint-summary">Créer</span>
            </div>
            <div class="endpoint-body">
                <div class="section-label">Body (JSON)</div>
                <table class="params-table"><thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead><tbody>
                    @foreach($r['fields'] as $f)
                    <tr><td><code>{{ $f[0] }}</code></td><td>{{ $f[1] }}</td><td><span class="{{ $f[2]==='requis'?'required-badge':'optional-badge' }}">{{ $f[2] }}</span></td><td>{{ $f[3] }}</td></tr>
                    @endforeach
                </tbody></table>
                <div class="section-label">Réponse 201</div>
                <div class="code-block">{ {!! $r['example'] !!} }</div>
            </div>
        </div>

        {{-- GET one --}}
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method GET">GET</span>
                <span class="endpoint-path">{{ $r['base'] }}/{{'{'}}{{ $r['key'] }}{{'}'}}</span>
                <span class="endpoint-summary">Détail</span>
            </div>
            <div class="endpoint-body">
                <div class="section-label">Paramètre URL</div>
                <table class="params-table"><thead><tr><th>Param</th><th>Description</th></tr></thead><tbody>
                    <tr><td><code>{{ $r['key'] }}</code></td><td>Identifiant de la ressource</td></tr>
                </tbody></table>
                <div class="section-label">Réponse 200</div>
                <div class="code-block">{ {!! $r['example'] !!} }</div>
            </div>
        </div>

        {{-- PUT --}}
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method PUT">PUT</span>
                <span class="endpoint-path">{{ $r['base'] }}/{{'{'}}{{ $r['key'] }}{{'}'}}</span>
                <span class="endpoint-summary">Mettre à jour</span>
            </div>
            <div class="endpoint-body">
                <div class="section-label">Body (JSON) — champs partiels acceptés</div>
                <table class="params-table"><thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead><tbody>
                    @foreach($r['update_fields'] as $f)
                    <tr><td><code>{{ $f[0] }}</code></td><td>{{ $f[1] }}</td><td><span class="{{ $f[2]==='requis'?'required-badge':'optional-badge' }}">{{ $f[2] }}</span></td><td>{{ $f[3] }}</td></tr>
                    @endforeach
                </tbody></table>
            </div>
        </div>

        {{-- DELETE --}}
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method DELETE">DELETE</span>
                <span class="endpoint-path">{{ $r['base'] }}/{{'{'}}{{ $r['key'] }}{{'}'}}</span>
                <span class="endpoint-summary">Supprimer</span>
            </div>
            <div class="endpoint-body">
                <div class="section-label">Réponse 200</div>
                <div class="code-block"><span class="key">"message"</span>: <span class="str">"Supprimé avec succès"</span></div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- EC spécial --}}
    <div class="endpoint-group" id="ecs">
        <div class="group-title">📝 Éléments constitutifs (EC)</div>
        <div class="auth-note">🔒 Toutes ces routes nécessitent <code>Authorization: Bearer {token}</code></div>
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method GET">GET</span>
                <span class="endpoint-path">/api/ecs/{code_ec}/support-cours</span>
                <span class="endpoint-summary">Télécharger le PDF</span>
            </div>
            <div class="endpoint-body"><p class="endpoint-desc">Télécharge le support de cours PDF associé à l'EC.</p></div>
        </div>
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method DELETE">DELETE</span>
                <span class="endpoint-path">/api/ecs/{code_ec}/support-cours</span>
                <span class="endpoint-summary">Supprimer le PDF</span>
            </div>
            <div class="endpoint-body"><p class="endpoint-desc">Supprime le fichier PDF du support de cours.</p></div>
        </div>
        <p style="font-size:.82rem;color:#94a3b8;margin-top:.5rem">Pour créer/modifier un EC avec un fichier, utiliser <code>multipart/form-data</code> avec le champ <code>support_cours</code> (PDF, max 10 Mo).</p>
    </div>

    {{-- Enseignes --}}
    <div class="endpoint-group" id="enseignes">
        <div class="group-title">🔗 Affectations (Enseigne)</div>
        <div class="auth-note">🔒 Toutes ces routes nécessitent <code>Authorization: Bearer {token}</code></div>
        @foreach([
            ['GET','/api/enseignes','Lister toutes les affectations'],
            ['POST','/api/enseignes','Créer une affectation (code_pers + code_ec)'],
            ['GET','/api/enseignes/{code_pers}/{code_ec}','Détail d\'une affectation'],
            ['DELETE','/api/enseignes/{code_pers}/{code_ec}','Supprimer une affectation'],
        ] as $ep)
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method {{ $ep[0] }}">{{ $ep[0] }}</span>
                <span class="endpoint-path">{{ $ep[1] }}</span>
                <span class="endpoint-summary">{{ $ep[2] }}</span>
            </div>
            <div class="endpoint-body">
                @if($ep[0]==='POST')
                <table class="params-table"><thead><tr><th>Champ</th><th>Type</th><th>Requis</th></tr></thead><tbody>
                    <tr><td><code>code_pers</code></td><td>string</td><td><span class="required-badge">requis</span></td></tr>
                    <tr><td><code>code_ec</code></td><td>string</td><td><span class="required-badge">requis</span></td></tr>
                </tbody></table>
                @else
                <p class="endpoint-desc">{{ $ep[2] }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Programmations --}}
    <div class="endpoint-group" id="programmations">
        <div class="group-title">📅 Programmation</div>
        <div class="auth-note">🔒 Toutes ces routes nécessitent <code>Authorization: Bearer {token}</code></div>
        @foreach([
            ['GET','/api/programmations','Lister toutes les séances'],
            ['POST','/api/programmations','Créer une séance'],
            ['GET','/api/programmations/{code_ec}/{num_salle}/{code_pers}','Détail'],
            ['PUT','/api/programmations/{code_ec}/{num_salle}/{code_pers}','Mettre à jour'],
            ['DELETE','/api/programmations/{code_ec}/{num_salle}/{code_pers}','Supprimer'],
        ] as $ep)
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method {{ $ep[0] }}">{{ $ep[0] }}</span>
                <span class="endpoint-path">{{ $ep[1] }}</span>
                <span class="endpoint-summary">{{ $ep[2] }}</span>
            </div>
            <div class="endpoint-body">
                @if($ep[0]==='POST')
                <table class="params-table"><thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead><tbody>
                    <tr><td><code>code_ec</code></td><td>string</td><td><span class="required-badge">requis</span></td><td>Code EC existant</td></tr>
                    <tr><td><code>num_salle</code></td><td>string</td><td><span class="required-badge">requis</span></td><td>Numéro salle existant</td></tr>
                    <tr><td><code>code_pers</code></td><td>string</td><td><span class="required-badge">requis</span></td><td>Code personnel existant</td></tr>
                    <tr><td><code>date</code></td><td>date</td><td><span class="required-badge">requis</span></td><td>Date du cours</td></tr>
                    <tr><td><code>date_debut</code></td><td>datetime</td><td><span class="required-badge">requis</span></td><td>Heure de début</td></tr>
                    <tr><td><code>date_fin</code></td><td>datetime</td><td><span class="required-badge">requis</span></td><td>Heure de fin (après début)</td></tr>
                    <tr><td><code>nbre_heure</code></td><td>integer</td><td><span class="required-badge">requis</span></td><td>Nombre d'heures</td></tr>
                    <tr><td><code>statut</code></td><td>string</td><td><span class="required-badge">requis</span></td><td>EN COURS | EN ATTENTE | ACHEVER</td></tr>
                </tbody></table>
                @else
                <p class="endpoint-desc">{{ $ep[2] }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Logs --}}
    <div class="endpoint-group" id="logs">
        <div class="group-title">📋 Logs de traçabilité</div>
        <div class="auth-note">🔒 Toutes ces routes nécessitent <code>Authorization: Bearer {token}</code></div>
        @foreach([
            ['GET','/api/logs','Tous les logs'],
            ['GET','/api/logs/{date}','Logs d\'une date (format YYYY-MM-DD)'],
            ['GET','/api/logs/{date}/stats','Statistiques des logs d\'une date'],
        ] as $ep)
        <div class="endpoint">
            <div class="endpoint-head" onclick="toggle(this)">
                <span class="method GET">GET</span>
                <span class="endpoint-path">{{ $ep[1] }}</span>
                <span class="endpoint-summary">{{ $ep[2] }}</span>
            </div>
            <div class="endpoint-body"><p class="endpoint-desc">{{ $ep[2] }}</p></div>
        </div>
        @endforeach
    </div>

  </div>{{-- end content --}}
</div>{{-- end layout --}}

@push('scripts')
<script>
function toggle(head) {
    const body = head.nextElementSibling;
    body.classList.toggle('open');
}
// Smooth scroll sidebar
document.querySelectorAll('.sidebar a').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.sidebar a').forEach(x => x.classList.remove('active'));
        a.classList.add('active');
        const target = document.querySelector(a.getAttribute('href'));
        if(target) target.scrollIntoView({behavior:'smooth', block:'start'});
    });
});
</script>
@endpush
@endsection
