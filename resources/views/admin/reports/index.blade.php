@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Laporan Penjualan</h1>

    <!-- Filter -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <!-- Period Selector -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Periode</label>
                <select name="period" id="periodSelect" onchange="toggleInputs()"
                    class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline w-40">
                    <option value="day" {{ $period === 'day' ? 'selected' : '' }}>Harian</option>
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Mingguan</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulanan</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>

            <!-- Date Input (Day/Week) -->
            <div id="dateInputGroup">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ $date }}"
                    class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
            </div>

            <!-- Month Input -->
            <div id="monthInputGroup" class="hidden">
                <label class="block text-gray-700 text-sm font-bold mb-2">Bulan</label>
                <input type="month" name="month" value="{{ $month }}"
                    class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
            </div>

            <!-- Year Input -->
            <div id="yearInputGroup" class="hidden">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun</label>
                <select name="year"
                    class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline w-32">
                    @for($y = date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Payment Filter -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Metode</label>
                <select name="payment_method" class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline w-32">
                    <option value="">Semua</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="bon" {{ request('payment_method') === 'bon' ? 'selected' : '' }}>Bon</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <select name="status" class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline w-32">
                    <option value="">Semua</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded h-10">
                Filter
            </button>
            
            <a href="{{ route('reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded h-10 flex items-center justify-center">
                Reset
            </a>
        </form>
    </div>

    <script>
        function toggleInputs() {
            const period = document.getElementById('periodSelect').value;
            const dateGroup = document.getElementById('dateInputGroup');
            const monthGroup = document.getElementById('monthInputGroup');
            const yearGroup = document.getElementById('yearInputGroup');

            // Reset visibility
            dateGroup.classList.add('hidden');
            monthGroup.classList.add('hidden');
            yearGroup.classList.add('hidden');

            if (period === 'day' || period === 'week') {
                dateGroup.classList.remove('hidden');
            } else if (period === 'month') {
                monthGroup.classList.remove('hidden');
            } else if (period === 'year') {
                yearGroup.classList.remove('hidden');
            }
        }

        // Init on load
        toggleInputs();
    </script>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-green-100 p-6 rounded shadow border-l-4 border-green-500">
            <h3 class="text-gray-700 font-bold uppercase text-sm">Total Pendapatan ({{ $label }})</h3>
            <p class="text-3xl font-bold text-green-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-blue-100 p-6 rounded shadow border-l-4 border-blue-500">
            <h3 class="text-gray-700 font-bold uppercase text-sm">Total Transaksi ({{ $label }})</h3>
            <p class="text-3xl font-bold text-blue-700">{{ $totalTransactions }}</p>
        </div>
        <div class="bg-yellow-100 p-6 rounded shadow border-l-4 border-yellow-500">
            <h3 class="text-gray-700 font-bold uppercase text-sm">Total Cash</h3>
            <p class="text-xl font-bold text-yellow-700">Rp {{ number_format($totalCash, 0, ',', '.') }}</p>
        </div>
        <div class="bg-purple-100 p-6 rounded shadow border-l-4 border-purple-500">
            <h3 class="text-gray-700 font-bold uppercase text-sm">Total QRIS</h3>
            <p class="text-xl font-bold text-purple-700">Rp {{ number_format($totalQris, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead class="bg-gray-200">
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Waktu</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            ID Transaksi</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Kasir</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Metode</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Total</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Status</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[200px]">
                            Items</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center whitespace-nowrap">
                                {{ $trx->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center whitespace-nowrap">
                                #{{ $trx->id }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center whitespace-nowrap">
                                {{ $trx->kasir->username }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center whitespace-nowrap">
                                @php
                                    $method = strtolower(trim($trx->payment_method));
                                    $badges = [
                                        'cash' => 'bg-green-100 text-green-800',
                                        'qris' => 'bg-purple-100 text-purple-800',
                                        'bon'  => 'bg-orange-100 text-orange-800',
                                    ];
                                    $badgeClass = $badges[$method] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                    {{ strtoupper($method) }}
                                </span>
                            </td>
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center font-bold whitespace-nowrap">
                                Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center whitespace-nowrap">
                                @if($trx->status === 'paid')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                <ul class="list-disc list-inside text-xs text-gray-600 inline-block text-left">
                                    @foreach($trx->items as $item)
                                        <li>{{ $item->product ? $item->product->name : 'Deleted Product' }} (x{{ $item->qty }})</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-5 bg-white text-sm text-center">Tidak ada transaksi pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
@endsection