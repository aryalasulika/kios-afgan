@extends('layouts.admin')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Produk</h1>

        <div class="flex items-center gap-4 w-full md:w-auto">
            <form action="{{ route('products.index') }}" method="GET" class="w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Produk..."
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </form>
            <a href="{{ route('products.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded whitespace-nowrap">
                + Tambah
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-center">Nama Produk</th>
                        <th class="py-3 px-6 text-center">Barcode</th>
                        <th class="py-3 px-6 text-center">Harga</th>
                        <th class="py-3 px-6 text-center">Stok</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($products as $product)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-center whitespace-nowrap">{{ $product->name }}</td>
                            <td class="py-3 px-6 text-center font-mono">{{ $product->barcode ?? '-' }}</td>
                            <td class="py-3 px-6 text-center whitespace-nowrap">Rp
                                {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span
                                    class="{{ $product->stock <= 5 ? 'bg-red-200 text-red-600 py-1 px-3 rounded-full text-xs' : 'text-gray-600' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center whitespace-nowrap">
                                <div class="flex item-center justify-center">
                                    <a href="{{ route('products.edit', $product->id) }}"
                                        class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-4 mr-2 transform hover:text-red-500 hover:scale-110">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $products->withQueryString()->links() }}
    </div>
@endsection