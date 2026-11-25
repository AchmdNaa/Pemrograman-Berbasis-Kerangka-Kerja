<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $from = $request->date('dari') ?? today()->subDays(6);
        $to = $request->date('sampai') ?? today();

        $query = Transaction::with('cashier')->withCount('items')
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->when($request->query('q'), fn ($q, $s) => $q->where('invoice_no', 'like', "%{$s}%"))
            ->when($request->query('metode'), fn ($q, $m) => $q->where('payment_method', $m))
            // Kasir hanya melihat transaksinya sendiri; admin bisa memfilter per kasir
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when($user->isAdmin() && $request->query('kasir'), fn ($q) => $q->where('user_id', $request->query('kasir')));

        return view('transactions.index', [
            'transactions' => (clone $query)->latest()->paginate(15)->withQueryString(),
            'sum'          => (int) (clone $query)->sum('total'),
            'count'        => (clone $query)->count(),
            'cashiers'     => $user->isAdmin() ? User::orderBy('name')->get() : collect(),
            'from'         => $from,
            'to'           => $to,
        ]);
    }

    /** Detail / struk transaksi (bisa dicetak). */
    public function show(Request $request, Transaction $transaction): View
    {
        abort_if(! $request->user()->isAdmin() && $transaction->user_id !== $request->user()->id, 404);

        return view('transactions.show', ['trx' => $transaction->load('items', 'cashier')]);
    }
}
