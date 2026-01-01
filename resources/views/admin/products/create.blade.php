@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Produk</h1>
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-800">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white p-8 rounded shadow-md w-full max-w-lg mx-auto">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                <input type="text" name="name"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Barcode (Opsional)</label>
                <input type="text" name="barcode"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                <input type="number" name="price"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Stok Awal</label>
                <input type="number" name="stock"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                Simpan Produk
            </button>
        </form>
    </div>
@endsection