@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="row g-4">
    <div class="col-xl-7"><div class="card p-3">
        <table class="table align-middle small mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>Peran</th><th class="text-center">Transaksi hari ini</th><th class="text-end">Total omzet</th><th></th></tr></thead>
            @foreach ($users as $u)
                <tr class="{{ $u->is_active ? '' : 'table-secondary' }}"><td><div class="fw-semibold">{{ $u->name }} @if ($u->is(auth()->user()))<span class="text-muted">(Anda)</span>@endif</div><div class="text-muted">{{ $u->email }}</div></td>
                    <td><span class="badge {{ $u->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">{{ $u->role }}</span> @unless ($u->is_active)<span class="badge text-bg-dark">nonaktif</span>@endunless</td>
                    <td class="text-center">{{ $u->trx_today }}</td><td class="text-end">{{ rupiah($u->omzet) }}</td>
                    <td class="text-end"><a href="{{ route('users.index', ['edit' => $u->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td></tr>
            @endforeach
        </table>
    </div></div>
    <div class="col-xl-5"><div class="card p-4">
        <h2 class="h6 fw-bold">{{ $edit ? 'Edit Akun' : 'Tambah Akun Kasir' }}</h2>
        <form action="{{ $edit ? route('users.update', $edit) : route('users.store') }}" method="POST" novalidate>
            @csrf @if ($edit) @method('PUT') @endif
            @foreach (['name' => 'Nama', 'email' => 'Email'] as $field => $label)
                <div class="mb-2"><label class="form-label small" for="{{ $field }}">{{ $label }}</label><input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $edit->$field ?? '') }}">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            @endforeach
            <div class="mb-2"><label class="form-label small" for="password">Password {{ $edit ? '(kosongkan bila tidak diganti)' : '' }}</label><input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="mb-2"><label class="form-label small" for="role">Peran</label><select class="form-select" id="role" name="role"><option value="kasir" @selected(old('role', $edit->role ?? 'kasir') === 'kasir')>Kasir</option><option value="admin" @selected(old('role', $edit->role ?? '') === 'admin')>Admin</option></select></div>
            <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $edit->is_active ?? true))><label class="form-check-label" for="is_active">Akun aktif</label></div>
            <button class="btn btn-primary">Simpan</button>@if ($edit) <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>@endif
        </form>
    </div></div>
</div>
@endsection
