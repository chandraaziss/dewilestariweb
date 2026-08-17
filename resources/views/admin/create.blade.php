@extends('admin.layout')

@section('content')

<div class="admin-card">

<h1 class="admin-title">
    ➕ Tambah Produk
</h1>

<form action="/admin/products/store"
      method="POST"
      enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text"
               name="name"
               class="form-control"
               required>
    </div>

    <div class="form-group">
        <label>Berat Produk</label>
        <input type="text"
               name="category"
               class="form-control"
               placeholder="Contoh: 250 gram">
    </div>

    <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="description"
                  class="form-control"></textarea>
    </div>

    <div class="form-group">
        <label>Harga</label>
        <input type="number"
               name="price"
               class="form-control"
               required>
    </div>

    <div class="form-group">
        <label>Manajemen Stok</label>
        <input type="number"
               name="stock"
               class="form-control"
               placeholder="Jumlah stok (pcs)"
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
        <small style="color: #666; font-size: 0.85em; display: block; margin-top: 5px;">Jika dipilih, stok yang Anda masukkan di atas akan memotong sisa ketersediaan dari supplier ini.</small>
    </div>

    <div class="form-group">
        <label>Tanggal Masuk Barang</label>
        <input type="date"
               name="stock_entry_date"
               class="form-control">
    </div>

    <div class="form-group">
        <label>Tanggal Expire</label>
        <input type="date"
               name="expiry_date"
               class="form-control">
    </div>

    <div class="form-group">
        <label>Gambar</label>
        <input type="file"
               name="image"
               class="form-control">
    </div>

    <button type="submit" class="btn btn-success">
        Simpan Produk
    </button>

</form>

</div>
@endsection