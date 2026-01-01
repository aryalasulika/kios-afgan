@extends('layouts.admin')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Bon (Piutang)</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <form action="{{ route('bon.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Quick Presets -->
            <div class="flex gap-2">
                <a href="{{ route('bon.index', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" 
                   class="px-3 py-2 text-sm font-medium bg-gray-200 hover:bg-gray-300 rounded text-gray-700">Hari Ini</a>
                <a href="{{ route('bon.index', ['start_date' => now()->startOfWeek()->format('Y-m-d'), 'end_date' => now()->endOfWeek()->format('Y-m-d')]) }}" 
                   class="px-3 py-2 text-sm font-medium bg-gray-200 hover:bg-gray-300 rounded text-gray-700">Minggu Ini</a>
                <a href="{{ route('bon.index', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')]) }}" 
                   class="px-3 py-2 text-sm font-medium bg-gray-200 hover:bg-gray-300 rounded text-gray-700">Bulan Ini</a>
            </div>

            <!-- Date Range -->
            <div class="flex flex-col sm:flex-row gap-2">
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline text-sm">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Sampai</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline text-sm">
                </div>
            </div>

            <!-- Search -->
            <div class="flex-1 w-full">
                <label class="block text-gray-700 text-xs font-bold mb-1">Cari Pelanggan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama Pelanggan..." 
                       class="shadow border rounded py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline w-full text-sm">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded h-10 text-sm">
                Filter
            </button>
            <a href="{{ route('bon.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded h-10 flex items-center justify-center text-sm">
                Reset
            </a>
        </form>
    </div>

    <!-- Stats (KPI) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-red-100 p-6 rounded shadow border-l-4 border-red-500">
            <h3 class="text-gray-700 font-bold uppercase text-sm">Total Bon</h3>
            <p class="text-3xl font-bold text-red-700">Rp {{ number_format($totalBon, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Tanggal
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Invoice
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Pelanggan
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Kasir
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Total
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bons as $bon)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                {{ $bon->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                #{{ $bon->id }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold whitespace-nowrap">
                                {{ $bon->customer_name ?? '-' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                {{ $bon->kasir->username ?? '-' }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right font-bold whitespace-nowrap">
                                Rp {{ number_format($bon->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                <button
                                    onclick="openSettleModal({{ $bon->id }}, '{{ $bon->customer_name }}', {{ $bon->total_amount }})"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded shadow transition text-xs">
                                    Pelunasan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                Tidak ada data bon yang belum lunas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
            {{ $bons->withQueryString()->links() }}
        </div>
    </div>

    <!-- Settle Modal -->
    <div id="settleModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 flex">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm m-auto">
            <h3 class="text-xl font-bold mb-2">Pelunasan Bon</h3>
            <p class="text-gray-600 mb-4">Pelanggan: <span id="modalCustomerName" class="font-bold"></span></p>
            <p class="text-gray-600 mb-6">Total: <span id="modalTotal" class="font-bold text-lg text-blue-600"></span></p>

            <form id="settleForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Metode Pembayaran</label>
                    <select name="payment_method"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeSettleModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Lunasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const settleModal = document.getElementById('settleModal');
        const settleForm = document.getElementById('settleForm');
        const modalCustomerName = document.getElementById('modalCustomerName');
        const modalTotal = document.getElementById('modalTotal');

        function openSettleModal(id, name, total) {
            settleForm.action = `/admin/bon/${id}/settle`;
            modalCustomerName.innerText = name || '-';
            modalTotal.innerText = 'Rp ' + parseInt(total).toLocaleString('id-ID');
            settleModal.classList.remove('hidden');
        }

        function closeSettleModal() {
            settleModal.classList.add('hidden');
        }
    </script>
@endsection