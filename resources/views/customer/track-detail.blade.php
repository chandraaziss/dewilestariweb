@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 120px; padding-bottom: 60px; min-height: 85vh; font-family: 'Arial', sans-serif; max-width: 800px; margin: 0 auto;">

    <!-- Back Button -->
    <a href="/customer/dashboard" style="display: inline-flex; align-items: center; gap: 6px; color: #2e7d32; text-decoration: none; font-weight: bold; font-size: 14px; margin-bottom: 24px; transition: all 0.2s;" onmouseover="this.style.color='#1b5e20'" onmouseout="this.style.color='#2e7d32'">
        ← Kembali ke Dashboard
    </a>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: bold; border: 1px solid #a7f3d0; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.15);">
            <span style="font-size: 20px;">✅</span> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; font-weight: bold; border: 1px solid #fecaca; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.15);">
            <span style="font-size: 20px;">⚠️</span> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #fecaca; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.15);">
            <div style="font-weight: bold; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <span>⚠️</span> Terdapat kesalahan input:
            </div>
            <ul style="margin: 0; padding-left: 20px; font-size: 13.5px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $optionLower = strtolower($order->delivery_option ?? '');
        $isPickupOrder = str_contains($optionLower, 'pickup') || str_contains($optionLower, 'ambil');
    @endphp

    <!-- Order Info Card -->
    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
            <div>
                <h2 style="margin: 0 0 6px 0; color: #1f2937; font-size: 22px;">📦 Lacak Pesanan</h2>
                <p style="margin: 0; color: #6b7280; font-size: 14px;">No. Pesanan: <strong style="color: #2e7d32;">{{ $order->order_number }}</strong></p>
            </div>
            <div style="text-align: right;">
                <span style="background: {{ $order->tracking_status === 'completed' ? '#d1fae5' : ($order->tracking_status === 'returned' ? '#fee2e2' : '#e0f2fe') }}; color: {{ $order->tracking_status === 'completed' ? '#065f46' : ($order->tracking_status === 'returned' ? '#991b1b' : '#0369a1') }}; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: bold;">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 14px; color: #4b5563;">
            <div><strong>Nama:</strong> {{ $order->customer_name }}</div>
            <div><strong>Telepon:</strong> {{ $order->customer_phone }}</div>
            <div><strong>Pengiriman:</strong> {{ ucfirst($order->delivery_option) }}</div>
            <div><strong>Total:</strong> <span style="color: #2e7d32; font-weight: bold;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
            @if($order->delivery_address)
                <div style="grid-column: 1 / -1;"><strong>Alamat:</strong> {{ $order->delivery_address }}</div>
            @endif
            @if($order->tracking_ticket_id)
                <div style="grid-column: 1 / -1;"><strong>ID Tiket:</strong> <span style="font-family: monospace; background: #f3f4f6; padding: 2px 8px; border-radius: 4px;">{{ $order->tracking_ticket_id }}</span></div>
            @endif

            <!-- Estimated Delivery Highlight Card -->
            <div style="grid-column: 1 / -1; background: linear-gradient(135deg, #ecfdf5, #f0fdf4); padding: 12px 18px; border-radius: 12px; border: 1px solid #a7f3d0; margin-top: 6px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 24px;">📅</span>
                    <div>
                        <div style="font-size: 12px; color: #065f46; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">Estimasi Pengiriman</div>
                        <div style="font-size: 15px; color: #047857; font-weight: 800; margin-top: 1px;">{{ $order->estimated_delivery }}</div>
                    </div>
                </div>
                <span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2);">
                    {{ $order->tracking_status === 'completed' ? 'TERKIRIM' : ($isPickupOrder ? 'SIAP AMBIL' : 'ESTIMASI TIBA') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Leaflet OpenStreetMap CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- OpenStreetMap Delivery Location & Live Courier Monitoring Map (ALWAYS RENDERED FOR ALL ORDERS) -->
    <div style="background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
            <h3 style="margin: 0; color: #1f2937; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                @if($isPickupOrder)
                    <span>🗺️</span> Peta Lokasi Pengambilan Pesanan (OpenStreetMap)
                @else
                    <span>🗺️</span> Monitoring Pengiriman & Posisi Pesanan (OpenStreetMap)
                @endif
            </h3>
            <span style="background: {{ $isPickupOrder ? '#2e7d32' : '#ef4444' }}; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                <span style="width: 8px; height: 8px; background: white; border-radius: 50%; display: inline-block; animation: blink 1s infinite;"></span> {{ $isPickupOrder ? 'LOKASI TOKO' : 'LIVE TRACKING' }}
            </span>
        </div>

        <p style="margin: 0 0 15px 0; font-size: 13.5px; color: #4b5563;">
            @if($isPickupOrder)
                🏪 <strong>Lokasi Toko (Tempat Ambil):</strong> Toko Dewi Lestari 2 — Jl. Raya Cimindi No.59, Cimahi
            @else
                📍 <strong>Alamat Tujuan:</strong> {{ $order->delivery_address ?: 'Lokasi Pengiriman' }}
            @endif
        </p>

        <div id="trackDeliveryMap" style="height: 350px; border-radius: 12px; border: 1px solid #d1d5db; overflow: hidden; z-index: 1;"></div>
    </div>

    <style>
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        @keyframes pulse-pin { 0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); } 70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); } 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const storeLat = -6.8925;
            const storeLon = 107.5620;
            const address = @json($order->delivery_address ?? '');
            const trackingStatus = @json($order->tracking_status ?? 'preparing');
            const isPickup = @json($isPickupOrder);

            const map = L.map('trackDeliveryMap').setView([storeLat, storeLon], isPickup ? 15 : 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Store Marker
            const storeIcon = L.divIcon({
                className: 'custom-map-pin-store',
                html: '<div style="background:#2e7d32; color:white; padding:7px 14px; border-radius:20px; font-weight:bold; font-size:12.5px; box-shadow:0 3px 8px rgba(0,0,0,0.3); border:2px solid white; white-space:nowrap;">🏪 Toko Dewi Lestari 2</div>',
                iconSize: [150, 32],
                iconAnchor: [75, 16]
            });

            const storeMarker = L.marker([storeLat, storeLon], { icon: storeIcon }).addTo(map);

            if (isPickup) {
                storeMarker.bindPopup('<b>🏪 Lokasi Pengambilan Pesanan</b><br>Pesanan diambil langsung di Toko Dewi Lestari 2<br><i>Jl. Raya Cimindi No.59, Cimahi</i>').openPopup();
            } else {
                storeMarker.bindPopup('<b>🏪 Toko Dewi Lestari 2</b><br>Jl. Raya Cimindi No.59, Cimahi');

                let destLat = storeLat - 0.015;
                let destLon = storeLon + 0.015;

                function renderMapWithDest(dLat, dLon) {
                    const destIcon = L.divIcon({
                        className: 'custom-map-pin-dest',
                        html: '<div style="background:#dc2626; color:white; padding:6px 12px; border-radius:20px; font-weight:bold; font-size:12px; box-shadow:0 3px 8px rgba(0,0,0,0.3); border:2px solid white; white-space:nowrap;">🏠 Tujuan Pengiriman</div>',
                        iconSize: [130, 30],
                        iconAnchor: [65, 15]
                    });

                    L.marker([dLat, dLon], { icon: destIcon }).addTo(map)
                        .bindPopup('<b>🏠 Alamat Tujuan:</b><br>' + (address || 'Tujuan'));

                    // Determine Courier Progress along the route
                    let progress = 0.08;
                    let statusBadge = '📦 Pesanan Sedang Dikemas';
                    let iconSymbol = '🛵';

                    if (trackingStatus === 'shipped') {
                        progress = 0.55;
                        statusBadge = '🚚 Kurir Sedang Dalam Perjalanan';
                        iconSymbol = '🚚';
                    } else if (trackingStatus === 'almost_arrived') {
                        progress = 0.88;
                        statusBadge = '⚡ Kurir Hampir Sampai di Tujuan';
                        iconSymbol = '🛵';
                    } else if (trackingStatus === 'completed') {
                        progress = 1.00;
                        statusBadge = '✅ Pesanan Telah Sampai';
                        iconSymbol = '✅';
                    } else if (trackingStatus === 'returned') {
                        progress = 0.50;
                        statusBadge = '🔄 Pesanan Di-retur';
                        iconSymbol = '🔄';
                    }

                    const courierLat = storeLat + (dLat - storeLat) * progress;
                    const courierLon = storeLon + (dLon - storeLon) * progress;

                    // Courier Position Marker with pulsing badge
                    const courierIcon = L.divIcon({
                        className: 'custom-map-pin-courier',
                        html: `<div style="background:#f59e0b; color:white; padding:7px 14px; border-radius:20px; font-weight:bold; font-size:12px; border:2px solid white; white-space:nowrap; animation: pulse-pin 1.8s infinite;">${iconSymbol} ${statusBadge}</div>`,
                        iconSize: [210, 32],
                        iconAnchor: [105, 16]
                    });

                    const courierMarker = L.marker([courierLat, courierLon], { icon: courierIcon }).addTo(map)
                        .bindPopup(`<b>${iconSymbol} Monitoring Pengiriman</b><br>Status: <strong>${statusBadge}</strong>`)
                        .openPopup();

                    // Traveled Path (Solid Green)
                    L.polyline([
                        [storeLat, storeLon],
                        [courierLat, courierLon]
                    ], {
                        color: '#16a34a',
                        weight: 5,
                        opacity: 0.9
                    }).addTo(map);

                    // Remaining Path (Dashed Blue)
                    if (progress < 1.0) {
                        L.polyline([
                            [courierLat, courierLon],
                            [dLat, dLon]
                        ], {
                            color: '#2563eb',
                            weight: 4,
                            opacity: 0.75,
                            dashArray: '8, 8'
                        }).addTo(map);
                    }

                    const bounds = L.latLngBounds([
                        [storeLat, storeLon],
                        [dLat, dLon]
                    ]);
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

                if (address && address.trim() !== '') {
                    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address + ', Indonesia'))
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                destLat = parseFloat(data[0].lat);
                                destLon = parseFloat(data[0].lon);
                            }
                            renderMapWithDest(destLat, destLon);
                        })
                        .catch(err => {
                            console.log('Geocoding error:', err);
                            renderMapWithDest(destLat, destLon);
                        });
                } else {
                    renderMapWithDest(destLat, destLon);
                }
            }
        });
    </script>

    <!-- Timeline Tracking -->
    <div style="background: white; padding: 35px 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 25px;">
            <h3 style="margin: 0; color: #1f2937; font-size: 18px;">🚚 Status Pengiriman</h3>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: bold; display: flex; align-items: center; gap: 6px;">
                <span>⏱️ Estimasi Tiba:</span> <strong>{{ $order->estimated_delivery }}</strong>
            </div>
        </div>

        @php
            $steps = [
                ['key' => 'preparing', 'icon' => '📦', 'label' => 'Dikemas', 'desc' => 'Pesanan sedang disiapkan dan dikemas'],
                ['key' => 'shipped', 'icon' => '🚚', 'label' => 'Dikirim', 'desc' => 'Pesanan sedang dalam perjalanan'],
                ['key' => 'completed', 'icon' => '✅', 'label' => 'Selesai', 'desc' => 'Pesanan telah diterima'],
            ];

            $statusOrder = ['pending' => 0, 'preparing' => 1, 'shipped' => 2, 'almost_arrived' => 2.5, 'completed' => 3, 'returned' => -1];
            $currentStep = $statusOrder[$order->tracking_status] ?? 0;
            $isReturned = $order->tracking_status === 'returned';
        @endphp

        <div style="position: relative; padding-left: 40px;">
            @foreach($steps as $index => $step)
                @php
                    $stepNum = $index + 1;
                    $isActive = $currentStep >= $stepNum && !$isReturned;
                    $isCurrent = (int)$currentStep === $stepNum && !$isReturned;
                    $circleColor = $isActive ? '#2e7d32' : '#d1d5db';
                    $lineColor = ($currentStep > $stepNum && !$isReturned) ? '#2e7d32' : '#e5e7eb';

                    $timestamp = null;
                    if ($step['key'] === 'preparing' && $order->paid_at) $timestamp = $order->paid_at;
                    elseif ($step['key'] === 'shipped' && $order->shipped_at) $timestamp = $order->shipped_at;
                    elseif ($step['key'] === 'completed' && $order->delivered_at) $timestamp = $order->delivered_at;
                @endphp

                <div style="position: relative; padding-bottom: {{ $index < count($steps) - 1 ? '40px' : '0' }}; {{ $isCurrent ? 'animation: pulse-glow 2s infinite;' : '' }}">
                    <!-- Circle -->
                    <div style="position: absolute; left: -40px; top: 0; width: 32px; height: 32px; border-radius: 50%; background: {{ $circleColor }}; display: flex; align-items: center; justify-content: center; font-size: 14px; z-index: 2; {{ $isCurrent ? 'box-shadow: 0 0 0 6px rgba(46, 125, 50, 0.15);' : '' }}">
                        @if($isActive)
                            <span style="color: white; font-size: 16px;">{{ $step['icon'] }}</span>
                        @else
                            <span style="color: white; font-size: 14px;">{{ $stepNum }}</span>
                        @endif
                    </div>

                    <!-- Connecting Line -->
                    @if($index < count($steps) - 1)
                        <div style="position: absolute; left: -24px; top: 32px; width: 2px; height: calc(100% - 32px); background: {{ $lineColor }};"></div>
                    @endif

                    <!-- Content -->
                    <div>
                        <h4 style="margin: 0 0 4px 0; font-size: 15px; color: {{ $isActive ? '#1f2937' : '#9ca3af' }}; font-weight: {{ $isActive ? '700' : '500' }};">
                            {{ $step['label'] }}
                            @if($isCurrent)
                                <span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 8px; font-weight: bold;">SAAT INI</span>
                            @endif
                        </h4>
                        <p style="margin: 0; font-size: 13px; color: {{ $isActive ? '#6b7280' : '#d1d5db' }};">{{ $step['desc'] }}</p>
                        @if($timestamp)
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #9ca3af;">
                                🕐 {{ \Carbon\Carbon::parse($timestamp)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Returned Status (if applicable) -->
            @if($isReturned)
                <div style="position: relative; padding-top: 40px;">
                    <div style="position: absolute; left: -24px; top: 0; width: 2px; height: 40px; background: #fee2e2;"></div>
                    <div style="position: absolute; left: -40px; top: 40px; width: 32px; height: 32px; border-radius: 50%; background: #dc2626; display: flex; align-items: center; justify-content: center; z-index: 2; box-shadow: 0 0 0 6px rgba(220, 38, 38, 0.15);">
                        <span style="color: white; font-size: 16px;">🔄</span>
                    </div>
                    <div style="padding-top: 40px;">
                        <h4 style="margin: 0 0 4px 0; font-size: 15px; color: #dc2626; font-weight: 700;">
                            Pengembalian
                            <span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 8px; font-weight: bold;">SAAT INI</span>
                        </h4>
                        <p style="margin: 0; font-size: 13px; color: #6b7280;">Pesanan sedang dalam proses pengembalian</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($order->delivery_proof)
        <!-- Delivery Proof Card -->
        <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; margin-bottom: 30px;">
            <h3 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                <span>📸 Bukti Foto Pengiriman</span>
            </h3>
            <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">Pesanan Anda telah sampai di lokasi tujuan. Berikut adalah bukti foto pengirimannya:</p>
            <div style="text-align: center; background: #f9fafb; padding: 15px; border-radius: 12px; border: 1px dashed #d1d5db;">
                <a href="{{ asset($order->delivery_proof) }}" target="_blank" style="display: inline-block;">
                    <img src="{{ asset($order->delivery_proof) }}" alt="Bukti Foto Pengiriman" style="max-width: 100%; max-height: 400px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                </a>
                <span style="display: block; font-size: 12px; color: #9ca3af; margin-top: 10px;">Klik gambar untuk memperbesar</span>
            </div>
        </div>
    @endif

    <!-- Order Items -->
    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; margin-bottom: 30px;">
        <h3 style="margin: 0 0 20px 0; color: #1f2937; font-size: 18px;">🛒 Produk yang Dibeli</h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($order->items as $item)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; background: #f9fafb; border-radius: 10px; border: 1px solid #f3f4f6;">
                    <div>
                        <div style="font-weight: 600; color: #1f2937; font-size: 14px;">{{ $item->product->name ?? $item->product->item_name ?? 'Produk' }}</div>
                        <div style="font-size: 13px; color: #6b7280; margin-top: 3px;">{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    </div>
                    <div style="font-weight: bold; color: #2e7d32; font-size: 14px;">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: bold; color: #374151; font-size: 15px;">Total Pembayaran</span>
            <span style="font-weight: bold; color: #2e7d32; font-size: 18px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Order Return Request / Status Section -->
    @if($order->return_reason || $order->tracking_status === 'returned')
        <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #fee2e2; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
                <h3 style="margin: 0; color: #991b1b; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                    <span>🔄</span> Status Pengajuan Pengembalian Pesanan (Retur)
                </h3>
                @php
                    $retStatus = $order->return_status ?? 'pending';
                    $badgeStyle = 'background: #fef3c7; color: #b45309; border: 1px solid #fde047;';
                    $statusText = '⏳ Dalam Peninjauan Admin';

                    if ($retStatus === 'reshipped' || $order->status === 'Pengiriman Pengganti') {
                        $badgeStyle = 'background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe;';
                        $statusText = '🚚 Produk Pengganti Sedang Dikirim';
                    } elseif ($retStatus === 'resolved' || ($retStatus === 'approved' && $order->tracking_status === 'completed')) {
                        $badgeStyle = 'background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;';
                        $statusText = '✅ Retur Selesai (Produk Pengganti Telah Diterima)';
                    } elseif ($retStatus === 'approved') {
                        $badgeStyle = 'background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;';
                        $statusText = '✅ Disetujui (Menunggu Pengiriman Pengganti)';
                    } elseif ($retStatus === 'rejected') {
                        $badgeStyle = 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;';
                        $statusText = '❌ Pengajuan Ditolak';
                    }
                @endphp
                <span style="{{ $badgeStyle }} padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: bold;">
                    {{ $statusText }}
                </span>
            </div>

            <div style="background: #fff5f5; padding: 18px; border-radius: 12px; border: 1px solid #fed7d7; margin-bottom: 20px;">
                <div style="font-size: 14px; color: #742a2a; margin-bottom: 8px;">
                    <strong>Alasan / Kendala Pengembalian:</strong>
                </div>
                <div style="font-size: 14.5px; color: #2d3748; white-space: pre-line; background: white; padding: 12px 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    {{ $order->return_reason ?: 'Tidak ada rincian alasan.' }}
                </div>

                @if($order->return_requested_at)
                    <div style="font-size: 12.5px; color: #718096; margin-top: 10px;">
                        🕐 Diajukan pada: {{ \Carbon\Carbon::parse($order->return_requested_at)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                    </div>
                @endif
            </div>

            @if($order->return_proof)
                <div style="margin-top: 15px;">
                    <strong style="font-size: 14px; color: #4a5568; display: block; margin-bottom: 8px;">📸 Foto Bukti Kendala Retur:</strong>
                    <div style="text-align: center; background: #f7fafc; padding: 12px; border-radius: 12px; border: 1px dashed #cbd5e0;">
                        <a href="{{ asset($order->return_proof) }}" target="_blank" style="display: inline-block;">
                            <img src="{{ asset($order->return_proof) }}" alt="Bukti Retur Pesanan" style="max-width: 100%; max-height: 350px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;">
                        </a>
                        <span style="display: block; font-size: 12px; color: #718096; margin-top: 6px;">Klik foto untuk melihat ukuran penuh</span>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Return Form Card -->
        <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                <h3 style="margin: 0; color: #1f2937; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                    <span>🔄</span> Ajukan Pengembalian Pesanan (Retur)
                </h3>
                <span style="background: #fef3c7; color: #b45309; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                    Jaga-jaga jika ada kendala
                </span>
            </div>

            <p style="margin: 0 0 20px 0; font-size: 13.5px; color: #6b7280; line-height: 1.5;">
                Apakah barang yang Anda terima bermasalah (rusak, basi, tidak sesuai, atau jumlah kurang)? Silakan isi formulir pengembalian di bawah ini agar tim kami dapat memproses penggantian atau pengembalian dana Anda.
            </p>

            <form action="/track-order/{{ rawurlencode($order->tracking_ticket_id) }}/return" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 18px;">
                @csrf

                <div>
                    <label for="return_category" style="display: block; font-weight: bold; font-size: 13.5px; color: #374151; margin-bottom: 6px;">
                        Kategori Kendala / Alasan Retur <span style="color: #dc2626;">*</span>
                    </label>
                    <select name="return_category" id="return_category" required style="width: 100%; padding: 11px 14px; border: 1px solid #d1d5db; border-radius: 10px; font-size: 14px; background-color: #f9fafb; color: #1f2937;">
                        <option value="" disabled selected>-- Pilih Kategori Kendala --</option>
                        <option value="Produk Rusak / Basi / Cacat">Produk Rusak / Basi / Cacat</option>
                        <option value="Jumlah Produk Kurang / Tidak Lengkap">Jumlah Produk Kurang / Tidak Lengkap</option>
                        <option value="Produk Tidak Sesuai Pesanan">Produk Tidak Sesuai Pesanan</option>
                        <option value="Salah Ukuran / Varian">Salah Ukuran / Varian</option>
                        <option value="Kendala Pengiriman / Kemasan Rusak">Kendala Pengiriman / Kemasan Rusak Parah</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="return_reason_details" style="display: block; font-weight: bold; font-size: 13.5px; color: #374151; margin-bottom: 6px;">
                        Penjelasan Detail Kendala <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea name="return_reason_details" id="return_reason_details" rows="4" required placeholder="Tuliskan secara jelas kendala produk yang diterima (misal: isi dodol terbuka/basi, jumlah kurang 1 bungkus, dll)..." style="width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 10px; font-size: 14px; font-family: inherit; color: #1f2937; background-color: #f9fafb; box-sizing: border-box; resize: vertical;"></textarea>
                </div>

                <div>
                    <label for="return_proof" style="display: block; font-weight: bold; font-size: 13.5px; color: #374151; margin-bottom: 6px;">
                        Unggah Foto Bukti Kendala (Opsional / Sangat Dianjurkan)
                    </label>
                    <input type="file" name="return_proof" id="return_proof" accept="image/*" onchange="previewReturnImage(event)" style="width: 100%; padding: 10px; border: 1px dashed #9ca3af; border-radius: 10px; background: #f9fafb; font-size: 13px; color: #4b5563; cursor: pointer;">
                    <small style="color: #6b7280; font-size: 12px; display: block; margin-top: 4px;">Format: JPG, PNG, WEBP, GIF. Maksimal 2 MB.</small>

                    <div id="returnProofPreviewContainer" style="display: none; margin-top: 12px; text-align: center; background: #f3f4f6; padding: 10px; border-radius: 10px;">
                        <span style="font-size: 12px; color: #4b5563; font-weight: bold; display: block; margin-bottom: 6px;">Preview Foto Bukti:</span>
                        <img id="returnProofPreviewImg" src="" alt="Preview" style="max-width: 100%; max-height: 220px; border-radius: 8px; border: 1px solid #d1d5db;">
                    </div>
                </div>

                <div style="text-align: right; margin-top: 6px;">
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengajukan pengembalian untuk pesanan ini?')" style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; border: none; padding: 12px 28px; border-radius: 10px; font-weight: bold; font-size: 14.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <span>📤</span> Kirim Pengajuan Pengembalian
                    </button>
                </div>
            </form>
        </div>

        <script>
            function previewReturnImage(event) {
                const container = document.getElementById('returnProofPreviewContainer');
                const img = document.getElementById('returnProofPreviewImg');
                const file = event.target.files[0];

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        container.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    container.style.display = 'none';
                    img.src = '';
                }
            }
        </script>
    @endif

    <!-- Rating Button (only for completed orders) -->
    @if($order->tracking_status === 'completed' && $order->user_id)
        <div style="text-align: center; margin-bottom: 30px;">
            <a href="/customer/orders/{{ $order->id }}/rate" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-size: 15px; font-weight: bold; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(245, 158, 11, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.3)'">
                ⭐ Beri Rating & Ulasan
            </a>
        </div>
    @endif

</div>

<style>
    @keyframes pulse-glow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }

    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
