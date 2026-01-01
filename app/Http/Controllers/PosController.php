<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        return view('kasir.index');
    }

    public function searchProduct(Request $request)
    {
        $query = trim($request->input('query'));

        if (!$query) {
            return response()->json(['success' => false, 'message' => 'Input kosong'], 400);
        }

        // 1. Try exact barcode
        $product = \App\Models\Product::where('barcode', $query)
            ->where('is_active', true)
            ->first();

        if ($product) {
            return response()->json([
                'success' => true,
                'action' => 'add',
                'data' => $product
            ]);
        }

        // 2. If not found, try name (LIKE)
        $products = \App\Models\Product::where('name', 'like', '%' . $query . '%')
            ->where('is_active', true)
            ->limit(10) // Limit results to prevent overload
            ->get();

        if ($products->count() === 1) {
            return response()->json([
                'success' => true,
                'action' => 'add',
                'data' => $products->first()
            ]);
        }

        if ($products->count() > 1) {
            return response()->json([
                'success' => true,
                'action' => 'choose',
                'data' => $products
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'total' => 'required|numeric',
            'payment_method' => 'required',
            'customer_name' => 'nullable|string',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $isBon = $request->payment_method === 'bon';

            if ($isBon && empty($request->customer_name)) {
                return response()->json(['success' => false, 'message' => 'Nama pelanggan wajib diisi untuk Bon.'], 400);
            }

            $transaction = \App\Models\Transaction::create([
                'kasir_id' => auth('kasir')->id(),
                'total_amount' => $request->total,
                'payment_method' => $request->payment_method,
                'cash_received' => $isBon ? 0 : $request->cash_received,
                'change_amount' => $isBon ? 0 : ($request->change_amount ?? 0),
                'status' => $isBon ? 'unpaid' : 'paid',
                'customer_name' => $isBon ? $request->customer_name : null,
            ]);

            foreach ($request->items as $item) {
                $product = \App\Models\Product::find($item['id']);

                // Decrement stock
                $product->decrement('stock', $item['qty']);

                \App\Models\TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'qty' => $item['qty'],
                    'subtotal' => $product->price * $item['qty'],
                ]);
            }

            return response()->json(['success' => true, 'transaction_id' => $transaction->id]);
        });
    }
}
