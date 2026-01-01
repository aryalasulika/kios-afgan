@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Kasir</h1>
        <a href="{{ route('cashiers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
            + Tambah Kasir
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-center border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4 border-b font-semibold text-gray-700">Nama</th>
                    <th class="p-4 border-b font-semibold text-gray-700">Username</th>
                    <th class="p-4 border-b font-semibold text-gray-700">Status</th>
                    <th class="p-4 border-b font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($cashiers as $cashier)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">{{ $cashier->name ?? '-' }}</td>
                        <td class="p-4">{{ $cashier->username }}</td>
                        <td class="p-4">
                            @if($cashier->is_active)
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Aktif</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Non-Aktif</span>
                            @endif
                        </td>

                        <td class="p-4 flex gap-4 justify-center items-center">
                            <!-- Edit Action -->
                            <a href="{{ route('cashiers.edit', $cashier->id) }}" class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            
                            <!-- Toggle Status Action -->
                            <form action="{{ route('cashiers.toggle-status', $cashier->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status kasir ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-{{ $cashier->is_active ? 'red' : 'green' }}-500 hover:text-{{ $cashier->is_active ? 'red' : 'green' }}-700 transition" title="{{ $cashier->is_active ? 'Non-aktifkan' : 'Aktifkan' }}">
                                    @if($cashier->is_active)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    @endif
                                </button>
                            </form>

                            <!-- Reset PIN Action -->
                            <button onclick="document.getElementById('resetPinModal-{{ $cashier->id }}').classList.remove('hidden')" class="text-yellow-500 hover:text-yellow-700 transition" title="Reset PIN">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </button>

                            <!-- Reset PIN Modal -->
                            <div id="resetPinModal-{{ $cashier->id }}" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50 flex">
                                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm m-auto text-left">
                                    <h3 class="text-lg font-bold mb-4 text-center">Reset PIN: {{ $cashier->username }}</h3>
                                    <form action="{{ route('cashiers.reset-pin', $cashier->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-4">
                                            <label class="block text-gray-700 text-sm font-bold mb-2 text-center">PIN Baru (4 Digit)</label>
                                            <input type="text" name="pin" maxlength="4" pattern="\d{4}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-center text-2xl tracking-widest" required>
                                        </div>
                                        <div class="flex justify-center gap-2">
                                            <button type="button" onclick="document.getElementById('resetPinModal-{{ $cashier->id }}').classList.add('hidden')" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                                Batal
                                            </button>
                                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cashiers->links() }}
    </div>
@endsection
