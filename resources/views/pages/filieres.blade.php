@extends('layouts.app')
@section('title', 'Filières — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
    <h1>🏫 Filières</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle filière</button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Liste des filières</span>
        <button class="btn btn-secondary btn-sm" onclick="loadData()">↻ Actualiser</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Libellé</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody id="tbody">
                <tr><td colspan="4" class="empty"><div class="empty-icon">⏳</div>Chargement...</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Créer --}}
<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvelle filière</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form id="createForm">
        <div class="form-group"><label>Code *</label><input name="code_filiere" required placeholder="Ex: INFO-L"></div>
        <div class="form-group"><label>Libellé *</label><input name="label_filiere" required placeholder="Ex: Informatique"></div>
        <div class="form-group"><label>Description *</label><textarea name="desc_filiere" rows="3" required placeholder="Description..."></textarea></div>
        <div class="modal-foot">
            <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Annuler</button>
            <button type="submit" class="btn btn-success">Créer</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Éditer --}}
<div class="overlay" id="editModal">
  <div class="modal">
    <div class="modal-head"><h2>Modifier la filière</h2><button class="modal-close" onclick="closeModal('editModal')">×</button></div>
    <div class="modal-body">
      <form id="editForm">
        <div class="form-group"><label>Code</label><input id="e_code" disabled><p class="form-hint">Non modifiable</p></div>
        <div class="form-group"><label>Libellé *</label><input id="e_label" name="label_filiere" required></div>
        <div class="form-group"><label>Description *</label><textarea id="e_desc" name="desc_filiere" rows="3" required></textarea></div>
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

function row(f) {
    return `<tr>
        <td><span class="badge badge-purple">${f.code_filiere}</span></td>
        <td>${f.label_filiere}</td>
        <td class="text-muted">${(f.desc_filiere||'').substring(0,60)}${(f.desc_filiere||'').length>60?'…':''}</td>
        <td><div class="actions">
            <button class="btn btn-primary btn-sm" onclick='editRow(${JSON.stringify(f)})'>Modifier</button>
            <button class="btn btn-danger btn-sm" onclick="deleteRow('${f.code_filiere}')">Supprimer</button>
        </div></td>
    </tr>`;
}

async function loadData() {
    const {ok,data} = await apiCall('GET','/api/filieres');
    const tb = document.getElementById('tbody');
    if(!ok) { tb.innerHTML=`<tr><td colspan="4" class="empty">Erreur de chargement</td></tr>`; return; }
    tb.innerHTML = data.length ? data.map(row).join('') : `<tr><td colspan="4" class="empty"><div class="empty-icon">📂</div>Aucune filière</td></tr>`;
}

document.getElementById('createForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    const {ok,data} = await apiCall('POST','/api/filieres',fd);
    if(ok){ toast('Filière créée'); closeModal('createModal'); e.target.reset(); loadData(); }
    else toast(data.message||'Erreur','error');
});

function editRow(f) {
    currentCode = f.code_filiere;
    document.getElementById('e_code').value  = f.code_filiere;
    document.getElementById('e_label').value = f.label_filiere;
    document.getElementById('e_desc').value  = f.desc_filiere||'';
    openModal('editModal');
}

document.getElementById('editForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    const {ok,data} = await apiCall('PUT',`/api/filieres/${currentCode}`,fd);
    if(ok){ toast('Filière mise à jour'); closeModal('editModal'); loadData(); }
    else toast(data.message||'Erreur','error');
});

async function deleteRow(code) {
    if(!confirmDelete(`Supprimer la filière "${code}" ?`)) return;
    const {ok,data} = await apiCall('DELETE',`/api/filieres/${code}`);
    if(ok){ toast('Filière supprimée'); loadData(); }
    else toast(data.message||'Erreur','error');
}

loadData();
</script>
@endpush
@endsection
