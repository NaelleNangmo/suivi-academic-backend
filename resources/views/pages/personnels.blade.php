@extends('layouts.app')
@section('title', 'Personnel — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')

<div class="page-header">
    <h1>👥 Personnel</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouveau membre</button>
</div>

@if(session('success'))<div class="alert alert-success">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">⚠ {{ session('error') }}</div>@endif

@php $typeColors = ['ENSEIGNANT'=>'badge-blue','RESPONSABLE ACADEMIQUE'=>'badge-purple','RESPONSABLE DISCIPLINE'=>'badge-amber']; @endphp

<div class="card">
    <div class="card-header"><span class="card-title">Liste du personnel ({{ $personnels->count() }})</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Code</th><th>Nom & Prénom</th><th>Sexe</th><th>Téléphone</th><th>Login</th><th>Type</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($personnels as $p)
                <tr>
                    <td><span class="badge badge-gray">{{ $p->code_pers }}</span></td>
                    <td>{{ $p->nom_pers }} {{ $p->prenom_pers }}</td>
                    <td>{{ $p->sexe_pers }}</td>
                    <td>{{ $p->phone_pers }}</td>
                    <td class="text-muted">{{ $p->login_pers }}</td>
                    <td><span class="badge {{ $typeColors[$p->type_pers] ?? 'badge-gray' }}">{{ $p->type_pers }}</span></td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-primary btn-sm" onclick='openEdit(@json($p))'>Modifier</button>
                            <form method="POST" action="/personnels/{{ $p->code_pers }}" onsubmit="return confirm('Supprimer ce membre ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty"><div class="empty-icon">👥</div>Aucun membre</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal" style="max-width:580px">
    <div class="modal-head"><h2>Nouveau membre</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form method="POST" action="/personnels">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="form-group"><label>Code *</label><input name="code_pers" required placeholder="Ex: P001" value="{{ old('code_pers') }}"></div>
            <div class="form-group"><label>Sexe *</label>
              <select name="sexe_pers" required><option value="M" {{ old('sexe_pers')=='M'?'selected':'' }}>Masculin</option><option value="F" {{ old('sexe_pers')=='F'?'selected':'' }}>Féminin</option></select>
            </div>
            <div class="form-group"><label>Nom *</label><input name="nom_pers" required value="{{ old('nom_pers') }}"></div>
            <div class="form-group"><label>Prénom</label><input name="prenom_pers" value="{{ old('prenom_pers') }}"></div>
        </div>
        <div class="form-group"><label>Téléphone *</label><input name="phone_pers" required placeholder="Ex: 0600000000" value="{{ old('phone_pers') }}"></div>
        <div class="form-group"><label>Email (login) *</label><input name="login_pers" type="email" required value="{{ old('login_pers') }}"></div>
        <div class="form-group"><label>Mot de passe *</label><input name="pwd_pers" type="password" required minlength="6"></div>
        <div class="form-group"><label>Type *</label>
          <select name="type_pers" required>
            <option value="ENSEIGNANT" {{ old('type_pers')=='ENSEIGNANT'?'selected':'' }}>Enseignant</option>
            <option value="RESPONSABLE ACADEMIQUE" {{ old('type_pers')=='RESPONSABLE ACADEMIQUE'?'selected':'' }}>Responsable académique</option>
            <option value="RESPONSABLE DISCIPLINE" {{ old('type_pers')=='RESPONSABLE DISCIPLINE'?'selected':'' }}>Responsable discipline</option>
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
  <div class="modal" style="max-width:580px">
    <div class="modal-head"><h2>Modifier le membre</h2><button class="modal-close" onclick="closeModal('editModal')">×</button></div>
    <div class="modal-body">
      <form id="editForm" method="POST">
        @csrf @method('PUT')
        <div class="form-group"><label>Code</label><input id="e_code" disabled><p class="form-hint">Non modifiable</p></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
            <div class="form-group"><label>Nom *</label><input id="e_nom" name="nom_pers" required></div>
            <div class="form-group"><label>Prénom</label><input id="e_prenom" name="prenom_pers"></div>
            <div class="form-group"><label>Sexe *</label>
              <select id="e_sexe" name="sexe_pers" required><option value="M">Masculin</option><option value="F">Féminin</option></select>
            </div>
            <div class="form-group"><label>Téléphone *</label><input id="e_phone" name="phone_pers" required></div>
        </div>
        <div class="form-group"><label>Email *</label><input id="e_login" name="login_pers" type="email" required></div>
        <div class="form-group"><label>Nouveau mot de passe <span class="form-hint" style="display:inline">(laisser vide = inchangé)</span></label><input name="pwd_pers" type="password" minlength="6"></div>
        <div class="form-group"><label>Type *</label>
          <select id="e_type" name="type_pers" required>
            <option value="ENSEIGNANT">Enseignant</option>
            <option value="RESPONSABLE ACADEMIQUE">Responsable académique</option>
            <option value="RESPONSABLE DISCIPLINE">Responsable discipline</option>
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
function openEdit(p) {
    document.getElementById('e_code').value   = p.code_pers;
    document.getElementById('e_nom').value    = p.nom_pers;
    document.getElementById('e_prenom').value = p.prenom_pers || '';
    document.getElementById('e_sexe').value   = p.sexe_pers;
    document.getElementById('e_phone').value  = p.phone_pers;
    document.getElementById('e_login').value  = p.login_pers;
    document.getElementById('e_type').value   = p.type_pers;
    document.getElementById('editForm').action = '/personnels/' + p.code_pers;
    openModal('editModal');
}
</script>
@endpush
@endsection
