@extends('layouts.app')
@section('title', 'Affectations — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
    <h1>🔗 Affectations enseignant ↔ EC</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle affectation</button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Liste des affectations</span>
        <button class="btn btn-secondary btn-sm" onclick="loadData()">↻ Actualiser</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Personnel</th><th>EC</th><th>Créé le</th><th>Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="4" class="empty"><div class="empty-icon">⏳</div>Chargement...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvelle affectation</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form id="createForm">
        <div class="form-group"><label>Personnel *</label>
          <select name="code_pers" required>
            <option value="">-- Choisir --</option>
            @foreach($personnels as $p)<option value="{{ $p->code_pers }}">{{ $p->nom_pers }} {{ $p->prenom_pers }} ({{ $p->code_pers }})</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label>Élément constitutif *</label>
          <select name="code_ec" required>
            <option value="">-- Choisir --</option>
            @foreach($ecs as $ec)<option value="{{ $ec->code_ec }}">{{ $ec->label_ec }} ({{ $ec->code_ec }})</option>@endforeach
          </select>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Annuler</button>
            <button type="submit" class="btn btn-success">Affecter</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
@include('partials.crud-scripts')
<script>
function row(e) {
    const date = e.created_at ? new Date(e.created_at).toLocaleDateString('fr-FR') : '—';
    return `<tr>
        <td><span class="badge badge-blue">${e.code_pers}</span></td>
        <td><span class="badge badge-amber">${e.code_ec}</span></td>
        <td class="text-muted">${date}</td>
        <td><button class="btn btn-danger btn-sm" onclick="deleteRow('${e.code_pers}','${e.code_ec}')">Retirer</button></td>
    </tr>`;
}

async function loadData() {
    const {ok,data} = await apiCall('GET','/api/enseignes');
    const tb = document.getElementById('tbody');
    tb.innerHTML = ok && data.length ? data.map(row).join('') : `<tr><td colspan="4" class="empty"><div class="empty-icon">🔗</div>${ok?'Aucune affectation':'Erreur'}</td></tr>`;
}

document.getElementById('createForm').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = Object.fromEntries(new FormData(e.target));
    const {ok,data} = await apiCall('POST','/api/enseignes',fd);
    if(ok){ toast('Affectation créée'); closeModal('createModal'); e.target.reset(); loadData(); }
    else toast(data.message||JSON.stringify(data.errors)||'Erreur','error');
});

async function deleteRow(codePers, codeEc) {
    if(!confirmDelete('Retirer cette affectation ?')) return;
    const {ok,data} = await apiCall('DELETE',`/api/enseignes/${codePers}/${codeEc}`);
    if(ok){ toast('Affectation retirée'); loadData(); }
    else toast(data.message||'Erreur','error');
}

loadData();
</script>
@endpush
@endsection
