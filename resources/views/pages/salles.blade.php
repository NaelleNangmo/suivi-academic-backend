@extends('layouts.app')
@section('title', 'Salles — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')

<div class="page-header">
    <h1>🏛️ Salles</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle salle</button>
</div>

@if(session('success'))<div class="alert alert-success">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">⚠ {{ session('error') }}</div>@endif

<div class="card">
    <div class="card-header"><span class="card-title">Liste des salles ({{ $salles->count() }})</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Numéro</th><th>Contenance</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($salles as $s)
                <tr>
                    <td><span class="badge badge-blue">{{ $s->num_salle }}</span></td>
                    <td>{{ $s->contenance }} places</td>
                    <td><span class="badge {{ $s->statut === 'DISPONIBLE' ? 'badge-green' : 'badge-red' }}">{{ $s->statut }}</span></td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-primary btn-sm" onclick='openEdit(@json($s))'>Modifier</button>
                            <form method="POST" action="/salles/{{ $s->num_salle }}" onsubmit="return confirm('Supprimer cette salle ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty"><div class="empty-icon">🏛️</div>Aucune salle</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvelle salle</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form method="POST" action="/salles">
        @csrf
        <div class="form-group"><label>Numéro *</label><input name="num_salle" required placeholder="Ex: A101" value="{{ old('num_salle') }}"></div>
        <div class="form-group"><label>Contenance *</label><input name="contenance" type="number" min="1" required placeholder="Ex: 50" value="{{ old('contenance') }}"></div>
        <div class="form-group"><label>Statut *</label>
          <select name="statut" required>
            <option value="DISPONIBLE" {{ old('statut')=='DISPONIBLE'?'selected':'' }}>Disponible</option>
            <option value="NON DISPONIBLE" {{ old('statut')=='NON DISPONIBLE'?'selected':'' }}>Non disponible</option>
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
      <form id="editForm" method="POST">
        @csrf @method('PUT')
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
function openEdit(s) {
    document.getElementById('e_num').value    = s.num_salle;
    document.getElementById('e_cont').value   = s.contenance;
    document.getElementById('e_statut').value = s.statut;
    document.getElementById('editForm').action = '/salles/' + s.num_salle;
    openModal('editModal');
}
</script>
@endpush
@endsection
