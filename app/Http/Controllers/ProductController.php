<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function api()
    {
        $products = Product::where('is_active', 1)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function index()
    {
    $products = Product::latest()->get();

    return view('admin.products', compact('products'));
    }

    public function create()
    {
    return view('admin.create');
    }
    
    public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required',
        'description' => 'required',
        'price' => 'required|numeric',
        'stock' => 'required|numeric',
        'category' => 'required',
        'image' => 'nullable|image'
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {

        $filename = time().'_'.$request->image->getClientOriginalName();

        $request->image->move(
            public_path('images/products'),
            $filename
        );

        $imagePath = 'images/products/'.$filename;
    }

    Product::create([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock,
        'category' => $request->category,
        'image_path' => $imagePath,
        'is_active' => 1
    ]);

    return redirect('/admin/products')
        ->with('success','Produk berhasil ditambahkan');
}
    public function edit($id)
{
    $product = Product::findOrFail($id);

    return view('admin.edit', compact('product'));
}

public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $imagePath = $product->image_path;

    if ($request->hasFile('image')) {

        $filename = time().'_'.$request->image->getClientOriginalName();

        $request->image->move(
            public_path('images/products'),
            $filename
        );

        $imagePath = 'images/products/'.$filename;
    }

    $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock,
        'category' => $request->category,
        'image_path' => $imagePath
    ]);

    return redirect('/admin/products')
        ->with('success','Produk berhasil diupdate');
}
public function destroy($id)
{
    $product = Product::findOrFail($id);

    $product->delete();

    return redirect('/admin/products')
        ->with('success','Produk berhasil dihapus');
}

}