@extends('layouts.app')
@section('title', 'Struk ' . $trx->invoice_no)
@push('styles')
<style>
    .receipt { width: 340px; margin: 0 auto; background: #fff; font-family: "Courier New", monospace; font-size: 13px; padding: 1.2rem; box-shadow: 0 .3rem 1.5rem rgba(0,0,0,.1); }
    .receipt hr { border-top: 1px dashed #000; opacity: 1; margin: .5rem 0; }
    @media print { body { background: #fff; } main { padding: 0 !important; } .receipt { box-shadow: none; width: 80mm; padding: 0; } }
</style>
@endpush
@section('content')
<div class="d-flex justify-content-center gap-2 mb-3 d-print-none">
    <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak Struk</button>
    <a href="{{ route('pos.index') }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg"></i> Transaksi Baru</a>
    <a href="{{ route('transactions.index') }}" class="btn btn-light border">Riwayat</a>
</div>
<div class="receipt">
    <div class="text-center">
        <div class="fw-bold fs-5">KASIRKU MART</div>
        <div>Jl. Teknik Kimia, Keputih, Surabaya</div>
        <div>Telp. 0812-3456-7890</div>
    </div>
    <hr>
    <div class="d-flex justify-content-between"><span>{{ $trx->invoice_no }}</span><span>{{ $trx->created_at->format('d/m/Y H:i') }}</span></div>
    <div>Kasir: {{ $trx->cashier->name }}</div>
    <hr>
    @foreach ($trx->items as $item)
        <div>{{ $item->product_name }}</div>
        <div class="d-flex justify-content-between"><span>&nbsp;&nbsp;{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</span><span>{{ number_format($item->subtotal, 0, ',', '.') }}</span></div>
    @endforeach
    <hr>
    <div class="d-flex justify-content-between"><span>Subtotal</span><span>{{ number_format($trx->subtotal, 0, ',', '.') }}</span></div>
    @if ($trx->discount)<div class="d-flex justify-content-between"><span>Diskon</span><span>-{{ number_format($trx->discount, 0, ',', '.') }}</span></div>@endif
    <div class="d-flex justify-content-between fw-bold fs-6"><span>TOTAL</span><span>{{ number_format($trx->total, 0, ',', '.') }}</span></div>
    <div class="d-flex justify-content-between"><span>Bayar ({{ \App\Models\Transaction::METHODS[$trx->payment_method] }})</span><span>{{ number_format($trx->paid, 0, ',', '.') }}</span></div>
    <div class="d-flex justify-content-between"><span>Kembali</span><span>{{ number_format($trx->change, 0, ',', '.') }}</span></div>
    @if ($trx->note)<hr><div>Catatan: {{ $trx->note }}</div>@endif
    <hr>
    <div class="text-center">{{ $trx->items->sum('qty') }} item · Terima kasih!<br>Barang yang sudah dibeli tidak dapat dikembalikan.</div>
</div>
@endsection
