<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Layar kasir (Point of Sale).
 * Keranjang dikelola di browser (JavaScript), lalu dikirim sekali saat pembayaran.
 * Server menghitung ulang semua harga & stok dari database: data dari browser tidak dipercaya.
 */
class PosController extends Controller
{
    public function index(): View
    {
        $products = Product::active()->with('category')->orderBy('name')->get();

        return view('pos.index', [
            'categories' => Category::orderBy('name')->get(),
            'products'   => $products,
            // Data ringkas untuk JavaScript keranjang (dikirim sebagai JSON)
            'catalog'    => $products->mapWithKeys(fn ($p) => [$p->id => $p->only(['id', 'sku', 'name', 'price', 'stock', 'unit'])]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items'          => ['required', 'array', 'min:1'],
            'items.*.id'     => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.qty'    => ['required', 'integer', 'min:1', 'max:999'],
            'discount'       => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', Rule::in(array_keys(Transaction::METHODS))],
            'paid'           => ['required', 'integer', 'min:0'],
            'note'           => ['nullable', 'string', 'max:200'],
        ], [
            'items.required' => 'Keranjang masih kosong.',
        ]);

        $transaction = DB::transaction(function () use ($data, $request) {
            // lockForUpdate: kunci baris produk agar stok tidak dipakai bersamaan oleh kasir lain
            $products = Product::whereIn('id', collect($data['items'])->pluck('id'))->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $lines = [];
            foreach ($data['items'] as $item) {
                $p = $products[$item['id']];
                if (! $p->is_active) {
                    throw ValidationException::withMessages(['items' => "Produk {$p->name} sudah tidak dijual."]);
                }
                if ($item['qty'] > $p->stock) {
                    throw ValidationException::withMessages(['items' => "Stok {$p->name} tidak cukup (tersisa {$p->stock} {$p->unit})."]);
                }
                $line = $p->price * $item['qty'];
                $subtotal += $line;
                $lines[] = ['product' => $p, 'qty' => $item['qty'], 'subtotal' => $line];
            }

            $discount = min((int) ($data['discount'] ?? 0), $subtotal);
            $total = $subtotal - $discount;
            // Non-tunai dibayar pas; tunai harus >= total
            $paid = $data['payment_method'] === 'tunai' ? (int) $data['paid'] : $total;
            if ($paid < $total) {
                throw ValidationException::withMessages(['paid' => 'Uang yang dibayarkan kurang dari total ' . rupiah($total) . '.']);
            }

            $trx = Transaction::create([
                'invoice_no'     => Transaction::nextInvoiceNo(),
                'user_id'        => $request->user()->id,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_method' => $data['payment_method'],
                'paid'           => $paid,
                'change'         => $paid - $total,
                'note'           => $data['note'] ?? null,
            ]);

            foreach ($lines as $l) {
                $trx->items()->create([
                    'product_id'   => $l['product']->id,
                    'product_name' => $l['product']->name,
                    'price'        => $l['product']->price,
                    'cost_price'   => $l['product']->cost_price,
                    'qty'          => $l['qty'],
                    'subtotal'     => $l['subtotal'],
                ]);
                $l['product']->decrement('stock', $l['qty']);
            }

            return $trx;
        });

        return redirect()->route('transactions.show', $transaction)->with('success', 'Transaksi berhasil. Kembalian: ' . rupiah($transaction->change));
    }
}
