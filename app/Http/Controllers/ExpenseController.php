<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use App\Models\Cashflow;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'supplier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_pengeluaran', 'like', "%{$search}%")
                  ->orWhere('nama_keperluan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $expenses = $query->latest('tanggal')->latest('id')->paginate(10)->appends($request->all());
        $categories = ExpenseCategory::orderBy('nama_kategori', 'asc')->get();
        $totalPengeluaran = (clone $query)->sum('nominal');

        return view('expenses.index', compact('expenses', 'categories', 'totalPengeluaran'));
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('nama_kategori', 'asc')->get();
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();

        // Generate No Pengeluaran EXP-YYYYMMDD-XXX
        $todayStr = Carbon::now()->format('Ymd');
        $lastExpToday = Expense::whereDate('created_at', Carbon::today())->latest('id')->first();
        $nextNum = $lastExpToday ? ((int)substr($lastExpToday->no_pengeluaran, -3) + 1) : 1;
        $defaultNoPengeluaran = 'EXP-' . $todayStr . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('expenses.create', compact('categories', 'suppliers', 'defaultNoPengeluaran'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pengeluaran' => 'required|unique:expenses,no_pengeluaran',
            'tanggal' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'nama_keperluan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'catatan' => 'nullable|string',
        ]);

        return DB::transaction(function() use ($validated) {
            $expense = Expense::create([
                'no_pengeluaran' => $validated['no_pengeluaran'],
                'tanggal' => $validated['tanggal'],
                'expense_category_id' => $validated['expense_category_id'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'nama_keperluan' => $validated['nama_keperluan'],
                'nominal' => $validated['nominal'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => auth()->id() ?? 1,
            ]);

            // Catat pengeluaran ke buku kas (cashflow keluar)
            $cat = ExpenseCategory::find($validated['expense_category_id']);
            $kategoriNama = $cat ? $cat->nama_kategori : 'Pengeluaran Toko';

            Cashflow::create([
                'tanggal' => $expense->tanggal,
                'jenis' => 'keluar',
                'kategori' => $kategoriNama,
                'nominal' => $expense->nominal,
                'referensi_tabel' => 'expenses',
                'referensi_id' => $expense->id,
                'keterangan' => "{$expense->no_pengeluaran}: {$expense->nama_keperluan}",
                'user_id' => auth()->id() ?? 1,
            ]);

            return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dicatat ke buku kas!');
        });
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'supplier', 'user']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::orderBy('nama_kategori', 'asc')->get();
        $suppliers = Supplier::orderBy('nama_supplier', 'asc')->get();

        return view('expenses.edit', compact('expense', 'categories', 'suppliers'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'no_pengeluaran' => 'required|unique:expenses,no_pengeluaran,' . $expense->id,
            'tanggal' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'nama_keperluan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'catatan' => 'nullable|string',
        ]);

        return DB::transaction(function() use ($validated, $expense) {
            $expense->update([
                'no_pengeluaran' => $validated['no_pengeluaran'],
                'tanggal' => $validated['tanggal'],
                'expense_category_id' => $validated['expense_category_id'],
                'supplier_id' => $validated['supplier_id'] ?? null,
                'nama_keperluan' => $validated['nama_keperluan'],
                'nominal' => $validated['nominal'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            $cat = ExpenseCategory::find($validated['expense_category_id']);
            $kategoriNama = $cat ? $cat->nama_kategori : 'Pengeluaran Toko';

            Cashflow::updateOrCreate(
                ['referensi_tabel' => 'expenses', 'referensi_id' => $expense->id],
                [
                    'tanggal' => $expense->tanggal,
                    'jenis' => 'keluar',
                    'kategori' => $kategoriNama,
                    'nominal' => $expense->nominal,
                    'keterangan' => "{$expense->no_pengeluaran}: {$expense->nama_keperluan}",
                    'user_id' => auth()->id() ?? 1,
                ]
            );

            return redirect()->route('expenses.index')->with('success', 'Data pengeluaran berhasil diperbarui dan buku kas disesuaikan!');
        });
    }

    public function destroy(Expense $expense)
    {
        return DB::transaction(function() use ($expense) {
            Cashflow::where('referensi_tabel', 'expenses')->where('referensi_id', $expense->id)->delete();
            $expense->delete();

            return redirect()->route('expenses.index')->with('success', 'Data pengeluaran berhasil dihapus dan buku kas disesuaikan.');
        });
    }
}
