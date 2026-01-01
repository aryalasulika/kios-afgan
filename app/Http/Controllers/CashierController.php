<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CashierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cashiers = User::where('role', 'kasir')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.cashiers.index', compact('cashiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cashiers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'pin' => 'required|numeric|digits:4',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'pin' => Hash::make($request->pin),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        return redirect()->route('cashiers.index')
            ->with('success', 'Kasir berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cashier = User::findOrFail($id);

        // Prevent editing non-cashiers via this controller
        if ($cashier->role !== 'kasir') {
            abort(404);
        }

        return view('admin.cashiers.edit', compact('cashier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cashier = User::findOrFail($id);

        if ($cashier->role !== 'kasir') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($cashier->id),
            ],
        ]);

        $cashier->update([
            'name' => $request->name,
            'username' => $request->username,
        ]);

        return redirect()->route('cashiers.index')
            ->with('success', 'Data kasir berhasil diperbarui.');
    }

    /**
     * Toggle the active status of the cashier.
     */
    public function toggleStatus(string $id)
    {
        $cashier = User::findOrFail($id);

        if ($cashier->role !== 'kasir') {
            abort(404);
        }

        $cashier->is_active = !$cashier->is_active;
        $cashier->save();

        $status = $cashier->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kasir berhasil {$status}.");
    }

    /**
     * Reset the cashier's PIN.
     */
    public function resetPin(Request $request, string $id)
    {
        $cashier = User::findOrFail($id);

        if ($cashier->role !== 'kasir') {
            abort(404);
        }

        $request->validate([
            'pin' => 'required|numeric|digits:4',
        ]);

        $cashier->pin = Hash::make($request->pin);
        $cashier->save();

        return back()->with('success', 'PIN kasir berhasil direset.');
    }
}
