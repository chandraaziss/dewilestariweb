@extends('layouts.app')

@section('content')

<div class="admin-container">

    <h1 class="admin-title">
        📦 Daftar Pesanan
    </h1>

    <table class="admin-table">

        <thead>
            <tr>
                <th>No Order</th>
                <th>Pembeli</th>
                <th>Telepon</th>
                <th>Total</th>
                <th>Status Pesanan</th>
                <th>Status Bayar</th>
                <th>Tanggal</th>
                <th>Detail</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

         @forelse($orders as $order)
         <tr>

    <td>{{ $order->order_number }}</td>

    <td>{{ $order->customer_name }}</td>

    <td>{{ $order->customer_phone }}</td>

    <td>
        Rp {{ number_format($order->total_amount,0,',','.') }}
    </td>

    <td>
        @if($order->status == 'completed')
            <span class="badge-success">Completed</span>
        @else
            <span class="badge-warning">Pending</span>
        @endif
    </td>

    <td>
        @if($order->payment_status == 'paid')
            <span class="badge-success">Paid</span>
        @else
            <span class="badge-danger">Unpaid</span>
        @endif
    </td>

    <td>{{ $order->created_at }}</td>

    <td>
        <a href="/admin/orders/{{ $order->id }}"
           class="btn-edit">
           Detail
        </a>
    </td>

    <td>
        @if($order->payment_status != 'paid')
            <a href="/admin/pay/{{ $order->id }}"
               class="btn-edit">
               Simulasikan Bayar
            </a>
        @endif
    </td>

</tr>

@empty

<tr>
    <td colspan="9">
        Belum ada pesanan
    </td>
</tr>

@endforelse
        </tbody>

    </table>

</div>

@endsection