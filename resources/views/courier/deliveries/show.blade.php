@extends('admin.layout')

@section('content')
<div class="admin-card">
    <div class="admin-header">
        <h1 class="admin-title">📦 Detail Pengiriman #{{ $order->order_number }}</h1>
        <a href="{{ route('courier.deliveries.index') }}" class="btn btn-warning">← Kembali ke Daftar</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border: 1px solid #c3e6cb;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
        <div style="background: #f9f9f9; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
            <h3 style="color: #2e7d32; margin-top: 0; margin-bottom: 15px; font-size: 16px;">👤 Informasi Pelanggan</h3>
            <p style="margin-bottom: 8px;"><strong>No Order:</strong> {{ $order->order_number }}</p>
            <p style="margin-bottom: 8px;"><strong>Nama Pelanggan:</strong> {{ $order->customer_name }}</p>
            <p style="margin-bottom: 8px;"><strong>Nomor Telepon:</strong> <a href="tel:{{ $order->customer_phone }}" style="color: #2e7d32; font-weight: bold; text-decoration: none;">{{ $order->customer_phone }}</a></p>
            <p style="margin-bottom: 8px;"><strong>Alamat Lengkap:</strong> {{ $order->delivery_address }}</p>
            @if($order->notes)
                <div style="background: #fff3cd; padding: 12px; border-radius: 8px; color: #856404; margin-top: 12px; border: 1px dashed #ffeba7;">
                    <strong>📝 Catatan Pelanggan:</strong> {{ $order->notes }}
                </div>
            @endif
        </div>

        <div style="background: #f9f9f9; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
            <h3 style="color: #2e7d32; margin-top: 0; margin-bottom: 15px; font-size: 16px;">🛵 Update Status Pengiriman</h3>
            
            @php
                $isReturnOrder = ($order->tracking_status === 'returned' || $order->status === 'Pengembalian' || !empty($order->return_reason));
            @endphp

            <p style="margin-bottom: 15px;">
                <strong>Status Saat Ini:</strong> 
                @if($order->status === 'Pengembalian')
                    <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-weight: bold; font-size: 13px; border: 1px solid #fecaca;">
                        🔄 Pengembalian / Retur
                    </span>
                @elseif($order->status === 'Pengiriman Pengganti')
                    <span style="background: #e0e7ff; color: #3730a3; padding: 4px 12px; border-radius: 12px; font-weight: bold; font-size: 13px; border: 1px solid #c7d2fe;">
                        🚚 Pengiriman Produk Pengganti
                    </span>
                @elseif($isReturnOrder)
                    <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-weight: bold; font-size: 13px; border: 1px solid #fecaca;">
                        🔄 Pengembalian / Retur
                    </span>
                @else
                    <span style="background: #2e7d32; color: white; padding: 4px 12px; border-radius: 12px; font-weight: bold; font-size: 13px;">
                        {{ $order->status }}
                    </span>
                @endif
            </p>

            @if($isReturnOrder)
                <div style="background: #fff5f5; padding: 14px; border-radius: 10px; border: 1px solid #fecaca; margin-bottom: 15px;">
                    <h4 style="margin: 0 0 6px 0; color: #dc2626; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                        <span>🔄</span> Informasi Pengembalian dari Pelanggan
                    </h4>
                    <p style="margin: 0 0 6px 0; font-size: 13px; color: #334155; white-space: pre-line;">
                        <strong>Alasan:</strong> {{ $order->return_reason ?: 'Pelanggan mengajukan pengembalian pesanan.' }}
                    </p>
                    @if($order->return_proof)
                        <div style="margin-top: 8px;">
                            <a href="{{ asset($order->return_proof) }}" target="_blank" style="color: #dc2626; font-weight: bold; font-size: 12.5px; text-decoration: underline;">
                                📷 Lihat Foto Bukti Kendala Retur
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            @if($order->status === 'Pengembalian')
                <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; text-align: center; font-weight: bold; border: 1px solid #fecaca; margin-bottom: 15px;">
                    🔄 Status Pesanan: Di-retur / Pengembalian
                </div>

                <form action="{{ route('courier.deliveries.status', $order->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Pengiriman Pengganti">
                    <button type="submit" onclick="return confirm('Mulai pengiriman produk pengganti ke alamat pelanggan?')" style="width: 100%; padding: 12px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 14.5px; cursor: pointer; box-shadow: 0 3px 8px rgba(37, 99, 235, 0.3);">
                        📦 Mulai Kirim Ulang Produk Pengganti
                    </button>
                </form>
            @elseif($order->status === 'Pengiriman Pengganti')
                <form action="{{ route('courier.deliveries.status', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="status" value="Selesai Retur">
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 6px; color: #1e293b;">📸 Upload Foto Bukti Penyerahan Produk Pengganti *</label>
                        <input type="file" name="delivery_proof" class="form-control" accept="image/*" required style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 8px; width: 100%;">
                    </div>
                    <button type="submit" class="btn btn-success" style="width: 100%; padding: 12px; font-size: 15px;">
                        ✅ Selesaikan Pengiriman Pengganti (Terkirim)
                    </button>
                </form>
            @else
                <form action="{{ route('courier.deliveries.status', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($order->status == 'Menunggu Kurir' || $order->status == 'completed' || $order->status == 'pending')
                        <input type="hidden" name="status" value="Diambil Kurir">
                        <button type="submit" class="btn btn-success" style="width: 100%; padding: 12px; font-size: 15px;">✋ Ambil Pesanan Ini (Diambil Kurir)</button>
                    @elseif($order->status == 'Diambil Kurir')
                        <input type="hidden" name="status" value="Dalam Perjalanan">
                        <button type="submit" class="btn btn-warning" style="width: 100%; padding: 12px; font-size: 15px;">🛵 Mulai Pengiriman (Dalam Perjalanan)</button>
                    @elseif($order->status == 'Dalam Perjalanan')
                        <input type="hidden" name="status" value="Terkirim">
                        <div style="margin-bottom: 15px;">
                            <label style="font-weight: bold; display: block; margin-bottom: 6px; color: #1e293b;">📸 Upload Foto Bukti Pengiriman *</label>
                            <input type="file" name="delivery_proof" class="form-control" accept="image/*" required style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 8px; width: 100%;">
                        </div>
                        <button type="submit" class="btn btn-success" style="width: 100%; padding: 12px; font-size: 15px; margin-bottom: 10px;">✅ Selesaikan Pengiriman (Terkirim)</button>
                    @else
                        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; text-align: center; font-weight: bold; margin-bottom: 10px;">
                            ✅ Pengiriman Telah Selesai
                        </div>
                    @endif
                </form>

                @if($isReturnOrder && $order->status !== 'Terkirim')
                    <form action="{{ route('courier.deliveries.status', $order->id) }}" method="POST" style="margin-top: 10px;">
                        @csrf
                        <input type="hidden" name="status" value="Pengembalian">
                        <button type="submit" onclick="return confirm('Tandai pesanan ini sebagai Pengembalian/Retur?')" style="width: 100%; padding: 10px; background: #dc2626; color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 14px; cursor: pointer;">
                            🔄 Tandai Pesanan Di-retur / Pengembalian
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <!-- Leaflet OpenStreetMap CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @if($order->delivery_address)
    <div style="margin-bottom: 25px;">
        <h3 style="color: #2e7d32; margin-bottom: 12px; font-size: 16px;">🗺️ Peta Alamat Tujuan Pengiriman</h3>
        <div id="courierDeliveryMap" style="height: 320px; border-radius: 12px; border: 1px solid #cbd5e1; overflow: hidden; z-index: 1;"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const storeLat = -6.8925;
            const storeLon = 107.5620;
            const address = @json($order->delivery_address ?? '');

            const map = L.map('courierDeliveryMap').setView([storeLat, storeLon], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const storeIcon = L.divIcon({
                className: 'admin-map-pin-store',
                html: '<div style="background:#2e7d32; color:white; padding:5px 10px; border-radius:16px; font-weight:bold; font-size:11px; box-shadow:0 2px 6px rgba(0,0,0,0.3); border:2px solid white; white-space:nowrap;">🏪 Toko Dewi Lestari 2</div>',
                iconSize: [130, 26],
                iconAnchor: [65, 13]
            });
            L.marker([storeLat, storeLon], { icon: storeIcon }).addTo(map)
                .bindPopup('<b>🏪 Toko Dewi Lestari 2</b><br>Jl. Raya Cimindi No.59, Cimahi')
                .openPopup();

            if (address && address.trim() !== '') {
                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address + ', Indonesia'))
                    .then(res => res.json())
                    .then(data => {
                        let destLat = storeLat - 0.015;
                        let destLon = storeLon + 0.015;
                        if (data && data.length > 0) {
                            destLat = parseFloat(data[0].lat);
                            destLon = parseFloat(data[0].lon);
                        }

                        const destIcon = L.divIcon({
                            className: 'admin-map-pin-dest',
                            html: '<div style="background:#dc2626; color:white; padding:5px 10px; border-radius:16px; font-weight:bold; font-size:11px; box-shadow:0 2px 6px rgba(0,0,0,0.3); border:2px solid white; white-space:nowrap;">🏠 Alamat Tujuan</div>',
                            iconSize: [120, 26],
                            iconAnchor: [60, 13]
                        });

                        L.marker([destLat, destLon], { icon: destIcon }).addTo(map)
                            .bindPopup('<b>🏠 Alamat Tujuan Pelanggan:</b><br>' + address);

                        L.polyline([
                            [storeLat, storeLon],
                            [destLat, destLon]
                        ], {
                            color: '#2563eb',
                            weight: 4,
                            opacity: 0.85,
                            dashArray: '8, 8'
                        }).addTo(map);

                        const bounds = L.latLngBounds([
                            [storeLat, storeLon],
                            [destLat, destLon]
                        ]);
                        map.fitBounds(bounds, { padding: [40, 40] });
                    })
                    .catch(err => {
                        console.log('Geocoding error:', err);
                    });
            }
        });
    </script>
    @endif

    <h3 style="color: #2e7d32; margin-bottom: 12px; font-size: 16px;">🛒 Daftar Produk & Total Berat Barang</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah (Qty)</th>
                <th>Berat Satuan</th>
                <th>Total Berat</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotalWeight = 0; @endphp
            @foreach($order->items as $item)
                @php
                    $unitWeight = \App\Http\Controllers\OrderController::parseWeightToGrams($item->weight ?? '');
                    if ($unitWeight <= 0 && $item->product) {
                        $unitWeight = \App\Http\Controllers\OrderController::parseWeightToGrams($item->product->weight ?? '');
                    }
                    $qty = $item->qty ?? $item->quantity ?? 1;
                    $itemTotalWeight = $unitWeight * $qty;
                    $grandTotalWeight += $itemTotalWeight;
                @endphp
                <tr>
                    <td>{{ $item->product->name ?? ($item->product_name ?? 'Produk') }}</td>
                    <td>{{ $qty }}</td>
                    <td>{{ $unitWeight > 0 ? $unitWeight . ' gr' : '-' }}</td>
                    <td><strong>{{ $itemTotalWeight > 0 ? $itemTotalWeight . ' gr' : '-' }}</strong></td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f8fafc;">
                <td colspan="3" style="text-align: right;">TOTAL BERAT & TOTAL BAYAR:</td>
                <td style="color: #1e293b; font-size: 15px;">⚖️ {{ $grandTotalWeight > 0 ? $grandTotalWeight . ' gr' : '-' }}</td>
                <td>-</td>
                <td style="color: #2e7d32; font-size: 16px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if($order->delivery_proof)
        <div style="margin-top: 25px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
            <h3 style="color: #2e7d32; margin-top: 0; margin-bottom: 12px;">📷 Bukti Pengiriman Foto</h3>
            <img src="{{ asset($order->delivery_proof) }}" style="max-width: 380px; border-radius: 10px; border: 1px solid #cbd5e1; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <div style="font-size: 13px; color: #64748b; margin-top: 8px;">Diterima tanggal & waktu: {{ $order->delivered_at }}</div>
        </div>
    @endif
</div>
@endsection
