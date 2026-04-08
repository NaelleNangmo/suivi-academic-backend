@extends('layouts.app')
@section('title', 'Filières — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')

<div class="page-header">
    <h1>🏫 Filières</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle filière</button>
</div>

@if(session('success'))<div class="alert alert-success">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">⚠ {{ session('error') }}</div>@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">Liste des filières ({{ $filieres->count() }})</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Libellé</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($filieres as $f)
                <tr>
                    <td><span class="badge badge-purple">{{ $f->code_filiere }}</span></td>
                    <td>{{ $f->label_filiere }}</td>
                    <td class="text-muted">{{ Str::limit($f->desc_filiere, 60) }}</td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-primary btn-sm" onclick='openEdit(@json($f))'>Modifier</button>
                            <form method="POST" action="/filieres/{{ $f->code_filiere }}" onsubmit="return confirm('Supprimer cette filière ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty"><div class="empty-icon">📂</div>Aucune filière</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Créer --}}
<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvelle filière</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form method="POST" action="/filieres">
        @csrf
        <div class="form-group"><label>Code *</label><input name="code_filiere" required placeholder="Ex: INFO-L" value="{{ old('code_filiere') }}"></div>
        <div class="form-group"><label>Libellé *</label><input name="label_filiere" required placeholder="Ex: Informatique" value="{{ old('label_filiere') }}"></div>
        <div class="form-group"><label>Description *</label><textarea name="desc_filiere" rows="3" required>{{ old('desc_filiere') }}</textarea></div>
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
      <form id="editForm" method="POST">
        @csrf @method('PUT')
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
function openEdit(f) {
    document.getElementById('e_code').value  = f.code_filiere;
    document.getElementById('e_label').value = f.label_filiere;
    document.getElementById('e_desc').value  = f.desc_filiere || '';
    document.getElementById('editForm').action = '/filieres/' + f.code_filiere;
    openModal('editModal');
}
</script>
@endpush
@endsection
