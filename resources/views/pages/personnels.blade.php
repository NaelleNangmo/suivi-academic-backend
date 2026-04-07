@extends('layouts.app')
@section('title', 'Personnel — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
    <h1>👥 Personnel</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouveau membre</button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Liste du personnel</span>
        <button class="btn btn-secondary btn-sm" onclick="loadData()">↻ Actualiser</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Nom & Prénom</th><th>Sexe</th><th>Téléphone</th><th>Login</th><th>Type</th><th>Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="7" class="empty"><div class="empty-icon">⏳</div>Chargement...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal" style="max-width:580px">
    <div class="modal-head"><h2>Nouveau membre</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form id="createForm">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="form-group"><label>Code *</label><input name="code_pers" required placeholder="Ex: P001"></div>
            <div class="form-group"><label>Sexe *</label>
              <select name="sexe_pers" required><option value="M">Masculin</option><option value="F">Féminin</option></select>
            </div>
            <div class="form-group"><label>Nom *</label><input name="nom_pers" required></div>
            <div class="form-group"><label>Prénom</label><input name="prenom_pers"></div>
        </div>
        <div class="form-group"><label>Téléphone *</label><input name="phone_pers" required placeholder="Ex: 06XXXXXXXX"></div>
        <div class="form-group"><label>Email (login) *</label><input name="login_pers" type="email" required placeholder="prenom.nom@ecole.fr"></div>
        <div class="form-group"><label>Mot de passe *</label><input name="pwd_pers" type="password" required minlength="6"></div>
        <div class="form-group"><label>Type *</label>
          <select name="type_pers" required>
            <option value="ENSEIGNANT">Enseignant</option>
            <option value="RESPONSABLE ACADEMIQUE">Responsable académique</option>
            <option value="RESPONSABLE DISCIPLINE">Responsable discipline</option>
          </select>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Annuler</button>
            <button type="submit" class="btn btn-success">Créer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="overlay" id="editModal">
  <div class="modal" style="max-width:580px">
    <div class="modal-head"><h2>Modifier le membre</h2><button class="modal-close" onclick="closeModal('editModal')">×</button></div>
    <div class="modal-body">
      <form id="editForm">
        <div class="form-group"><label>Code</label><input id="e_code" disabled><p class="form-hint">Non modifiable</p></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="form-group"><label>Nom *</label><input id="e_nom" name="nom_pers" required></div>
            <div class="form-group"><label>Prénom</label><input id="e_prenom" name="prenom_pers"></div>
            <div class="form-group"><label>Sexe *</label>
              <select id="e_sexe" name="sexe_pers" required><option value="M">Masculin</option><option value="F">Féminin</option></select>
            </div>
            <div class="form-group"><label>Téléphone *</label><input id="e_phone" name="phone_pers" required></div>
        </div>
        <div class="form-group"><label>Email *</label><input id="e_login" name="login_pers" type="email" required></div>
        <div class="form-group"><label>Nouveau mot de passe <span class="form-hint" style="display:inline">(laisser vide = inchangé)</span></label><input name="pwd_pers" type="password" minlength="6"></div>
        <div class="form-group"><label>Type *</label>
          <select id="e_type" name="type_pers" required>
            <option value="ENSEIGNANT">Enseignant</option>
            <option value="RESPONSABLE ACADEMIQUE">Responsable académique</option>
            <option value="RESPONSABLE DISCIPLINE">Responsable discipline</option>
          </select>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Annuler</button>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
@include('partials.crud-scripts')
<script>
let currentCode = null;
const typeColors = {'ENSEIGNANT':'badge-blue','RESPONSABLE ACADEMIQUE':'badge-purple','RESPONSABLE DISCIPLINE':'badge-amber'};

function row(p) {
    const tc = typeColors[p.type_pers]||'badge-gray';
    return `<tr>
        <td><span class="badge badge-gray">${p.code_pers}</span></td>
        <td>${p.nom_pers} ${p.prenom_pers||''}</td>
        <td>${p.sexe_pers}</td>
        <td>${p.phone_pers}</td>
        <td class="text-muted">${p.login_pers}</td>
        <td><span class="badge ${tc}">${p.type_pers}</span></td>
        <td><div class="actions">
            <button class="btn btn-primary btn-sm" onclick='editRow(${JSON.stringify(p)})'>Modifier</button>
            <button class="btn btn-danger btn-sm" onclick="deleteRow('${p.code_pers}')">Supprimer</button>
        </div></td>
    </tr>`;
}

async function loadData() {
    const {ok,data} = await apiCall('GET','/api/personnels');
    const tb = document.getElementById('tbody');
    tb.innerHTML = ok && data.length ? data.map(row).join('') : `<tr><td colspan="7" class="empty"><div class="empty-icon">👥</div>${ok?'Aucun membre':'Erreur'}</td></tr>`;
}

document.getElementById('createForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    if(!fd.prenom_pers) delete fd.prenom_pers;
    const {ok,data} = await apiCall('POST','/api/personnels',fd);
    if(ok){ toast('Membre créé'); closeModal('createModal'); e.target.reset(); loadData(); }
    else toast(data.message||JSON.stringify(data.errors)||'Erreur','error');
});

function editRow(p) {
    currentCode = p.code_pers;
    document.getElementById('e_code').value   = p.code_pers;
    document.getElementById('e_nom').value    = p.nom_pers;
    document.getElementById('e_prenom').value = p.prenom_pers||'';
    document.getElementById('e_sexe').value   = p.sexe_pers;
    document.getElementById('e_phone').value  = p.phone_pers;
    document.getElementById('e_login').value  = p.login_pers;
    document.getElementById('e_type').value   = p.type_pers;
    openModal('editModal');
}

document.getElementById('editForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    if(!fd.pwd_pers) delete fd.pwd_pers;
    if(!fd.prenom_pers) delete fd.prenom_pers;
    const {ok,data} = await apiCall('PUT',`/api/personnels/${currentCode}`,fd);
    if(ok){ toast('Membre mis à jour'); closeModal('editModal'); loadData(); }
    else toast(data.message||'Erreur','error');
});

async function deleteRow(code) {
    if(!confirmDelete(`Supprimer le membre "${code}" ?`)) return;
    const {ok,data} = await apiCall('DELETE',`/api/personnels/${code}`);
    if(ok){ toast('Membre supprimé'); loadData(); }
    else toast(data.message||'Erreur','error');
}

loadData();
</script>
@endpush
@endsection
