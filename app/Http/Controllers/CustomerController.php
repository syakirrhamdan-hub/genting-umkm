<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('transactions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('kode_pelanggan', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('nama', 'asc')->paginate(10)->appends($request->all());

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $lastCustomer = Customer::latest('id')->first();
        $nextNum = $lastCustomer ? ($lastCustomer->id + 1) : 1;
        $defaultKode = 'PLG-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('customers.create', compact('defaultKode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_pelanggan' => 'required|unique:customers,kode_pelanggan',
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil ditambahkan!');
    }

    public function show(Customer $customer)
    {
        $customer->load(['transactions' => function($q) {
            $q->latest()->with('details');
        }]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'kode_pelanggan' => 'required|unique:customers,kode_pelanggan,' . $customer->id,
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil dihapus!');
    }
}
