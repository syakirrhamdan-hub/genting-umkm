<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::with('expenses');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('nama_kontak', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('nama_supplier', 'asc')->paginate(10)->appends($request->all());

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $lastSupplier = Supplier::latest('id')->first();
        $nextNum = $lastSupplier ? ($lastSupplier->id + 1) : 1;
        $defaultKode = 'SPL-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('suppliers.create', compact('defaultKode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_supplier' => 'required|unique:suppliers,kode_supplier',
            'nama_supplier' => 'required|string|max:255',
            'nama_kontak' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Data pemasok/pengrajin genteng berhasil ditambahkan!');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'kode_supplier' => 'required|unique:suppliers,kode_supplier,' . $supplier->id,
            'nama_supplier' => 'required|string|max:255',
            'nama_kontak' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Data pemasok berhasil diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Data pemasok berhasil dihapus!');
    }
}
