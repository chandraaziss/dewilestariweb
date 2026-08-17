@extends('admin.layout')

@section('content')
<div class="admin-card">
    <div class="admin-header">
        <h1 class="admin-title">📜 Riwayat Pengiriman Selesai</h1>
        <a href="{{ route('courier.deliveries.index') }}" class="btn btn-success">📦 Daftar Tugas Aktif</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>No Order</th>
                <th>Nama Pelanggan</th>
                <th>Telepon</th>
                <th>Alamat</th>
                <th>Waktu Diterima</th>
                <th>Bukti Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td><strong>#{{ $order->order_number }}</strong></td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->customer_phone }}</td>
                <td style="max-width: 200px; text-align: left;">{{ $order->delivery_address }}</td>
                <td>{{ \Carbon\Carbon::parse($order->delivered_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                <td>
                    @if($order->delivery_proof)
                        <a href="{{ asset($order->delivery_proof) }}" target="_blank">
                            <img src="{{ asset($order->delivery_proof) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    <a href="{{ route('courier.deliveries.show', $order->id) }}" class="btn btn-success" style="padding: 4px 10px; font-size: 12px;">Lihat Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: #888; padding: 30px;">
                    Belum ada riwayat pengiriman yang selesai.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
