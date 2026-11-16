<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('filter_stok')) {
            if ($request->filter_stok === 'menipis') {
                $query->whereRaw('stok <= stok_minimum');
            } elseif ($request->filter_stok === 'tersedia') {
                $query->whereRaw('stok > stok_minimum');
            }
        }

        $products = $query->orderBy('nama_produk', 'asc')->paginate(10)->appends($request->all());
        $categories = Category::orderBy('nama_kategori', 'asc')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        // Generate auto kode produk
        $lastProduct = Product::latest('id')->first();
        $nextNum = $lastProduct ? ($lastProduct->id + 1) : 1;
        $defaultKode = 'GNT-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('products.create', compact('categories', 'defaultKode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_produk' => 'required|unique:products,kode_produk',
            'nama_produk' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk genteng berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('nama_kategori', 'asc')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'kode_produk' => 'required|unique:products,kode_produk,' . $product->id,
            'nama_produk' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Data genteng berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk genteng berhasil dihapus!');
    }

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            'tipe_penyesuaian' => 'required|in:tambah,kurang,set',
            'jumlah' => 'required|integer|min:0',
            'alasan' => 'required|string|max:255',
        ]);

        $jumlah = (int)$request->jumlah;
        $oldStock = $product->stok;

        if ($request->tipe_penyesuaian === 'tambah') {
            $product->stok += $jumlah;
        } elseif ($request->tipe_penyesuaian === 'kurang') {
            $product->stok = max(0, $product->stok - $jumlah);
        } else {
            $product->stok = $jumlah;
        }

        $product->save();

        return redirect()->back()->with('success', "Stok {$product->nama_produk} berhasil disesuaikan dari {$oldStock} menjadi {$product->stok} {$product->satuan}. Alasan: {$request->alasan}");
    }
}
