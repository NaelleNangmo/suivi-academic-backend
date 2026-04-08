@extends('layouts.app')
@section('title', 'Programmation — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')

<div class="page-header">
    <h1>📅 Programmation des cours</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle séance</button>
</div>

@if(session('success'))<div class="alert alert-success">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">⚠ {{ session('error') }}</div>@endif

@php $statutColors = ['EN COURS'=>'badge-blue','EN ATTENTE'=>'badge-amber','ACHEVER'=>'badge-green']; @endphp

<div class="card">
    <div class="card-header"><span class="card-title">Liste des séances ({{ $programmations->count() }})</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>EC</th><th>Salle</th><th>Personnel</th><th>Date</th><th>Début</th><th>Fin</th><th>Heures</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($programmations as $p)
                <tr>
                    <td><span class="badge badge-amber">{{ $p->code_ec }}</span></td>
                    <td><span class="badge badge-blue">{{ $p->num_salle }}</span></td>
                    <td><span class="badge badge-gray">{{ $p->code_pers }}</span></td>
                    <td>{{ $p->date ? \Carbon\Carbon::parse($p->date)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $p->date_debut ? \Carbon\Carbon::parse($p->date_debut)->format('H:i') : '—' }}</td>
                    <td>{{ $p->date_fin ? \Carbon\Carbon::parse($p->date_fin)->format('H:i') : '—' }}</td>
                    <td>{{ $p->nbre_heure }}h</td>
                    <td><span class="badge {{ $statutColors[$p->statut] ?? 'badge-gray' }}">{{ $p->statut }}</span></td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-primary btn-sm" onclick='openEdit(@json($p))'>Modifier</button>
                            <form method="POST" action="/programmations/{{ $p->code_ec }}/{{ $p->num_salle }}/{{ $p->code_pers }}" onsubmit="return confirm('Supprimer cette séance ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty"><div class="empty-icon">📅</div>Aucune séance programmée</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal" style="max-width:600px">
    <div class="modal-head"><h2>Nouvelle séance</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form method="POST" action="/programmations">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <div class="form-group"><label>EC *</label>
            <select name="code_ec" required>
              <option value="">-- Choisir --</option>
              @foreach($ecs as $ec)<option value="{{ $ec->code_ec }}" {{ old('code_ec')==$ec->code_ec?'selected':'' }}>{{ $ec->label_ec }}</option>@endforeach
            </select>
          </div>
          <div class="form-group"><label>Salle *</label>
            <select name="num_salle" required>
              <option value="">-- Choisir --</option>
              @foreach($salles as $s)<option value="{{ $s->num_salle }}" {{ old('num_salle')==$s->num_salle?'selected':'' }}>{{ $s->num_salle }} ({{ $s->contenance }} pl.)</option>@endforeach
            </select>
          </div>
          <div class="form-group" style="grid-column:1/-1"><label>Personnel *</label>
            <select name="code_pers" required>
              <option value="">-- Choisir --</option>
              @foreach($personnels as $pers)<option value="{{ $pers->code_pers }}" {{ old('code_pers')==$pers->code_pers?'selected':'' }}>{{ $pers->nom_pers }} {{ $pers->prenom_pers }}</option>@endforeach
            </select>
          </div>
          <div class="form-group"><label>Date *</label><input name="date" type="date" required value="{{ old('date') }}"></div>
          <div class="form-group"><label>Heure début *</label><input name="date_debut" type="datetime-local" required value="{{ old('date_debut') }}"></div>
          <div class="form-group"><label>Heure fin *</label><input name="date_fin" type="datetime-local" required value="{{ old('date_fin') }}"></div>
          <div class="form-group"><label>Nb heures *</label><input name="nbre_heure" type="number" min="1" required value="{{ old('nbre_heure') }}"></div>
          <div class="form-group"><label>Statut *</label>
            <select name="statut" required>
              <option value="EN ATTENTE" {{ old('statut')=='EN ATTENTE'?'selected':'' }}>En attente</option>
              <option value="EN COURS" {{ old('statut')=='EN COURS'?'selected':'' }}>En cours</option>
              <option value="ACHEVER" {{ old('statut')=='ACHEVER'?'selected':'' }}>Achevé</option>
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
      <form id="editForm" method="POST">
        @csrf @method('PUT')
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
function fmtDt(dt) {
    if (!dt) return '';
    return dt.replace(' ', 'T').substring(0, 16);
}
function openEdit(p) {
    document.getElementById('e_date').value   = p.date ? p.date.substring(0,10) : '';
    document.getElementById('e_debut').value  = fmtDt(p.date_debut);
    document.getElementById('e_fin').value    = fmtDt(p.date_fin);
    document.getElementById('e_nbh').value    = p.nbre_heure;
    document.getElementById('e_statut').value = p.statut;
    document.getElementById('editForm').action = '/programmations/' + p.code_ec + '/' + p.num_salle + '/' + p.code_pers;
    openModal('editModal');
}
</script>
@endpush
@endsection
