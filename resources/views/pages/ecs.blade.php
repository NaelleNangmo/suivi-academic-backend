@extends('layouts.app')
@section('title', 'Éléments constitutifs — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
    <h1>📝 Éléments constitutifs</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvel EC</button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Liste des EC</span>
        <button class="btn btn-secondary btn-sm" onclick="loadData()">↻ Actualiser</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Libellé</th><th>UE</th><th>Heures</th><th>Crédits</th><th>Support</th><th>Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="7" class="empty"><div class="empty-icon">⏳</div>Chargement...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvel EC</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form id="createForm" enctype="multipart/form-data">
        <div class="form-group"><label>Code EC *</label><input name="code_ec" required placeholder="Ex: EC-ALGO-01"></div>
        <div class="form-group"><label>Libellé *</label><input name="label_ec" required placeholder="Ex: Algorithmique avancée"></div>
        <div class="form-group"><label>Description *</label><textarea name="desc_ec" rows="2" required></textarea></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="form-group"><label>Heures *</label><input name="nbh_ec" type="number" min="1" required></div>
            <div class="form-group"><label>Crédits *</label><input name="nbc_ec" type="number" min="1" required></div>
        </div>
        <div class="form-group"><label>UE *</label>
          <select name="code_ue" required>
            <option value="">-- Choisir --</option>
            @foreach($ues as $u)<option value="{{ $u->code_ue }}">{{ $u->label_ue }}</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label>Support de cours (PDF, max 10 Mo)</label><input type="file" name="support_cours" accept=".pdf" id="c_file"></div>
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
    <div class="modal-head"><h2>Modifier l'EC</h2><button class="modal-close" onclick="closeModal('editModal')">×</button></div>
    <div class="modal-body">
      <form id="editForm">
        <div class="form-group"><label>Code</label><input id="e_code" disabled><p class="form-hint">Non modifiable</p></div>
        <div class="form-group"><label>Libellé *</label><input id="e_label" name="label_ec" required></div>
        <div class="form-group"><label>Description *</label><textarea id="e_desc" name="desc_ec" rows="2" required></textarea></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="form-group"><label>Heures *</label><input id="e_nbh" name="nbh_ec" type="number" min="1" required></div>
            <div class="form-group"><label>Crédits *</label><input id="e_nbc" name="nbc_ec" type="number" min="1" required></div>
        </div>
        <div class="form-group"><label>UE *</label>
          <select id="e_ue" name="code_ue" required>
            <option value="">-- Choisir --</option>
            @foreach($ues as $u)<option value="{{ $u->code_ue }}">{{ $u->label_ue }}</option>@endforeach
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

function row(ec) {
    const support = ec.support_cours
        ? `<a href="/api/ecs/${ec.code_ec}/support-cours" class="btn btn-warning btn-sm" target="_blank">📄 PDF</a>`
        : `<span class="text-muted">—</span>`;
    return `<tr>
        <td><span class="badge badge-amber">${ec.code_ec}</span></td>
        <td>${ec.label_ec}</td>
        <td><span class="badge badge-green">${ec.code_ue}</span></td>
        <td>${ec.nbh_ec}h</td>
        <td>${ec.nbc_ec} cr.</td>
        <td>${support}</td>
        <td><div class="actions">
            <button class="btn btn-primary btn-sm" onclick='editRow(${JSON.stringify(ec)})'>Modifier</button>
            <button class="btn btn-danger btn-sm" onclick="deleteRow('${ec.code_ec}')">Supprimer</button>
        </div></td>
    </tr>`;
}

async function loadData() {
    const {ok,data} = await apiCall('GET','/api/ecs');
    const tb = document.getElementById('tbody');
    tb.innerHTML = ok && data.length ? data.map(row).join('') : `<tr><td colspan="7" class="empty"><div class="empty-icon">📝</div>${ok?'Aucun EC':'Erreur'}</td></tr>`;
}

// Création avec FormData (fichier)
document.getElementById('createForm').addEventListener('submit', async e => {
    e.preventDefault();
    const form = e.target;
    const fd = new FormData(form);
    const res = await fetch('/api/ecs', {
        method:'POST',
        headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF},
        body: fd
    });
    const data = await res.json().catch(()=>({}));
    if(res.ok){ toast('EC créé'); closeModal('createModal'); form.reset(); loadData(); }
    else toast(data.message||JSON.stringify(data.errors)||'Erreur','error');
});

function editRow(ec) {
    currentCode = ec.code_ec;
    document.getElementById('e_code').value  = ec.code_ec;
    document.getElementById('e_label').value = ec.label_ec;
    document.getElementById('e_desc').value  = ec.desc_ec||'';
    document.getElementById('e_nbh').value   = ec.nbh_ec;
    document.getElementById('e_nbc').value   = ec.nbc_ec;
    document.getElementById('e_ue').value    = ec.code_ue;
    openModal('editModal');
}

document.getElementById('editForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    fd.nbh_ec = parseInt(fd.nbh_ec); fd.nbc_ec = parseInt(fd.nbc_ec);
    const {ok,data} = await apiCall('PUT',`/api/ecs/${currentCode}`,fd);
    if(ok){ toast('EC mis à jour'); closeModal('editModal'); loadData(); }
    else toast(data.message||'Erreur','error');
});

async function deleteRow(code) {
    if(!confirmDelete(`Supprimer l'EC "${code}" ?`)) return;
    const {ok,data} = await apiCall('DELETE',`/api/ecs/${code}`);
    if(ok){ toast('EC supprimé'); loadData(); }
    else toast(data.message||'Erreur','error');
}

loadData();
</script>
@endpush
@endsection
