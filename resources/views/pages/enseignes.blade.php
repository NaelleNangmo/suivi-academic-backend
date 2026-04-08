@extends('layouts.app')
@section('title', 'Affectations — GestAcad')
@push('styles') @include('partials.crud-styles') @endpush

@section('content')

<div class="page-header">
    <h1>🔗 Affectations enseignant ↔ EC</h1>
    <button class="btn btn-success" onclick="openModal('createModal')">+ Nouvelle affectation</button>
</div>

@if(session('success'))<div class="alert alert-success">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">⚠ {{ session('error') }}</div>@endif

<div class="card">
    <div class="card-header"><span class="card-title">Liste des affectations ({{ $enseignes->count() }})</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Personnel</th><th>EC</th><th>Créé le</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($enseignes as $e)
                <tr>
                    <td>
                        <span class="badge badge-blue">{{ $e->code_pers }}</span>
                        @if($e->personnel)<span class="text-muted"> {{ $e->personnel->nom_pers }}</span>@endif
                    </td>
                    <td>
                        <span class="badge badge-amber">{{ $e->code_ec }}</span>
                        @if($e->ec)<span class="text-muted"> {{ $e->ec->label_ec }}</span>@endif
                    </td>
                    <td class="text-muted">{{ $e->created_at?->format('d/m/Y') ?? '—' }}</td>
                    <td>
                        <form method="POST" action="/enseignes/{{ $e->code_pers }}/{{ $e->code_ec }}" onsubmit="return confirm('Retirer cette affectation ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Retirer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty"><div class="empty-icon">🔗</div>Aucune affectation</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="overlay" id="createModal">
  <div class="modal">
    <div class="modal-head"><h2>Nouvelle affectation</h2><button class="modal-close" onclick="closeModal('createModal')">×</button></div>
    <div class="modal-body">
      <form method="POST" action="/enseignes">
        @csrf
        <div class="form-group"><label>Personnel *</label>
          <select name="code_pers" required>
            <option value="">-- Choisir --</option>
            @foreach($personnels as $p)<option value="{{ $p->code_pers }}" {{ old('code_pers')==$p->code_pers?'selected':'' }}>{{ $p->nom_pers }} {{ $p->prenom_pers }} ({{ $p->code_pers }})</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label>Élément constitutif *</label>
          <select name="code_ec" required>
            <option value="">-- Choisir --</option>
            @foreach($ecs as $ec)<option value="{{ $ec->code_ec }}" {{ old('code_ec')==$ec->code_ec?'selected':'' }}>{{ $ec->label_ec }} ({{ $ec->code_ec }})</option>@endforeach
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
@endpush
@endsection
