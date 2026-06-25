@extends('admin.layout')

@section('content')
<h1>Tambah Produk</h1>

<!-- <form
    action="/admin/products/store"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf

    Nama Produk
    <input type="text" name="name">
    <br><br>

    Deskripsi
    <textarea name="description"></textarea>
    <br><br>

    Harga
    <input type="number" name="price">
    <br><br>

    Kategori
    <input type="text" name="category">
    <br><br>

    Stok
    <input type="number" name="stock">
    <br><br>

    Gambar
    <input type="file" name="image">
    <br><br>

    <button type="submit">
        Simpan
    </button>
</form> -->
<div class="admin-card">

<h1 class="admin-title">
    ➕ Tambah Produk
</h1>

<form ...>

<div class="form-group">
    <label>Nama Produk</label>
    <input type="text"
           name="name"
           class="form-control">
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
    <textarea
        name="description"
        class="form-control"></textarea>
</div>

<div class="form-group">
    <label>Harga</label>
    <input type="number"
           name="price"
           class="form-control">
</div>

<div class="form-group">
    <label>Stok</label>
    <input type="number"
           name="stock"
           class="form-control">
</div>

<div class="form-group">
    <label>Gambar</label>
    <input type="file"
           name="image"
           class="form-control">
</div>

<button class="btn btn-success">
    Simpan Produk
</button>

</form>

</div>
@endsection