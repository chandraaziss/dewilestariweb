@extends('layouts.app')

@section('content')

<div class="admin-container">

    <h1 class="admin-title">
        📊 Laporan Penjualan
    </h1>

    <div class="report-cards">

        <div class="report-card">

            <h3>Total Pesanan</h3>

            <h2>
                {{ $totalOrders }}
            </h2>

        </div>

        <div class="report-card">

            <h3>Total Penjualan</h3>

            <h2>
                Rp {{ number_format($totalSales,0,',','.') }}
            </h2>

        </div>

    </div>

    <table class="admin-table">

        <thead>

            <tr>
                <th>No Order</th>
                <th>Pembeli</th>
                <th>Total</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>

        </thead>

        <tbody>

        @forelse($orders as $order)

            <tr>

                <td>
                    {{ $order->order_number }}
                </td>

                <td>
                    {{ $order->customer_name }}
                </td>

                <td>
                    Rp {{ number_format($order->total_amount,0,',','.') }}
                </td>

                <td>

                    <span class="badge-success">
                        Paid
                    </span>

                </td>

                <td>
                    {{ $order->created_at }}
                </td>

            </tr>

        @empty

            <tr>

                <td colspan="5">
                    Belum ada penjualan
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection