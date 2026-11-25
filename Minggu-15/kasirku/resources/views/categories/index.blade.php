@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
<div class="row g-4">
    <div class="col-lg-7"><div class="card p-3">
        <table class="table align-middle small mb-0">
            <thead class="table-light"><tr><th>Kategori</th><th class="text-center">Produk</th><th></th></tr></thead>
            @foreach ($categories as $c)
                <tr><td><span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background: {{ $c->color }}"></span><span class="fw-semibold">{{ $c->name }}</span></td><td class="text-center">{{ $c->products_count }}</td>
                    <td class="text-end"><a href="{{ route('categories.index', ['edit' => $c->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('categories.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
            @endforeach
        </table>
    </div></div>
    <div class="col-lg-5"><div class="card p-4">
        <h2 class="h6 fw-bold">{{ $edit ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
        <form action="{{ $edit ? route('categories.update', $edit) : route('categories.store') }}" method="POST" novalidate>
            @csrf @if ($edit) @method('PUT') @endif
            <div class="mb-2"><label class="form-label small" for="name">Nama</label><input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $edit->name ?? '') }}">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label small" for="color">Warna label</label><input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $edit->color ?? '#4f46e5') }}">@error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <button class="btn btn-primary">Simpan</button>@if ($edit) <a href="{{ route('categories.index') }}" class="btn btn-light">Batal</a>@endif
        </form>
    </div></div>
</div>
@endsection
