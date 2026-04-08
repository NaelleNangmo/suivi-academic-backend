@extends('layouts.app')
@section('title', 'Éléments constitutifs — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')

<div class="page-header">
    <h1>📝 Éléments constitutifs</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvel EC</button>
</div>

@if(session('success'))<div class="alert alert-success">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">⚠ {{ session('error') }}</div>@endif

<div class="card">
    <div class="card-header"><span class="card-title">Liste des EC ({{ $ecs->count() }})</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Libellé</th><th>UE</th><th>Heures</th><th>Crédits</th><th>Support</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($ecs as $ec)
                <tr>
                    <td><span class="badge badge-amber">{{ $ec->code_ec }}</span></td>
                    <td>{{ $ec->label_ec }}</td>
                    <td><span class="badge badge-green">{{ $ec->code_ue }}</span></td>
                    <td>{{ $ec->nbh_ec }}h</td>
                    <td>{{ $ec->nbc_ec }} cr.</td>
                    <td>
                        @if($ec->support_cours)
                            <a href="{{ asset('storage/'.$ec->support_cours) }}" target="_blank" class="btn btn-warning btn-sm">📄 PDF</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-primary btn-sm" onclick='openEdit(@json($ec))'>Modifier</button>
                            <form method="POST" action="/ecs/{{ $ec->code_ec }}" onsubmit="return confirm('Supprimer cet EC ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty"><div class="empty-icon">📝</div>Aucun EC</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvel EC</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form method="POST" action="/ecs" enctype="multipart/form-data">
        @csrf
        <div class="form-group"><label>Code EC *</label><input name="code_ec" required placeholder="Ex: EC-ALGO-01" value="{{ old('code_ec') }}"></div>
        <div class="form-group"><label>Libellé *</label><input name="label_ec" required placeholder="Ex: Algorithmique avancée" value="{{ old('label_ec') }}"></div>
        <div class="form-group"><label>Description *</label><textarea name="desc_ec" rows="2" required>{{ old('desc_ec') }}</textarea></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="form-group"><label>Heures *</label><input name="nbh_ec" type="number" min="1" required value="{{ old('nbh_ec') }}"></div>
            <div class="form-group"><label>Crédits *</label><input name="nbc_ec" type="number" min="1" required value="{{ old('nbc_ec') }}"></div>
        </div>
        <div class="form-group"><label>UE *</label>
          <select name="code_ue" required>
            <option value="">-- Choisir --</option>
            @foreach($ues as $u)<option value="{{ $u->code_ue }}" {{ old('code_ue')==$u->code_ue?'selected':'' }}>{{ $u->label_ue }}</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label>Support de cours (PDF, max 10 Mo)</label><input type="file" name="support_cours" accept=".pdf"></div>
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
    <div class="modal-head"><h2>Modifier l'EC</h2><button class="modal-close" onclick="closeModal('editModal')">×</button></div>
    <div class="modal-body">
      <form id="editForm" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="form-group"><label>Code</label><input id="e_code" disabled><p class="form-hint">Non modifiable</p></div>
        <div class="form-group"><label>Libellé *</label><input id="e_label" name="label_ec" required></div>
        <div class="form-group"><label>Description *</label><textarea id="e_desc" name="desc_ec" rows="2" required></textarea></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="form-group"><label>Heures *</label><input id="e_nbh" name="nbh_ec" type="number" min="1" required></div>
            <div class="form-group"><label>Crédits *</label><input id="e_nbc" name="nbc_ec" type="number" min="1" required></div>
        </div>
        <div class="form-group"><label>UE *</label>
          <select id="e_ue" name="code_ue" required>
            <option value="">-- Choisir --</option>
            @foreach($ues as $u)<option value="{{ $u->code_ue }}">{{ $u->label_ue }}</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label>Nouveau support PDF <span class="form-hint" style="display:inline">(laisser vide = inchangé)</span></label><input type="file" name="support_cours" accept=".pdf"></div>
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
function openEdit(ec) {
    document.getElementById('e_code').value  = ec.code_ec;
    document.getElementById('e_label').value = ec.label_ec;
    document.getElementById('e_desc').value  = ec.desc_ec || '';
    document.getElementById('e_nbh').value   = ec.nbh_ec;
    document.getElementById('e_nbc').value   = ec.nbc_ec;
    document.getElementById('e_ue').value    = ec.code_ue;
    document.getElementById('editForm').action = '/ecs/' + ec.code_ec;
    openModal('editModal');
}
</script>
@endpush
@endsection
