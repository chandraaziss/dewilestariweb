@extends('layouts.app')

@section('content')

<div class="admin-container">

    <h1 class="admin-title">
        📦 Detail Pesanan
    </h1>

    <div class="card">

        <p>
            <strong>No Order:</strong>
            {{ $order->order_number }}
        </p>

        <p>
            <strong>Nama:</strong>
            {{ $order->customer_name }}
        </p>

        <p>
            <strong>Telepon:</strong>
            {{ $order->customer_phone }}
        </p>

        <p>
            <strong>Pengiriman:</strong>
            {{ $order->delivery_option }}
        </p>

        <p>
            <strong>Alamat:</strong>
            {{ $order->delivery_address }}
        </p>

        <p>
            <strong>Catatan:</strong>
            {{ $order->notes }}
        </p>

        <p>
            <strong>Total:</strong>
            Rp {{ number_format($order->total_amount,0,',','.') }}
        </p>

        <p>
            <strong>Status:</strong>
            {{ $order->status }}
        </p>

        <p>
            <strong>Pembayaran:</strong>
            {{ $order->payment_status }}
        </p>

    </div>

    <h2 style="margin-top:30px;">
        🛒 Produk yang Dibeli
    </h2>

    <table class="admin-table">

        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>

        @foreach($order->items as $item)

            <tr>

                <td>
                     {{ $item->product->name ?? '-' }}
                </td>

                <td>
                    {{ $item->qty }}
                </td>

                <td>
                    Rp {{ number_format($item->price,0,',','.') }}
                </td>

                <td>
                    Rp {{ number_format($item->subtotal,0,',','.') }}
                </td>
                <td>

    <a href="/admin/orders/{{ $order->id }}" class="btn-edit"> Detail </a> </td>
</tr>
        @endforeach
        </tbody>

    </table>

    <br>

    <a href="/admin/orders" class="btn-edit">
        ← Kembali
    </a>

</div>

@endsection