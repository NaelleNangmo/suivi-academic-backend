@extends('layouts.app')
@section('title', 'Salles — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
    <h1>🏛️ Salles</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle salle</button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Liste des salles</span>
        <button class="btn btn-secondary btn-sm" onclick="loadData()">↻ Actualiser</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Numéro</th><th>Contenance</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="4" class="empty"><div class="empty-icon">⏳</div>Chargement...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvelle salle</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form id="createForm">
        <div class="form-group"><label>Numéro de salle *</label><input name="num_salle" required placeholder="Ex: A101"></div>
        <div class="form-group"><label>Contenance *</label><input name="contenance" type="number" min="1" required placeholder="Ex: 50"></div>
        <div class="form-group"><label>Statut *</label>
          <select name="statut" required>
            <option value="DISPONIBLE">Disponible</option>
            <option value="NON DISPONIBLE">Non disponible</option>
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
    <div class="modal-head"><h2>Modifier la salle</h2><button class="modal-close" onclick="closeModal('editModal')">×</button></div>
    <div class="modal-body">
      <form id="editForm">
        <div class="form-group"><label>Numéro</label><input id="e_num" disabled><p class="form-hint">Non modifiable</p></div>
        <div class="form-group"><label>Contenance *</label><input id="e_cont" name="contenance" type="number" min="1" required></div>
        <div class="form-group"><label>Statut *</label>
          <select id="e_statut" name="statut" required>
            <option value="DISPONIBLE">Disponible</option>
            <option value="NON DISPONIBLE">Non disponible</option>
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
let currentNum = null;

function row(s) {
    const badge = s.statut === 'DISPONIBLE' ? 'badge-green' : 'badge-red';
    return `<tr>
        <td><span class="badge badge-blue">${s.num_salle}</span></td>
        <td>${s.contenance} places</td>
        <td><span class="badge ${badge}">${s.statut}</span></td>
        <td><div class="actions">
            <button class="btn btn-primary btn-sm" onclick='editRow(${JSON.stringify(s)})'>Modifier</button>
            <button class="btn btn-danger btn-sm" onclick="deleteRow('${s.num_salle}')">Supprimer</button>
        </div></td>
    </tr>`;
}

async function loadData() {
    const {ok,data} = await apiCall('GET','/api/salles');
    const tb = document.getElementById('tbody');
    tb.innerHTML = ok && data.length ? data.map(row).join('') : `<tr><td colspan="4" class="empty"><div class="empty-icon">🏛️</div>${ok?'Aucune salle':'Erreur'}</td></tr>`;
}

document.getElementById('createForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    fd.contenance = parseInt(fd.contenance);
    const {ok,data} = await apiCall('POST','/api/salles',fd);
    if(ok){ toast('Salle créée'); closeModal('createModal'); e.target.reset(); loadData(); }
    else toast(data.message||JSON.stringify(data.errors)||'Erreur','error');
});

function editRow(s) {
    currentNum = s.num_salle;
    document.getElementById('e_num').value    = s.num_salle;
    document.getElementById('e_cont').value   = s.contenance;
    document.getElementById('e_statut').value = s.statut;
    openModal('editModal');
}

document.getElementById('editForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    fd.contenance = parseInt(fd.contenance);
    const {ok,data} = await apiCall('PUT',`/api/salles/${currentNum}`,fd);
    if(ok){ toast('Salle mise à jour'); closeModal('editModal'); loadData(); }
    else toast(data.message||'Erreur','error');
});

async function deleteRow(num) {
    if(!confirmDelete(`Supprimer la salle "${num}" ?`)) return;
    const {ok,data} = await apiCall('DELETE',`/api/salles/${num}`);
    if(ok){ toast('Salle supprimée'); loadData(); }
    else toast(data.message||'Erreur','error');
}

loadData();
</script>
@endpush
@endsection
