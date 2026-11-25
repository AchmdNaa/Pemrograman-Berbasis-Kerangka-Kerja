<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = Transaction::whereDate('created_at', today());
        $isAdmin = auth()->user()->isAdmin();

        // Penjualan 7 hari terakhir (untuk grafik batang)
        $week = collect(range(6, 0))->map(function (int $d) {
            $date = Carbon::today()->subDays($d);

            return [
                'label' => $date->translatedFormat('D d/m'),
                'total' => (int) Transaction::whereDate('created_at', $date)->sum('total'),
            ];
        });

        $topProducts = TransactionItem::query()
            ->selectRaw('product_name, SUM(qty) AS qty, SUM(subtotal) AS omzet')
            ->whereHas('product')
            ->whereIn('transaction_id', Transaction::where('created_at', '>=', now()->subDays(30))->select('id'))
            ->groupBy('product_name')->orderByDesc('qty')->limit(5)->get();

        return view('dashboard', [
            'salesToday'   => (int) (clone $today)->sum('total'),
            'countToday'   => (clone $today)->count(),
            'profitToday'  => $isAdmin ? (int) TransactionItem::whereIn('transaction_id', (clone $today)->select('id'))->selectRaw('SUM((price - cost_price) * qty) AS p')->value('p') : null,
            'itemsToday'   => (int) TransactionItem::whereIn('transaction_id', (clone $today)->select('id'))->sum('qty'),
            'week'         => $week,
            'topProducts'  => $topProducts,
            'lowStock'     => Product::active()->lowStock()->orderBy('stock')->limit(6)->get(),
            'recent'       => Transaction::with('cashier')->latest()->limit(6)->get(),
        ]);
    }
}
