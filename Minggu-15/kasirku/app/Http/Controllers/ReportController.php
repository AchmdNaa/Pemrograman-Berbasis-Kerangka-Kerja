<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Laporan penjualan & laba (khusus admin).
 */
class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = ($request->date('dari') ?? today()->startOfMonth())->startOfDay();
        $to = ($request->date('sampai') ?? today())->endOfDay();

        $trx = Transaction::whereBetween('created_at', [$from, $to]);
        $items = TransactionItem::whereIn('transaction_id', (clone $trx)->select('id'));

        $daily = (clone $trx)->selectRaw('DATE(created_at) AS tanggal, COUNT(*) AS jumlah, SUM(total) AS omzet')
            ->groupBy(DB::raw('DATE(created_at)'))->orderBy('tanggal')->get();

        return view('reports.index', [
            'from'       => $from,
            'to'         => $to,
            'omzet'      => (int) (clone $trx)->sum('total'),
            'discount'   => (int) (clone $trx)->sum('discount'),
            'count'      => (clone $trx)->count(),
            'profit'     => (int) (clone $items)->selectRaw('SUM((price - cost_price) * qty) AS p')->value('p') - (int) (clone $trx)->sum('discount'),
            'daily'      => $daily,
            'byProduct'  => (clone $items)->selectRaw('product_name, SUM(qty) AS qty, SUM(subtotal) AS omzet, SUM((price - cost_price) * qty) AS laba')
                ->groupBy('product_name')->orderByDesc('omzet')->get(),
            'byMethod'   => (clone $trx)->selectRaw('payment_method, COUNT(*) AS jumlah, SUM(total) AS omzet')->groupBy('payment_method')->get(),
        ]);
    }
}
