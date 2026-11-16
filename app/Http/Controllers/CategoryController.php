<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('nama_kategori', 'asc')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $slug = Str::slug($request->nama_kategori);
        $count = Category::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        Category::create([
            'nama_kategori' => $request->nama_kategori,
            'slug' => $slug,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori genteng berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $category->update([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori genteng berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Kategori ini tidak dapat dihapus karena masih memiliki produk genteng terkait.');
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategori genteng berhasil dihapus!');
    }
}
