@extends('admin.layout')

@section('content')

    <div class="admin-card">

        <div class="admin-header">
            <h1 class="admin-title">
                📦 Manajemen Stok
            </h1>

            <a href="/admin/products/create" class="btn btn-success">
                ➕ Tambah Produk
            </a>
        </div>

        <table class="admin-table">

            <tr>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Berat Produk</th>
                <th>Harga</th>
                <th>Manajemen Stok</th>
                <th>Aksi</th>
            </tr>

            @foreach($products as $product)
                @php
                    $isExpired = $product->expiry_date && $product->expiry_date->isPast();
                @endphp

                <tr style="{{ $isExpired ? 'background:#ffebee;color:#b71c1c;' : '' }}">

                    <td>
                        @if($product->image_path)
                            <img src="/{{ $product->image_path }}" class="product-image">
                        @endif
                    </td>

                    <td>
                        {{ $product->name }}
                        @if($isExpired)
                            <div
                                style="margin-top: 0.25rem; display:inline-block; padding: 0.25rem 0.5rem; color: #b71c1c; background: #ffcdd2; border-radius: 4px; font-size: 0.85rem;">
                                ❌ Kadaluarsa
                            </div>
                        @endif
                    </td>

                    <td>{{ $product->category }}</td>

                    <td>
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>

                    <td>
                        <div><strong>Stok:</strong> {{ $product->stock }}</div>
                        @if($product->stock_entry_date)
                            <div style="font-size:0.9rem; color:#444;">Masuk:
                                {{ \Carbon\Carbon::parse($product->stock_entry_date)->translatedFormat('d F Y') }}</div>
                        @endif
                        @if($product->expiry_date)
                            <div style="font-size:0.9rem; color:#444;">Expire:
                                {{ \Carbon\Carbon::parse($product->expiry_date)->translatedFormat('d F Y') }}</div>
                        @endif
                        @if($product->stock > 0 && $product->stock <= 10 && !$isExpired)
                            <div
                                style="margin-top: 0.25rem; display:inline-block; padding: 0.25rem 0.5rem; color: #721c24; background: #f8d7da; border-radius: 4px; font-size: 0.85rem;">
                                ⚠️ Hampir habis
                            </div>
                        @endif
                    </td>

                    <td>

                        <a href="/admin/products/{{ $product->id }}/edit" class="btn btn-warning">
                            Edit
                        </a>

                        <form action="/admin/products/{{ $product->id }}" method="POST" style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus produk ini?')">

                                Hapus

                            </button>

                        </form>

                    </td>

                </tr>

            @endforeach

        </table>

    </div>

@endsection