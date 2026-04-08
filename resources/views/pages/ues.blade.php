@extends('layouts.app')
@section('title', 'Unités d\'enseignement — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')

<div class="page-header">
    <h1>📖 Unités d'enseignement</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle UE</button>
</div>

@if(session('success'))<div class="alert alert-success">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">⚠ {{ session('error') }}</div>@endif

<div class="card">
    <div class="card-header"><span class="card-title">Liste des UE ({{ $ues->count() }})</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Libellé</th><th>Niveau</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($ues as $u)
                <tr>
                    <td><span class="badge badge-green">{{ $u->code_ue }}</span></td>
                    <td>{{ $u->label_ue }}</td>
                    <td><span class="badge badge-blue">{{ $u->code_niveau }}</span></td>
                    <td class="text-muted">{{ Str::limit($u->desc_ue, 50) }}</td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-primary btn-sm" onclick='openEdit(@json($u))'>Modifier</button>
                            <form method="POST" action="/ues/{{ $u->code_ue }}" onsubmit="return confirm('Supprimer cette UE ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty"><div class="empty-icon">📖</div>Aucune UE</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvelle UE</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form method="POST" action="/ues">
        @csrf
        <div class="form-group"><label>Code UE *</label><input name="code_ue" required placeholder="Ex: UE-ALGO-01" value="{{ old('code_ue') }}"></div>
        <div class="form-group"><label>Libellé *</label><input name="label_ue" required placeholder="Ex: Algorithmique" value="{{ old('label_ue') }}"></div>
        <div class="form-group"><label>Description *</label><textarea name="desc_ue" rows="2" required>{{ old('desc_ue') }}</textarea></div>
        <div class="form-group"><label>Niveau *</label>
          <select name="code_niveau" required>
            <option value="">-- Choisir --</option>
            @foreach($niveaux as $n)<option value="{{ $n->code_niveau }}" {{ old('code_niveau')==$n->code_niveau?'selected':'' }}>{{ $n->label_niveau }} ({{ $n->code_filiere }})</option>@endforeach
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
      <form id="editForm" method="POST">
        @csrf @method('PUT')
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
function openEdit(u) {
    document.getElementById('e_code').value   = u.code_ue;
    document.getElementById('e_label').value  = u.label_ue;
    document.getElementById('e_desc').value   = u.desc_ue || '';
    document.getElementById('e_niveau').value = u.code_niveau;
    document.getElementById('editForm').action = '/ues/' + u.code_ue;
    openModal('editModal');
}
</script>
@endpush
@endsection
