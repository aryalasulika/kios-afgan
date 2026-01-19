<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Transaction::with('kasir', 'items')->latest();
        $label = "Semua Transaksi";

        // Date Range Filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
            $label = \Carbon\Carbon::parse($request->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($request->end_date)->format('d M Y');
        } elseif ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
            $label = 'Sejak ' . \Carbon\Carbon::parse($request->start_date)->format('d M Y');
        } elseif ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
            $label = 'Sampai ' . \Carbon\Carbon::parse($request->end_date)->format('d M Y');
        }

        // Filters
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Clone for totals
        $totalsQuery = clone $query;

        // Paginate main query
        $transactions = $query->paginate(20);

        // Calculate totals from the full result set (not just the page)
        // Optimally:
        $stats = $totalsQuery->selectRaw('
            count(*) as count, 
            sum(case when status = "paid" then total_amount else 0 end) as revenue, 
            sum(case when payment_method = "cash" or settlement_method = "cash" then total_amount else 0 end) as cash, 
            sum(case when payment_method = "qris" or settlement_method = "qris" then total_amount else 0 end) as qris,
            sum(case when status = "unpaid" then total_amount else 0 end) as unpaid
        ')->first();

        $totalRevenue = $stats->revenue ?? 0;
        $totalTransactions = $stats->count ?? 0;
        $totalCash = $stats->cash ?? 0;
        $totalQris = $stats->qris ?? 0;
        $totalUnpaid = $stats->unpaid ?? 0;

        return view('admin.reports.index', compact(
            'transactions',
            'totalRevenue',
            'totalTransactions',
            'totalCash',
            'totalQris',
            'totalUnpaid',
            'label'
        ));
    }
}
