<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class BonController extends Controller
{
    /**
     * Display a listing of unpaid transactions (Bon).
     */
    public function index(Request $request)
    {
        $query = Transaction::with('kasir')
            ->where('status', 'unpaid');

        // Date Filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search Filter (Customer Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('customer_name', 'like', "%{$search}%");
        }

        // Clone query for KPI calculation (before pagination)
        $totalBon = (clone $query)->sum('total_amount');

        $bons = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.bon.index', compact('bons', 'totalBon'));
    }

    /**
     * Settle (pay) a specific bon transaction.
     */
    public function settle(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status !== 'unpaid') {
            return back()->with('error', 'Transaksi ini sudah lunas.');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,qris',
        ]);

        $transaction->update([
            'status' => 'paid',
            'settlement_method' => $request->payment_method,
            'settlement_at' => now(),
        ]);

        return back()->with('success', 'Bon berhasil dilunasi.');
    }
}
