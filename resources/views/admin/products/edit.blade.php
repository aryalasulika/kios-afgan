@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Produk</h1>
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-800">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white p-8 rounded shadow-md w-full max-w-lg mx-auto">
        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                <input type="text" name="name" value="{{ $product->name }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Barcode (Opsional)</label>
                <input type="text" name="barcode" value="{{ $product->barcode }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                <input type="number" name="price" value="{{ $product->price }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                <input type="number" name="stock" value="{{ $product->stock }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                Update Produk
            </button>
        </form>
    </div>
@endsection