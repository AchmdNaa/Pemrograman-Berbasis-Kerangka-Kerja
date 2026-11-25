@extends('layouts.app')
@section('title', 'Produk')
@section('content')
<div class="d-flex justify-content-between mb-3 gap-2 flex-wrap">
    <form class="d-flex gap-2" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Cari nama / kode">
        <select name="kategori" class="form-select form-select-sm"><option value="">Semua kategori</option>@foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('kategori') == $c->id)>{{ $c->name }}</option>@endforeach</select>
        <select name="stok" class="form-select form-select-sm"><option value="">Semua stok</option><option value="menipis" @selected(request('stok') === 'menipis')>Stok menipis</option></select>
        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel"></i></button>
    </form>
    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
</div>
<div class="card p-3">
    <table class="table align-middle small">
        <thead class="table-light"><tr><th>Kode</th><th>Produk</th><th>Kategori</th><th class="text-end">Harga beli</th><th class="text-end">Harga jual</th><th class="text-end">Margin</th><th class="text-center">Stok</th><th></th></tr></thead>
        @forelse ($products as $p)
            <tr class="{{ $p->is_active ? '' : 'table-secondary' }}">
                <td class="font-monospace">{{ $p->sku }}</td>
                <td class="fw-semibold">{{ $p->name }} @unless ($p->is_active)<span class="badge text-bg-secondary">nonaktif</span>@endunless</td>
                <td><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: {{ $p->category->color }}"></span>{{ $p->category->name }}</td>
                <td class="text-end">{{ rupiah($p->cost_price) }}</td>
                <td class="text-end fw-semibold">{{ rupiah($p->price) }}</td>
                <td class="text-end text-success">{{ rupiah($p->margin) }}</td>
                <td class="text-center"><span class="badge {{ $p->stock == 0 ? 'text-bg-danger' : ($p->is_low_stock ? 'text-bg-warning' : 'text-bg-light border') }}">{{ $p->stock }} {{ $p->unit }}</span></td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('products.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus {{ $p->name }}?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada produk.</td></tr>
        @endforelse
    </table>
    {{ $products->links() }}
</div>
@endsection
