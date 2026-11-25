@extends('layouts.app')
@section('title', 'Kasir')
@push('styles')
<style>
    .prod { cursor: pointer; transition: .1s; border: 2px solid transparent !important; }
    .prod:hover { border-color: #a5b4fc !important; transform: translateY(-1px); }
    .prod.habis { opacity: .45; cursor: not-allowed; }
    .prod .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .cart { position: sticky; top: 1rem; }
    .cart-items { max-height: 340px; overflow-y: auto; }
    .qty-btn { width: 28px; height: 28px; padding: 0; line-height: 1; }
</style>
@endpush
@section('content')
@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
<div class="row g-4">
    <div class="col-xl-7">
        <div class="card p-3">
            <div class="d-flex gap-2 mb-3">
                <input type="search" id="search" class="form-control" placeholder="Cari nama / kode barang lalu Enter untuk menambah…" autofocus>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3" id="catFilter">
                <button class="btn btn-sm btn-primary" data-cat="">Semua</button>
                @foreach ($categories as $c)<button class="btn btn-sm btn-outline-secondary" data-cat="{{ $c->id }}"><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: {{ $c->color }}"></span>{{ $c->name }}</button>@endforeach
            </div>
            <div class="row g-2" id="grid">
                @foreach ($products as $p)
                    <div class="col-6 col-md-4 col-xxl-3 item" data-id="{{ $p->id }}" data-cat="{{ $p->category_id }}" data-search="{{ strtolower($p->name . ' ' . $p->sku) }}">
                        <div class="card prod h-100 p-2 {{ $p->stock <= 0 ? 'habis' : '' }}">
                            <div class="small text-muted"><span class="dot" style="background: {{ $p->category->color }}"></span> {{ $p->sku }}</div>
                            <div class="fw-semibold small lh-sm my-1" style="min-height: 2.4em">{{ $p->name }}</div>
                            <div class="d-flex justify-content-between align-items-end">
                                <span class="fw-bold text-primary">{{ rupiah($p->price) }}</span>
                                <span class="small {{ $p->stock <= 5 ? 'text-danger' : 'text-muted' }}">stok {{ $p->stock }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <form action="{{ route('pos.store') }}" method="POST" id="checkout" class="card p-3 cart">
            @csrf
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 fw-bold mb-0"><i class="bi bi-cart3"></i> Keranjang</h2>
                <button type="button" class="btn btn-sm btn-link text-danger" id="clearCart">Kosongkan</button>
            </div>
            <div class="cart-items" id="cartItems"></div>
            <div id="hiddenItems"></div>
            <hr>
            <div class="d-flex justify-content-between small"><span>Subtotal</span><span id="subtotal">Rp0</span></div>
            <div class="d-flex justify-content-between align-items-center small my-1">
                <label for="discount">Diskon (Rp)</label>
                <input type="number" min="0" name="discount" id="discount" value="{{ old('discount', 0) }}" class="form-control form-control-sm text-end" style="width: 130px">
            </div>
            <div class="d-flex justify-content-between fs-4 fw-bold"><span>Total</span><span id="total" class="text-primary">Rp0</span></div>
            <div class="btn-group w-100 my-2" role="group">
                @foreach (\App\Models\Transaction::METHODS as $k => $l)
                    <input type="radio" class="btn-check" name="payment_method" id="pm-{{ $k }}" value="{{ $k }}" @checked(old('payment_method', 'tunai') === $k)>
                    <label class="btn btn-outline-primary btn-sm" for="pm-{{ $k }}">{{ $l }}</label>
                @endforeach
            </div>
            <div id="cashBox">
                <label class="form-label small mb-1" for="paid">Uang diterima</label>
                <input type="number" min="0" name="paid" id="paid" value="{{ old('paid') }}" class="form-control form-control-lg text-end" placeholder="0">
                <div class="d-flex flex-wrap gap-1 mt-2" id="quickCash"></div>
                <div class="d-flex justify-content-between mt-2"><span>Kembalian</span><span id="change" class="fw-bold">Rp0</span></div>
            </div>
            <input type="text" name="note" class="form-control form-control-sm mt-2" placeholder="Catatan (opsional)" value="{{ old('note') }}">
            <button class="btn btn-primary btn-lg w-100 mt-3" id="payBtn" disabled><i class="bi bi-check2-circle"></i> Bayar &amp; Cetak Struk</button>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
(() => {
    const products = @json($catalog);
    const old = @json(old('items', []));
    const cart = new Map(old.map(i => [String(i.id), Number(i.qty)]));
    const $ = id => document.getElementById(id);
    const rp = n => 'Rp' + Math.max(0, n).toLocaleString('id-ID');

    function add(id, n = 1) {
        const p = products[id]; if (!p) return;
        const q = (cart.get(String(id)) || 0) + n;
        if (q > p.stock) { alert(`Stok ${p.name} hanya ${p.stock} ${p.unit}.`); return; }
        q <= 0 ? cart.delete(String(id)) : cart.set(String(id), q);
        render();
    }

    function render() {
        let subtotal = 0, html = '', hidden = '', i = 0;
        cart.forEach((qty, id) => {
            const p = products[id], line = p.price * qty; subtotal += line;
            html += `<div class="d-flex align-items-center gap-2 border-bottom py-2">
                <div class="flex-grow-1 small"><div class="fw-semibold">${p.name}</div><div class="text-muted">${rp(p.price)} × ${qty}</div></div>
                <button type="button" class="btn btn-outline-secondary qty-btn" data-dec="${id}">−</button>
                <span class="fw-semibold" style="width: 24px; text-align: center">${qty}</span>
                <button type="button" class="btn btn-outline-secondary qty-btn" data-inc="${id}">+</button>
                <span class="fw-semibold small text-end" style="width: 90px">${rp(line)}</span></div>`;
            hidden += `<input type="hidden" name="items[${i}][id]" value="${id}"><input type="hidden" name="items[${i}][qty]" value="${qty}">`;
            i++;
        });
        $('cartItems').innerHTML = html || '<p class="text-muted small text-center my-4">Klik produk untuk menambahkan ke keranjang.</p>';
        $('hiddenItems').innerHTML = hidden;
        const discount = Math.min(Number($('discount').value) || 0, subtotal);
        const total = subtotal - discount;
        $('subtotal').textContent = rp(subtotal);
        $('total').textContent = rp(total);

        const cash = document.querySelector('[name=payment_method]:checked').value === 'tunai';
        $('cashBox').style.display = cash ? '' : 'none';
        const paid = Number($('paid').value) || 0;
        $('change').textContent = rp(paid - total);
        $('change').className = 'fw-bold ' + (cash && paid < total ? 'text-danger' : 'text-success');
        $('payBtn').disabled = cart.size === 0 || (cash && paid < total);

        // Tombol uang pas & pecahan umum
        const opts = [...new Set([total, ...[20000, 50000, 100000].map(v => Math.ceil(total / v) * v)])].filter(v => v > 0).slice(0, 4);
        $('quickCash').innerHTML = opts.map(v => `<button type="button" class="btn btn-sm btn-light border" data-cash="${v}">${v === total ? 'Uang pas' : rp(v)}</button>`).join('');
    }

    document.querySelectorAll('#grid .item').forEach(el => el.addEventListener('click', () => add(el.dataset.id)));
    document.addEventListener('click', e => {
        const t = e.target.closest('[data-inc],[data-dec],[data-cash]'); if (!t) return;
        if (t.dataset.inc) add(t.dataset.inc, 1);
        if (t.dataset.dec) add(t.dataset.dec, -1);
        if (t.dataset.cash) { $('paid').value = t.dataset.cash; render(); }
    });
    ['discount', 'paid'].forEach(id => $(id).addEventListener('input', render));
    document.querySelectorAll('[name=payment_method]').forEach(r => r.addEventListener('change', render));
    $('clearCart').addEventListener('click', () => { cart.clear(); render(); });

    let activeCat = '';
    function filter() {
        const q = $('search').value.toLowerCase();
        document.querySelectorAll('#grid .item').forEach(el => {
            el.style.display = (!activeCat || el.dataset.cat === activeCat) && el.dataset.search.includes(q) ? '' : 'none';
        });
    }
    $('search').addEventListener('input', filter);
    $('search').addEventListener('keydown', e => {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        // Enter: tambahkan produk yang kodenya persis sama (scanner barcode) atau satu-satunya hasil pencarian
        const q = $('search').value.trim().toLowerCase();
        const exact = Object.values(products).find(p => p.sku.toLowerCase() === q);
        const visible = [...document.querySelectorAll('#grid .item')].filter(el => el.style.display !== 'none');
        const id = exact ? exact.id : (visible.length === 1 ? visible[0].dataset.id : null);
        if (id) { add(id); $('search').value = ''; filter(); }
    });
    document.querySelectorAll('#catFilter button').forEach(b => b.addEventListener('click', () => {
        activeCat = b.dataset.cat;
        document.querySelectorAll('#catFilter button').forEach(x => x.className = 'btn btn-sm ' + (x === b ? 'btn-primary' : 'btn-outline-secondary'));
        filter();
    }));
    render();
})();
</script>
@endpush
