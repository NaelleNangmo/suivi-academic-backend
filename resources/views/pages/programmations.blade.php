@extends('layouts.app')
@section('title', 'Programmation — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
    <h1>📅 Programmation des cours</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle séance</button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Liste des séances</span>
        <button class="btn btn-secondary btn-sm" onclick="loadData()">↻ Actualiser</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>EC</th><th>Salle</th><th>Personnel</th><th>Date</th><th>Début</th><th>Fin</th><th>Heures</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="9" class="empty"><div class="empty-icon">⏳</div>Chargement...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal" style="max-width:600px">
    <div class="modal-head"><h2>Nouvelle séance</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form id="createForm">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <div class="form-group"><label>EC *</label>
            <select name="code_ec" required>
              <option value="">-- Choisir --</option>
              @foreach($ecs as $ec)<option value="{{ $ec->code_ec }}">{{ $ec->label_ec }}</option>@endforeach
            </select>
          </div>
          <div class="form-group"><label>Salle *</label>
            <select name="num_salle" required>
              <option value="">-- Choisir --</option>
              @foreach($salles as $s)<option value="{{ $s->num_salle }}">{{ $s->num_salle }} ({{ $s->contenance }} pl.)</option>@endforeach
            </select>
          </div>
          <div class="form-group" style="grid-column:1/-1"><label>Personnel *</label>
            <select name="code_pers" required>
              <option value="">-- Choisir --</option>
              @foreach($personnels as $p)<option value="{{ $p->code_pers }}">{{ $p->nom_pers }} {{ $p->prenom_pers }}</option>@endforeach
            </select>
          </div>
          <div class="form-group"><label>Date *</label><input name="date" type="date" required></div>
          <div class="form-group"><label>Heure début *</label><input name="date_debut" type="datetime-local" required></div>
          <div class="form-group"><label>Heure fin *</label><input name="date_fin" type="datetime-local" required></div>
          <div class="form-group"><label>Nb heures *</label><input name="nbre_heure" type="number" min="1" required></div>
          <div class="form-group"><label>Statut *</label>
            <select name="statut" required>
              <option value="EN ATTENTE">En attente</option>
              <option value="EN COURS">En cours</option>
              <option value="ACHEVER">Achevé</option>
            </select>
          </div>
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
  <div class="modal" style="max-width:600px">
    <div class="modal-head"><h2>Modifier la séance</h2><button class="modal-close" onclick="closeModal('editModal')">×</button></div>
    <div class="modal-body">
      <form id="editForm">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <div class="form-group"><label>Date *</label><input id="e_date" name="date" type="date" required></div>
          <div class="form-group"><label>Heure début *</label><input id="e_debut" name="date_debut" type="datetime-local" required></div>
          <div class="form-group"><label>Heure fin *</label><input id="e_fin" name="date_fin" type="datetime-local" required></div>
          <div class="form-group"><label>Nb heures *</label><input id="e_nbh" name="nbre_heure" type="number" min="1" required></div>
          <div class="form-group" style="grid-column:1/-1"><label>Statut *</label>
            <select id="e_statut" name="statut" required>
              <option value="EN ATTENTE">En attente</option>
              <option value="EN COURS">En cours</option>
              <option value="ACHEVER">Achevé</option>
            </select>
          </div>
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
let current = null;
const statutColors = {'EN COURS':'badge-blue','EN ATTENTE':'badge-amber','ACHEVER':'badge-green'};

function fmt(dt) { return dt ? new Date(dt).toLocaleString('fr-FR',{dateStyle:'short',timeStyle:'short'}) : '—'; }
function fmtDate(dt) { return dt ? dt.split('T')[0] : ''; }

function row(p) {
    const sc = statutColors[p.statut]||'badge-gray';
    return `<tr>
        <td><span class="badge badge-amber">${p.code_ec}</span></td>
        <td><span class="badge badge-blue">${p.num_salle}</span></td>
        <td><span class="badge badge-gray">${p.code_pers}</span></td>
        <td>${fmt(p.date)}</td>
        <td>${fmt(p.date_debut)}</td>
        <td>${fmt(p.date_fin)}</td>
        <td>${p.nbre_heure}h</td>
        <td><span class="badge ${sc}">${p.statut}</span></td>
        <td><div class="actions">
            <button class="btn btn-primary btn-sm" onclick='editRow(${JSON.stringify(p)})'>Modifier</button>
            <button class="btn btn-danger btn-sm" onclick="deleteRow('${p.code_ec}','${p.num_salle}','${p.code_pers}')">Supprimer</button>
        </div></td>
    </tr>`;
}

async function loadData() {
    const {ok,data} = await apiCall('GET','/api/programmations');
    const tb = document.getElementById('tbody');
    tb.innerHTML = ok && data.length ? data.map(row).join('') : `<tr><td colspan="9" class="empty"><div class="empty-icon">📅</div>${ok?'Aucune séance':'Erreur'}</td></tr>`;
}

document.getElementById('createForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    fd.nbre_heure = parseInt(fd.nbre_heure);
    const {ok,data} = await apiCall('POST','/api/programmations',fd);
    if(ok){ toast('Séance créée'); closeModal('createModal'); e.target.reset(); loadData(); }
    else toast(data.message||JSON.stringify(data.errors)||'Erreur','error');
});

function editRow(p) {
    current = p;
    document.getElementById('e_date').value   = fmtDate(p.date);
    document.getElementById('e_debut').value  = p.date_debut ? p.date_debut.replace(' ','T').substring(0,16) : '';
    document.getElementById('e_fin').value    = p.date_fin   ? p.date_fin.replace(' ','T').substring(0,16)   : '';
    document.getElementById('e_nbh').value    = p.nbre_heure;
    document.getElementById('e_statut').value = p.statut;
    openModal('editModal');
}

document.getElementById('editForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    fd.nbre_heure = parseInt(fd.nbre_heure);
    const url = `/api/programmations/${current.code_ec}/${current.num_salle}/${current.code_pers}`;
    const {ok,data} = await apiCall('PUT',url,fd);
    if(ok){ toast('Séance mise à jour'); closeModal('editModal'); loadData(); }
    else toast(data.message||'Erreur','error');
});

async function deleteRow(ec, salle, pers) {
    if(!confirmDelete('Supprimer cette séance ?')) return;
    const {ok,data} = await apiCall('DELETE',`/api/programmations/${ec}/${salle}/${pers}`);
    if(ok){ toast('Séance supprimée'); loadData(); }
    else toast(data.message||'Erreur','error');
}

loadData();
</script>
@endpush
@endsection
