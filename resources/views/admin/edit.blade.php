<h1>Edit Produk</h1>

<form
    action="/admin/products/{{ $product->id }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf
    @method('PUT')

    Nama Produk
    <input
        type="text"
        name="name"
        value="{{ $product->name }}"
    >
    <br><br>

    Deskripsi
    <textarea name="description">{{ $product->description }}</textarea>
    <br><br>

    Harga
    <input
        type="number"
        name="price"
        value="{{ $product->price }}"
    >
    <br><br>

    Stok
    <input
        type="number"
        name="stock"
        value="{{ $product->stock }}"
    >
    <br><br>
    Berat Produk
    <input
    type="text"
    name="category"
    value="{{ $product->category }}"
    >
    <br><br>

    @if($product->image_path)
        <img
            src="/{{ $product->image_path }}"
            width="120"
        >
        <br><br>
    @endif

    Ganti Gambar
    <input type="file" name="image">
    <br><br>

    <button type="submit">
        Update
    </button>
</form>