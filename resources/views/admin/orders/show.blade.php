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

        <p>
            <strong>ID Tiket Pelacakan:</strong>
            {{ $order->tracking_ticket_id ?? '-' }}
        </p>

        <p style="background: #e8f5e9; padding: 10px 14px; border-radius: 8px; border: 1px solid #c8e6c9; color: #1b5e20;">
            <strong>📅 Estimasi Pengiriman:</strong>
            <span style="font-weight: bold; color: #2e7d32;">{{ $order->estimated_delivery }}</span>
        </p>

        @if($order->delivery_proof)
            <p style="margin-top: 15px;">
                <strong>Bukti Foto Pengiriman:</strong><br>
                <a href="{{ asset($order->delivery_proof) }}" target="_blank" style="display: inline-block;">
                    <img src="{{ asset($order->delivery_proof) }}" alt="Bukti Foto Pengiriman" style="max-width: 300px; border-radius: 8px; margin-top: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #ddd;">
                </a>
            </p>
        @endif

    </div>

    <!-- Leaflet OpenStreetMap CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- OpenStreetMap Delivery Location Map for Admin -->
    @if($order->delivery_option === 'delivery' || $order->delivery_address)
    <div class="card" style="margin-top: 20px;">
        <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 17px; display: flex; align-items: center; gap: 8px;">
            <span>🗺️</span> Peta Lokasi Pengiriman OpenStreetMap
        </h3>
        <p style="margin: 0 0 12px 0; font-size: 13.5px; color: #4b5563;">
            📍 <strong>Alamat Tujuan:</strong> {{ $order->delivery_address ?: 'Lokasi Pengiriman' }}
        </p>
        <div id="adminDeliveryMap" style="height: 320px; border-radius: 10px; border: 1px solid #ccc; overflow: hidden; z-index: 1;"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const storeLat = -6.8925;
            const storeLon = 107.5620;
            const address = @json($order->delivery_address ?? '');

            const map = L.map('adminDeliveryMap').setView([storeLat, storeLon], 13);

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

    @if($order->return_reason || $order->tracking_status === 'returned')
    <h2 style="margin-top:30px; color: #dc2626;">
        🔄 Pengajuan Pengembalian (Retur) Pelanggan
    </h2>
    <div class="card" style="margin-bottom:30px; border-left: 4px solid #dc2626; background: #fff5f5;">
        <p style="margin: 0 0 10px 0;"><strong>Alasan / Detail Kendala:</strong></p>
        <div style="background: white; padding: 12px 15px; border-radius: 6px; border: 1px solid #feb2b2; margin-bottom: 15px; white-space: pre-line;">
            {{ $order->return_reason ?: 'Tidak ada detail alasan' }}
        </div>

        @if($order->return_requested_at)
            <p style="margin: 0 0 15px 0; font-size: 13px; color: #666;">
                🕐 <strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($order->return_requested_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
            </p>
        @endif

        @if($order->return_proof)
            <p style="margin: 0 0 8px 0;"><strong>Bukti Foto Retur:</strong></p>
            <div style="margin-bottom: 15px;">
                <a href="{{ asset($order->return_proof) }}" target="_blank">
                    <img src="{{ asset($order->return_proof) }}" alt="Bukti Retur" style="max-width: 300px; max-height: 250px; border-radius: 8px; border: 1px solid #ccc;">
                </a>
                <small style="display: block; color: #666; margin-top: 4px;">Klik foto untuk memperbesar</small>
            </div>
        @endif
    </div>
    @endif

    <h2 style="margin-top:30px;">
        🚚 Update Status Pengiriman & Retur
    </h2>
    <div class="card" style="margin-bottom:30px;">
        <form action="/admin/orders/{{ $order->id }}/tracking" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:15px;">
                <label for="tracking_status" style="font-weight:bold; display:block; margin-bottom:5px;">Status Pelacakan</label>
                <select name="tracking_status" id="tracking_status" style="padding:8px; border-radius:5px; border:1px solid #ccc; width:100%; max-width:300px;">
                    <option value="pending" {{ $order->tracking_status == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="preparing" {{ $order->tracking_status == 'preparing' ? 'selected' : '' }}>Dikemas</option>
                    <option value="shipped" {{ $order->tracking_status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="almost_arrived" {{ $order->tracking_status == 'almost_arrived' ? 'selected' : '' }}>Hampir Sampai</option>
                    <option value="completed" {{ $order->tracking_status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="returned" {{ $order->tracking_status == 'returned' ? 'selected' : '' }}>Pengembalian</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label for="estimated_delivery_date" style="font-weight:bold; display:block; margin-bottom:5px;">Estimasi Tanggal Pengiriman (Opsional / Manual Override)</label>
                <input type="date" name="estimated_delivery_date" id="estimated_delivery_date" 
                       value="{{ $order->estimated_delivery_date ? \Carbon\Carbon::parse($order->estimated_delivery_date)->format('Y-m-d') : '' }}" 
                       style="padding:8px; border-radius:5px; border:1px solid #ccc; width:100%; max-width:300px;">
                <small style="display:block; color:#666; margin-top:4px;">Kosongkan jika ingin menggunakan estimasi otomatis sistem.</small>
            </div>

            @if($order->return_reason || $order->tracking_status === 'returned' || $order->return_status)
            <div style="margin-bottom:15px;">
                <label for="return_status" style="font-weight:bold; display:block; margin-bottom:5px;">Status Keputusan & Proses Retur</label>
                <select name="return_status" id="return_status" style="padding:8px; border-radius:5px; border:1px solid #ccc; width:100%; max-width:350px;">
                    <option value="pending" {{ ($order->return_status ?? 'pending') == 'pending' ? 'selected' : '' }}>⏳ Dalam Peninjauan (Pending)</option>
                    <option value="approved" {{ ($order->return_status ?? '') == 'approved' ? 'selected' : '' }}>✅ Disetujui - Menunggu Kirim Ulang</option>
                    <option value="reshipped" {{ ($order->return_status ?? '') == 'reshipped' ? 'selected' : '' }}>🚚 Produk Pengganti Sedang Dikirim Ulang</option>
                    <option value="resolved" {{ ($order->return_status ?? '') == 'resolved' ? 'selected' : '' }}>🎉 Selesai (Produk Pengganti Diterima)</option>
                    <option value="rejected" {{ ($order->return_status ?? '') == 'rejected' ? 'selected' : '' }}>❌ Ditolak (Rejected)</option>
                </select>
            </div>
            @endif

            <div id="delivery_proof_container" style="margin-bottom:15px; display: none;">
                <label for="delivery_proof" style="font-weight:bold; display:block; margin-bottom:5px;">
                    Bukti Foto Pengiriman (Sudah Sampai)
                </label>
                <input type="file" name="delivery_proof" id="delivery_proof" accept="image/*" style="padding:8px; border-radius:5px; border:1px solid #ccc; width:100%; max-width:300px;">
                <small style="color: #666; display: block; margin-top: 5px;">Format: JPG, PNG, JPEG, GIF, WEBP. Maks 2MB.</small>
            </div>

            <button type="submit" style="padding:8px 15px; background:#2e7d32; color:white; border:none; border-radius:5px; cursor:pointer;">Update Status</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('tracking_status');
            const proofContainer = document.getElementById('delivery_proof_container');

            function toggleProofContainer() {
                if (statusSelect.value === 'completed') {
                    proofContainer.style.display = 'block';
                } else {
                    proofContainer.style.display = 'none';
                }
            }

            statusSelect.addEventListener('change', toggleProofContainer);
            toggleProofContainer(); // Run on load
        });
    </script>

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