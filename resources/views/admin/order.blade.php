@extends('admin.layout')

@section('content')

<div class="admin-card">

<h1 class="admin-title">
📦 Daftar Pesanan
</h1>

<table class="admin-table">

<tr>
    <th>No Order</th>
    <th>Customer</th>
    <th>HP</th>
    <th>Total</th>
    <th>Status</th>
    <th>Pembayaran</th>
</tr>

@foreach($orders as $order)

<tr>

<td>{{ $order->order_number }}</td>

<td>{{ $order->customer_name }}</td>

<td>{{ $order->customer_phone }}</td>

<td>
Rp {{ number_format($order->total_amount,0,',','.') }}
</td>

<td>{{ $order->status }}</td>

<td>{{ $order->payment_status }}</td>

</tr>

@endforeach

</table>

</div>

@endsection