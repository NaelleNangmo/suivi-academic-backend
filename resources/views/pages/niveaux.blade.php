@extends('layouts.app')
@section('title', 'Niveaux — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')

<div class="page-header">
    <h1>📚 Niveaux</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouveau niveau</button>
</div>

@if(session('success'))<div class="alert alert-success">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">⚠ {{ session('error') }}</div>@endif

<div class="card">
    <div class="card-header"><span class="card-title">Liste des niveaux ({{ $niveaux->count() }})</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Libellé</th><th>Filière</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($niveaux as $n)
                <tr>
                    <td><span class="badge badge-blue">{{ $n->code_niveau }}</span></td>
                    <td>{{ $n->label_niveau }}</td>
                    <td><span class="badge badge-purple">{{ $n->code_filiere }}</span></td>
                    <td class="text-muted">{{ Str::limit($n->desc_niveau, 50) }}</td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-primary btn-sm" onclick='openEdit(@json($n))'>Modifier</button>
                            <form method="POST" action="/niveaux/{{ $n->code_niveau }}" onsubmit="return confirm('Supprimer ce niveau ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty"><div class="empty-icon">📚</div>Aucun niveau</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouveau niveau</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form method="POST" action="/niveaux">
        @csrf
        <div class="form-group"><label>Libellé *</label><input name="label_niveau" required placeholder="Ex: Licence 1" value="{{ old('label_niveau') }}"></div>
        <div class="form-group"><label>Description *</label><textarea name="desc_niveau" rows="2" required>{{ old('desc_niveau') }}</textarea></div>
        <div class="form-group"><label>Filière *</label>
          <select name="code_filiere" required>
            <option value="">-- Choisir --</option>
            @foreach($filieres as $f)<option value="{{ $f->code_filiere }}" {{ old('code_filiere')==$f->code_filiere?'selected':'' }}>{{ $f->label_filiere }}</option>@endforeach
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
      <form id="editForm" method="POST">
        @csrf @method('PUT')
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
function openEdit(n) {
    document.getElementById('e_label').value   = n.label_niveau;
    document.getElementById('e_desc').value    = n.desc_niveau || '';
    document.getElementById('e_filiere').value = n.code_filiere;
    document.getElementById('editForm').action = '/niveaux/' + n.code_niveau;
    openModal('editModal');
}
</script>
@endpush
@endsection
