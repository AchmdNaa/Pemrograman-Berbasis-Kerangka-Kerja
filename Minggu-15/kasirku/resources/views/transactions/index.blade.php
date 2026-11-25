@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('content')
<form class="card p-3 mb-3 row g-2 flex-row align-items-end" method="GET">
    <div class="col-md-2"><label class="form-label small mb-1">Dari</label><input type="date" name="dari" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-sm"></div>
    <div class="col-md-2"><label class="form-label small mb-1">Sampai</label><input type="date" name="sampai" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-sm"></div>
    <div class="col-md-3"><label class="form-label small mb-1">No. nota</label><input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="INV-…"></div>
    <div class="col-md-2"><label class="form-label small mb-1">Metode</label><select name="metode" class="form-select form-select-sm"><option value="">Semua</option>@foreach (\App\Models\Transaction::METHODS as $k => $l)<option value="{{ $k }}" @selected(request('metode') === $k)>{{ $l }}</option>@endforeach</select></div>
    @if (auth()->user()->isAdmin())
        <div class="col-md-2"><label class="form-label small mb-1">Kasir</label><select name="kasir" class="form-select form-select-sm"><option value="">Semua</option>@foreach ($cashiers as $c)<option value="{{ $c->id }}" @selected(request('kasir') == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
    @endif
    <div class="col-md-1"><button class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel"></i></button></div>
</form>
<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="card stat p-3"><div class="small text-muted">Jumlah transaksi</div><div class="num">{{ $count }}</div></div></div>
    <div class="col-md-6"><div class="card stat p-3" style="--c: #10b981"><div class="small text-muted">Total penjualan</div><div class="num">{{ rupiah($sum) }}</div></div></div>
</div>
<div class="card p-3">
    <table class="table align-middle small">
        <thead class="table-light"><tr><th>No. Nota</th><th>Waktu</th><th>Kasir</th><th class="text-center">Item</th><th>Metode</th><th class="text-end">Total</th><th></th></tr></thead>
        @forelse ($transactions as $t)
            <tr><td class="font-monospace fw-semibold">{{ $t->invoice_no }}</td><td>{{ $t->created_at->format('d/m/Y H:i') }}</td><td>{{ $t->cashier->name }}</td><td class="text-center">{{ $t->items_count }}</td>
                <td><span class="badge text-bg-light border">{{ \App\Models\Transaction::METHODS[$t->payment_method] }}</span></td><td class="text-end fw-semibold">{{ rupiah($t->total) }}</td>
                <td class="text-end"><a href="{{ route('transactions.show', $t) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-receipt"></i> Struk</a></td></tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi pada periode ini.</td></tr>
        @endforelse
    </table>
    {{ $transactions->links() }}
</div>
@endsection
