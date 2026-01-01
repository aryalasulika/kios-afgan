<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Product::latest();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'barcode' => 'nullable|unique:products,barcode',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        \App\Models\Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(\App\Models\Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, \App\Models\Product $product)
    {
        $request->validate([
            'name' => 'required',
            'barcode' => 'nullable|unique:products,barcode,' . $product->id,
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(\App\Models\Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }
}
