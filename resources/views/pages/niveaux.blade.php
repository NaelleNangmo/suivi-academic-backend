@extends('layouts.app')
@section('title', 'Niveaux — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
    <h1>📚 Niveaux</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouveau niveau</button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Liste des niveaux</span>
        <button class="btn btn-secondary btn-sm" onclick="loadData()">↻ Actualiser</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Libellé</th><th>Filière</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="5" class="empty"><div class="empty-icon">⏳</div>Chargement...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouveau niveau</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form id="createForm">
        <div class="form-group"><label>Libellé *</label><input name="label_niveau" required placeholder="Ex: Licence 1"></div>
        <div class="form-group"><label>Description *</label><textarea name="desc_niveau" rows="2" required placeholder="Description..."></textarea></div>
        <div class="form-group"><label>Filière *</label>
          <select name="code_filiere" required>
            <option value="">-- Choisir --</option>
            @foreach($filieres as $f)<option value="{{ $f->code_filiere }}">{{ $f->label_filiere }}</option>@endforeach
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
    <div class="modal-head"><h2>Modifier le niveau</h2><button class="modal-close" onclick="closeModal('editModal')">×</button></div>
    <div class="modal-body">
      <form id="editForm">
        <div class="form-group"><label>Libellé *</label><input id="e_label" name="label_niveau" required></div>
        <div class="form-group"><label>Description *</label><textarea id="e_desc" name="desc_niveau" rows="2" required></textarea></div>
        <div class="form-group"><label>Filière *</label>
          <select id="e_filiere" name="code_filiere" required>
            <option value="">-- Choisir --</option>
            @foreach($filieres as $f)<option value="{{ $f->code_filiere }}">{{ $f->label_filiere }}</option>@endforeach
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
let currentId = null;

function row(n) {
    return `<tr>
        <td><span class="badge badge-blue">${n.code_niveau}</span></td>
        <td>${n.label_niveau}</td>
        <td><span class="badge badge-purple">${n.code_filiere}</span></td>
        <td class="text-muted">${(n.desc_niveau||'').substring(0,50)}${(n.desc_niveau||'').length>50?'…':''}</td>
        <td><div class="actions">
            <button class="btn btn-primary btn-sm" onclick='editRow(${JSON.stringify(n)})'>Modifier</button>
            <button class="btn btn-danger btn-sm" onclick="deleteRow(${n.code_niveau})">Supprimer</button>
        </div></td>
    </tr>`;
}

async function loadData() {
    const {ok,data} = await apiCall('GET','/api/niveaux');
    const tb = document.getElementById('tbody');
    tb.innerHTML = ok && data.length ? data.map(row).join('') : `<tr><td colspan="5" class="empty"><div class="empty-icon">📚</div>${ok?'Aucun niveau':'Erreur de chargement'}</td></tr>`;
}

document.getElementById('createForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    const {ok,data} = await apiCall('POST','/api/niveaux',fd);
    if(ok){ toast('Niveau créé'); closeModal('createModal'); e.target.reset(); loadData(); }
    else toast(data.message||JSON.stringify(data.errors)||'Erreur','error');
});

function editRow(n) {
    currentId = n.code_niveau;
    document.getElementById('e_label').value   = n.label_niveau;
    document.getElementById('e_desc').value    = n.desc_niveau||'';
    document.getElementById('e_filiere').value = n.code_filiere;
    openModal('editModal');
}

document.getElementById('editForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    const {ok,data} = await apiCall('PUT',`/api/niveaux/${currentId}`,fd);
    if(ok){ toast('Niveau mis à jour'); closeModal('editModal'); loadData(); }
    else toast(data.message||'Erreur','error');
});

async function deleteRow(id) {
    if(!confirmDelete(`Supprimer le niveau #${id} ?`)) return;
    const {ok,data} = await apiCall('DELETE',`/api/niveaux/${id}`);
    if(ok){ toast('Niveau supprimé'); loadData(); }
    else toast(data.message||'Erreur','error');
}

loadData();
</script>
@endpush
@endsection
