<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')->orderBy('nama_kategori', 'asc')->get();
        return view('expense_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        ExpenseCategory::create([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('expense-categories.index')->with('success', 'Kategori pengeluaran berhasil ditambahkan!');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $expenseCategory->update([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('expense-categories.index')->with('success', 'Kategori pengeluaran berhasil diperbarui!');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()->route('expense-categories.index')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan pada data pengeluaran.');
        }

        $expenseCategory->delete();
        return redirect()->route('expense-categories.index')->with('success', 'Kategori pengeluaran berhasil dihapus!');
    }
}
