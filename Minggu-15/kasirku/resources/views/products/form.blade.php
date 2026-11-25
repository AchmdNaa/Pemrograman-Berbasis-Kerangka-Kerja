@extends('layouts.app')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('content')
<form action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" method="POST" class="card p-4" style="max-width: 760px" novalidate>
    @csrf
    @if ($product->exists) @method('PUT') @endif
    <div class="row g-3">
        @php($f = fn ($name) => $errors->has($name) ? 'is-invalid' : '')
        <div class="col-md-4">
            <label class="form-label" for="sku">Kode barang / barcode</label>
            <input class="form-control {{ $f('sku') }}" id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-8">
            <label class="form-label" for="name">Nama produk</label>
            <input class="form-control {{ $f('name') }}" id="name" name="name" value="{{ old('name', $product->name) }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="category_id">Kategori</label>
            <select class="form-select {{ $f('category_id') }}" id="category_id" name="category_id"><option value="">— pilih —</option>@foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach</select>
            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="unit">Satuan</label>
            <select class="form-select {{ $f('unit') }}" id="unit" name="unit">@foreach (['pcs', 'botol', 'bungkus', 'kg', 'liter', 'dus', 'sachet'] as $u)<option @selected(old('unit', $product->unit) === $u)>{{ $u }}</option>@endforeach</select>
            @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        @foreach (['cost_price' => 'Harga beli (Rp)', 'price' => 'Harga jual (Rp)', 'stock' => 'Stok', 'min_stock' => 'Stok minimum'] as $field => $label)
            <div class="col-md-3">
                <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                <input type="number" min="0" class="form-control {{ $f($field) }}" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $product->$field) }}">
                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @endforeach
        <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $product->is_active))><label class="form-check-label" for="is_active">Dijual (tampil di layar kasir)</label></div></div>
    </div>
    <div class="mt-4 d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button><a href="{{ route('products.index') }}" class="btn btn-light">Batal</a></div>
</form>
@endsection
