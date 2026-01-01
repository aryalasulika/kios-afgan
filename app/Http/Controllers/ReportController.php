<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'day');

        // Defaults
        $date = $request->input('date', date('Y-m-d'));
        $month = $request->input('month', date('Y-m'));
        $year = $request->input('year', date('Y'));

        $query = \App\Models\Transaction::with('kasir', 'items')->latest();
        $label = "";

        switch ($period) {
            case 'day':
                $query->whereDate('created_at', $date);
                $label = \Carbon\Carbon::parse($date)->format('d M Y');
                break;
            case 'week':
                $start = \Carbon\Carbon::parse($date)->startOfWeek();
                $end = \Carbon\Carbon::parse($date)->endOfWeek();
                $query->whereBetween('created_at', [$start, $end]);
                $label = $start->format('d M Y') . ' - ' . $end->format('d M Y');
                break;
            case 'month':
                // $month format YYYY-MM
                try {
                    $carbonDate = \Carbon\Carbon::createFromFormat('Y-m', $month);
                    $query->whereYear('created_at', $carbonDate->year)
                        ->whereMonth('created_at', $carbonDate->month);
                    $label = $carbonDate->format('F Y');
                } catch (\Exception $e) {
                    // Fallback if invalid format
                    $label = $month;
                }
                break;
            case 'year':
                $query->whereYear('created_at', $year);
                $label = $year;
                break;
            default:
                $query->whereDate('created_at', date('Y-m-d'));
                $label = date('d M Y');
                break;
        }

        // Clone for totals
        $totalsQuery = clone $query;

        // Paginate main query
        $transactions = $query->paginate(20);

        // Calculate totals from the full result set (not just the page)
        // For Cash/QRIS split, I also need to sum from the full query or a cloned query
        // Since I can't filter the already executed builder easily without cloning again or grouping.
        // Optimally:
        $stats = $totalsQuery->selectRaw('count(*) as count, sum(total_amount) as revenue, sum(case when payment_method = "cash" then total_amount else 0 end) as cash, sum(case when payment_method = "qris" then total_amount else 0 end) as qris')->first();

        $totalRevenue = $stats->revenue ?? 0;
        $totalTransactions = $stats->count ?? 0;
        $totalCash = $stats->cash ?? 0;
        $totalQris = $stats->qris ?? 0;

        return view('admin.reports.index', compact(
            'transactions',
            'totalRevenue',
            'totalTransactions',
            'totalCash',
            'totalQris',
            'period',
            'date',
            'month',
            'year',
            'label'
        ));
    }
}
