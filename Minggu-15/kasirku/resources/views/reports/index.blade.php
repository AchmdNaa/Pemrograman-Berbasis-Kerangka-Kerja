@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('content')
<form class="card p-3 mb-3 d-flex flex-row flex-wrap gap-2 align-items-end d-print-none" method="GET">
    <div><label class="form-label small mb-1">Dari</label><input type="date" name="dari" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-sm"></div>
    <div><label class="form-label small mb-1">Sampai</label><input type="date" name="sampai" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-sm"></div>
    <button class="btn btn-sm btn-primary">Tampilkan</button>
    <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
</form>
<p class="small text-muted">Periode {{ $from->translatedFormat('d F Y') }} – {{ $to->translatedFormat('d F Y') }}</p>
<div class="row g-3 mb-4">
    @foreach ([['Omzet', rupiah($omzet), '#4f46e5'], ['Transaksi', $count, '#0ea5e9'], ['Diskon diberikan', rupiah($discount), '#f59e0b'], ['Laba kotor (setelah diskon)', rupiah($profit), '#10b981']] as [$l, $n, $c])
        <div class="col-sm-6 col-xl-3"><div class="card stat p-3 h-100" style="--c: {{ $c }}"><div class="small text-muted">{{ $l }}</div><div class="num">{{ $n }}</div></div></div>
    @endforeach
</div>
<div class="row g-4">
    <div class="col-xl-7"><div class="card p-3">
        <h2 class="h6 fw-bold">Penjualan per Produk</h2>
        <table class="table table-sm small mb-0">
            <thead><tr><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Omzet</th><th class="text-end">Laba</th></tr></thead>
            @forelse ($byProduct as $p)<tr><td>{{ $p->product_name }}</td><td class="text-center">{{ $p->qty }}</td><td class="text-end">{{ rupiah($p->omzet) }}</td><td class="text-end text-success">{{ rupiah($p->laba) }}</td></tr>
            @empty<tr><td colspan="4" class="text-center text-muted py-3">Belum ada penjualan.</td></tr>@endforelse
        </table>
    </div></div>
    <div class="col-xl-5">
        <div class="card p-3 mb-4">
            <h2 class="h6 fw-bold">Harian</h2>
            <table class="table table-sm small mb-0"><thead><tr><th>Tanggal</th><th class="text-center">Transaksi</th><th class="text-end">Omzet</th></tr></thead>
                @foreach ($daily as $d)<tr><td>{{ \Illuminate\Support\Carbon::parse($d->tanggal)->translatedFormat('D, d M') }}</td><td class="text-center">{{ $d->jumlah }}</td><td class="text-end">{{ rupiah($d->omzet) }}</td></tr>@endforeach
            </table>
        </div>
        <div class="card p-3">
            <h2 class="h6 fw-bold">Metode Pembayaran</h2>
            @foreach ($byMethod as $m)<div class="d-flex justify-content-between border-bottom py-1 small"><span>{{ \App\Models\Transaction::METHODS[$m->payment_method] }} ({{ $m->jumlah }})</span><span class="fw-semibold">{{ rupiah($m->omzet) }}</span></div>@endforeach
        </div>
    </div>
</div>
@endsection
