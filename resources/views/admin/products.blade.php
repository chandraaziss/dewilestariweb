@extends('admin.layout')

@section('content')

<div class="admin-card">

    <div class="admin-header">
        <h1 class="admin-title">
            📦 Manajemen Produk
        </h1>

        <a href="/admin/products/create"
           class="btn btn-success">
            ➕ Tambah Produk
        </a>
    </div>

    <table class="admin-table">

        <tr>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Berat Produk</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>

        @foreach($products as $product)

        <tr>

            <td>
                @if($product->image_path)
                    <img
                        src="/{{ $product->image_path }}"
                        class="product-image">
                @endif
            </td>

            <td>{{ $product->name }}</td>

            <td>{{ $product->category }}</td>

            <td>
                Rp {{ number_format($product->price,0,',','.') }}
            </td>

            <td>{{ $product->stock }}</td>

            <td>

                <a
                    href="/admin/products/{{ $product->id }}/edit"
                    class="btn btn-warning">
                    Edit
                </a>

                <form
                    action="/admin/products/{{ $product->id }}"
                    method="POST"
                    style="display:inline">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-danger"
                        onclick="return confirm('Hapus produk ini?')">

                        Hapus

                    </button>

                </form>

            </td>

        </tr>

        @endforeach

    </table>

</div>

@endsection