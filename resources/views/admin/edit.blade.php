@extends('admin.layout')

@section('content')

<div class="admin-card">

    <h1 class="admin-title">
        ✏️ Edit Produk
    </h1>

    <form
        action="/admin/products/{{ $product->id }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ $product->name }}"
                   required>
        </div>

        <div class="form-group">
            <label>Berat Produk</label>
            <input type="text"
                   name="category"
                   class="form-control"
                   value="{{ $product->category }}"
                   placeholder="Contoh: 250 gram"
                   required>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description"
                      class="form-control"
                      required>{{ $product->description }}</textarea>
        </div>

        <div class="form-group">
            <label>Harga</label>
            <input type="number"
                   name="price"
                   class="form-control"
                   value="{{ $product->price }}"
                   required>
        </div>

        <div class="form-group">
            <label>Manajemen Stok</label>
            <input type="number"
                   name="stock"
                   class="form-control"
                   value="{{ $product->stock }}"
                   required>
        </div>

        <div class="form-group">
            <label>Ambil dari Stok Masuk Supplier (Opsional)</label>
            <select name="supplier_stock_id" class="form-control">
                <option value="">-- Tidak memotong stok supplier --</option>
                @foreach($supplierStocks as $s_stock)
                    <option value="{{ $s_stock->id }}">
                        {{ $s_stock->supplier->name ?? 'Unknown' }} | {{ $s_stock->item_name }} ({{ $s_stock->weight }}) - Sisa: {{ $s_stock->available_quantity }}
                    </option>
                @endforeach
            </select>
            <small style="color: #666; font-size: 0.85em; display: block; margin-top: 5px;">Jika dipilih dan ada <b>penambahan</b> stok di atas, maka penambahan tersebut akan memotong sisa ketersediaan dari supplier ini.</small>
        </div>

        <div class="form-group">
            <label>Tanggal Masuk Barang</label>
            <input type="date"
                   name="stock_entry_date"
                   class="form-control"
                   value="{{ $product->stock_entry_date ? $product->stock_entry_date->format('Y-m-d') : '' }}">
        </div>

        <div class="form-group">
            <label>Tanggal Expire</label>
            <input type="date"
                   name="expiry_date"
                   class="form-control"
                   value="{{ $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '' }}">
        </div>

        <div class="form-group">
            <label>Gambar</label>
            <input type="file"
                   name="image"
                   class="form-control">
        </div>

        @if($product->image_path)
            <div class="form-group">
                <label>Gambar Saat Ini</label>
                <div>
                    <img src="/{{ $product->image_path }}" width="120" alt="{{ $product->name }}">
                </div>
            </div>
        @endif

        <button type="submit" class="btn btn-success">
            Update Produk
        </button>
    </form>

</div>

@endsection