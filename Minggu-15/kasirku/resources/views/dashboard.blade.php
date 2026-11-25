@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card stat p-3 h-100"><div class="small text-muted">Penjualan hari ini</div><div class="num">{{ rupiah($salesToday) }}</div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card stat p-3 h-100" style="--c: #0ea5e9"><div class="small text-muted">Transaksi hari ini</div><div class="num">{{ $countToday }}</div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card stat p-3 h-100" style="--c: #f59e0b"><div class="small text-muted">Barang terjual hari ini</div><div class="num">{{ $itemsToday }}</div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card stat p-3 h-100" style="--c: #10b981"><div class="small text-muted">Laba kotor hari ini</div><div class="num">{{ $profitToday === null ? '—' : rupiah($profitToday) }}</div>@if ($profitToday === null)<div class="small text-muted">hanya untuk admin</div>@endif</div></div>
</div>
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card p-3 h-100">
            <h2 class="h6 fw-bold">Penjualan 7 Hari Terakhir</h2>
            @php($max = max($week->max('total'), 1))
            <div class="d-flex align-items-end gap-3 pt-3" style="height: 230px">
                @foreach ($week as $d)
                    <div class="flex-fill text-center d-flex flex-column justify-content-end h-100">
                        <div class="small fw-semibold mb-1" style="font-size: .7rem">{{ $d['total'] ? number_format($d['total'] / 1000, 0, ',', '.') . 'rb' : '' }}</div>
                        <div class="rounded-top" style="height: {{ max(2, round($d['total'] / $max * 170)) }}px; background: linear-gradient(#818cf8, #4f46e5)"></div>
                        <div class="small text-muted mt-1">{{ $d['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card p-3 h-100">
            <h2 class="h6 fw-bold">Produk Terlaris (30 hari)</h2>
            @forelse ($topProducts as $i => $p)
                <div class="d-flex justify-content-between border-bottom py-2 small"><span><span class="badge bg-primary me-1">{{ $i + 1 }}</span> {{ $p->product_name }}</span><span class="text-muted">{{ $p->qty }} terjual</span></div>
            @empty
                <p class="small text-muted">Belum ada penjualan.</p>
            @endforelse
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-xl-7">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between mb-2"><h2 class="h6 fw-bold mb-0">Transaksi Terbaru</h2><a href="{{ route('transactions.index') }}" class="small">Semua</a></div>
            <table class="table table-sm small mb-0">
                @foreach ($recent as $t)
                    <tr><td><a href="{{ route('transactions.show', $t) }}" class="font-monospace">{{ $t->invoice_no }}</a></td><td>{{ $t->created_at->format('d/m H:i') }}</td><td>{{ $t->cashier->name }}</td><td class="text-end fw-semibold">{{ rupiah($t->total) }}</td></tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card p-3 h-100">
            <h2 class="h6 fw-bold"><i class="bi bi-exclamation-triangle text-warning"></i> Stok Menipis</h2>
            @forelse ($lowStock as $p)
                <div class="d-flex justify-content-between border-bottom py-2 small"><span>{{ $p->name }}</span><span class="badge {{ $p->stock == 0 ? 'text-bg-danger' : 'text-bg-warning' }}">{{ $p->stock }} {{ $p->unit }}</span></div>
            @empty
                <p class="small text-muted mb-0">Semua stok aman.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
