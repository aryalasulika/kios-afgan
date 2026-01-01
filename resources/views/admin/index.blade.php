@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded shadow-md border-l-4 border-blue-500">
            <div class="text-gray-500 text-sm font-bold uppercase mb-1">Penjualan Hari Ini</div>
            <div class="text-2xl font-bold text-gray-800">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white p-6 rounded shadow-md border-l-4 border-green-500">
            <div class="text-gray-500 text-sm font-bold uppercase mb-1">Transaksi Hari Ini</div>
            <div class="text-2xl font-bold text-gray-800">{{ $todayTransactions }}</div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded shadow-md border-l-4 border-purple-500">
            <div class="text-gray-500 text-sm font-bold uppercase mb-1">Total Produk</div>
            <div class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white p-6 rounded shadow-md border-l-4 border-red-500">
            <div class="text-gray-500 text-sm font-bold uppercase mb-1">Stok Menipis (<= 5)</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $lowStock }}</div>
            </div>
        </div>
@endsection