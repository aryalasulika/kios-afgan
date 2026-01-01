<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $today = now()->today();

        $todaySales = \App\Models\Transaction::whereDate('created_at', $today)->sum('total_amount');
        $todayTransactions = \App\Models\Transaction::whereDate('created_at', $today)->count();
        $totalProducts = \App\Models\Product::count();
        $lowStock = \App\Models\Product::where('stock', '<=', 5)->count();

        return view('admin.index', compact('todaySales', 'todayTransactions', 'totalProducts', 'lowStock'));
    }
}
