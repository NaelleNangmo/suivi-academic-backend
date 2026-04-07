@extends('layouts.app')
@section('title', 'Unités d\'enseignement — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
    <h1>📖 Unités d'enseignement</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle UE</button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Liste des UE</span>
        <button class="btn btn-secondary btn-sm" onclick="loadData()">↻ Actualiser</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Libellé</th><th>Niveau</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="5" class="empty"><div class="empty-icon">⏳</div>Chargement...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvelle UE</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form id="createForm">
        <div class="form-group"><label>Code UE *</label><input name="code_ue" required placeholder="Ex: UE-INFO-01"></div>
        <div class="form-group"><label>Libellé *</label><input name="label_ue" required placeholder="Ex: Algorithmique"></div>
        <div class="form-group"><label>Description *</label><textarea name="desc_ue" rows="2" required></textarea></div>
        <div class="form-group"><label>Niveau *</label>
          <select name="code_niveau" required>
            <option value="">-- Choisir --</option>
            @foreach($niveaux as $n)<option value="{{ $n->code_niveau }}">{{ $n->label_niveau }} ({{ $n->code_filiere }})</option>@endforeach
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
  <div class="modal">
    <div class="modal-head"><h2>Modifier l'UE</h2><button class="modal-close" onclick="closeModal('editModal')">×</button></div>
    <div class="modal-body">
      <form id="editForm">
        <div class="form-group"><label>Code</label><input id="e_code" disabled><p class="form-hint">Non modifiable</p></div>
        <div class="form-group"><label>Libellé *</label><input id="e_label" name="label_ue" required></div>
        <div class="form-group"><label>Description *</label><textarea id="e_desc" name="desc_ue" rows="2" required></textarea></div>
        <div class="form-group"><label>Niveau *</label>
          <select id="e_niveau" name="code_niveau" required>
            <option value="">-- Choisir --</option>
            @foreach($niveaux as $n)<option value="{{ $n->code_niveau }}">{{ $n->label_niveau }} ({{ $n->code_filiere }})</option>@endforeach
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

function row(u) {
    return `<tr>
        <td><span class="badge badge-green">${u.code_ue}</span></td>
        <td>${u.label_ue}</td>
        <td><span class="badge badge-blue">${u.code_niveau}</span></td>
        <td class="text-muted">${(u.desc_ue||'').substring(0,50)}${(u.desc_ue||'').length>50?'…':''}</td>
        <td><div class="actions">
            <button class="btn btn-primary btn-sm" onclick='editRow(${JSON.stringify(u)})'>Modifier</button>
            <button class="btn btn-danger btn-sm" onclick="deleteRow('${u.code_ue}')">Supprimer</button>
        </div></td>
    </tr>`;
}

async function loadData() {
    const {ok,data} = await apiCall('GET','/api/ues');
    const tb = document.getElementById('tbody');
    tb.innerHTML = ok && data.length ? data.map(row).join('') : `<tr><td colspan="5" class="empty"><div class="empty-icon">📖</div>${ok?'Aucune UE':'Erreur'}</td></tr>`;
}

document.getElementById('createForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    fd.code_niveau = parseInt(fd.code_niveau);
    const {ok,data} = await apiCall('POST','/api/ues',fd);
    if(ok){ toast('UE créée'); closeModal('createModal'); e.target.reset(); loadData(); }
    else toast(data.message||JSON.stringify(data.errors)||'Erreur','error');
});

function editRow(u) {
    currentCode = u.code_ue;
    document.getElementById('e_code').value   = u.code_ue;
    document.getElementById('e_label').value  = u.label_ue;
    document.getElementById('e_desc').value   = u.desc_ue||'';
    document.getElementById('e_niveau').value = u.code_niveau;
    openModal('editModal');
}

document.getElementById('editForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    fd.code_niveau = parseInt(fd.code_niveau);
    const {ok,data} = await apiCall('PUT',`/api/ues/${currentCode}`,fd);
    if(ok){ toast('UE mise à jour'); closeModal('editModal'); loadData(); }
    else toast(data.message||'Erreur','error');
});

async function deleteRow(code) {
    if(!confirmDelete(`Supprimer l'UE "${code}" ?`)) return;
    const {ok,data} = await apiCall('DELETE',`/api/ues/${code}`);
    if(ok){ toast('UE supprimée'); loadData(); }
    else toast(data.message||'Erreur','error');
}

loadData();
</script>
@endpush
@endsection
