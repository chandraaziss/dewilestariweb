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

        <div style="margin-top: 15px; padding: 12px 16px; background: #fffbeb; border: 1px solid #fde047; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong style="color: #b45309; font-size: 14px;">💬 Notifikasi & Chat Pelanggan:</strong>
                <div style="font-size: 12px; color: #78350f;">Kirim pesan pemberitahuan kendala transfer belum masuk / kurang bayar ke pelanggan.</div>
            </div>
            <button type="button" onclick="openDiscrepancyModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ addslashes($order->customer_name) }}', '{{ $order->customer_phone }}', '{{ $order->total_amount }}')" style="padding: 8px 16px; font-weight: bold; background: #d97706; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                ⚠️ Laporkan Kendala Bayar / Chat Pelanggan
            </button>
        </div>

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
            </tr>
        @endforeach
        </tbody>

    </table>

    <br>

    <a href="javascript:history.back()" class="btn-edit">
        ← Kembali
    </a>

<!-- Modal Laporkan Kendala Pembayaran / Chat Pelanggan -->
<div id="discrepancyModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; justify-content:center; align-items:center; padding:16px;">
    <div style="background:white; border-radius:16px; max-width:520px; width:100%; padding:24px; text-align:left; box-shadow:0 20px 40px rgba(0,0,0,0.3); font-family:sans-serif;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:16px;">
            <h3 style="margin:0; color:#b45309; font-size:17px; font-weight:800; display:flex; align-items:center; gap:8px;">
                ⚠️ Laporkan Kendala Bayar / Chat Pelanggan
            </h3>
            <button type="button" onclick="closeDiscrepancyModal()" style="background:none; border:none; font-size:18px; cursor:pointer; color:#64748b;">✖</button>
        </div>

        <form id="discrepancyForm" method="POST" action="">
            @csrf
            <div style="margin-bottom:14px; background:#fef3c7; padding:12px; border-radius:8px; border:1px solid #fde047; font-size:13px; color:#78350f; line-height:1.5;">
                <strong>Pesanan:</strong> <span id="discOrderNum">#ORD-xxx</span><br>
                <strong>Pelanggan:</strong> <span id="discCustName">Nama Pelanggan</span> (<span id="discCustPhone">08123456789</span>)<br>
                <strong>Total Tagihan:</strong> <span id="discTotalAmount" style="font-weight:bold; color:#b45309;">Rp 0</span>
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-weight:bold; font-size:13px; color:#1e293b; display:block; margin-bottom:6px;">Pilih Jenis Kendala Pembayaran:</label>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label style="display:flex; align-items:flex-start; gap:8px; font-size:13px; background:#fff1f2; padding:10px; border-radius:8px; border:1px solid #fecdd3; cursor:pointer;">
                        <input type="radio" name="discrepancy_type" value="not_received" checked onchange="updateDiscrepancyTemplate()" style="margin-top:2px;">
                        <div>
                            <strong style="color:#e11d48;">🔴 Belum Ada Transfer / Transfer Belum Masuk</strong>
                            <div style="font-size:11.5px; color:#9f1239;">Pelanggan belum melakukan transfer / dana belum diterima di rekening.</div>
                        </div>
                    </label>

                    <label style="display:flex; align-items:flex-start; gap:8px; font-size:13px; background:#fffbeb; padding:10px; border-radius:8px; border:1px solid #fef08a; cursor:pointer;">
                        <input type="radio" name="discrepancy_type" value="insufficient" onchange="updateDiscrepancyTemplate()" style="margin-top:2px;">
                        <div>
                            <strong style="color:#b45309;">⚠️ Jumlah Transfer Kurang (Kurang Bayar)</strong>
                            <div style="font-size:11.5px; color:#78350f;">Nominal yang ditransfer pelanggan kurang dari total tagihan pesanan.</div>
                        </div>
                    </label>

                    <label style="display:flex; align-items:flex-start; gap:8px; font-size:13px; background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0; cursor:pointer;">
                        <input type="radio" name="discrepancy_type" value="custom" onchange="updateDiscrepancyTemplate()" style="margin-top:2px;">
                        <div>
                            <strong style="color:#334155;">✏️ Pesan Kustom / Lainnya</strong>
                            <div style="font-size:11.5px; color:#64748b;">Tuliskan pesan pemberitahuan kustom secara bebas.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-weight:bold; font-size:13px; color:#1e293b; display:block; margin-bottom:6px;">Pratinjau & Isi Pesan Pemberitahuan:</label>
                <textarea name="message" id="discrepancyMessage" rows="5" required style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px; font-size:13px; font-family:sans-serif; box-sizing:border-box; outline:none; resize:vertical;"></textarea>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
                <button type="button" onclick="sendViaWhatsApp()" style="background:#25d366; color:white; border:none; padding:9px 15px; border-radius:8px; font-weight:bold; font-size:12.5px; cursor:pointer; display:flex; align-items:center; gap:6px;">
                    📲 Kirim via WhatsApp Direct
                </button>
                <button type="submit" style="background:#d97706; color:white; border:none; padding:9px 18px; border-radius:8px; font-weight:bold; font-size:12.5px; cursor:pointer; display:flex; align-items:center; gap:6px;">
                    💬 Kirim ke Live Chat Website
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentDiscOrder = null;

function openDiscrepancyModal(orderId, orderNum, custName, custPhone, totalAmount) {
    currentDiscOrder = { id: orderId, orderNum: orderNum, name: custName, phone: custPhone, total: totalAmount };
    document.getElementById('discrepancyForm').action = '/admin/orders/' + orderId + '/notify-discrepancy';
    document.getElementById('discOrderNum').innerText = '#' + orderNum;
    document.getElementById('discCustName').innerText = custName;
    document.getElementById('discCustPhone').innerText = custPhone;
    document.getElementById('discTotalAmount').innerText = 'Rp ' + Number(totalAmount).toLocaleString('id-ID');

    const radios = document.querySelectorAll('input[name="discrepancy_type"]');
    if (radios.length > 0) radios[0].checked = true;
    updateDiscrepancyTemplate();
    document.getElementById('discrepancyModal').style.display = 'flex';
}

function closeDiscrepancyModal() {
    document.getElementById('discrepancyModal').style.display = 'none';
}

function updateDiscrepancyTemplate() {
    if (!currentDiscOrder) return;
    const selectedRadio = document.querySelector('input[name="discrepancy_type"]:checked');
    if (!selectedRadio) return;
    const selectedType = selectedRadio.value;
    const msgBox = document.getElementById('discrepancyMessage');
    const formattedTotal = 'Rp ' + Number(currentDiscOrder.total).toLocaleString('id-ID');

    if (selectedType === 'not_received') {
        msgBox.value = `[Toko Dewi Lestari 2]\nHalo Kak ${currentDiscOrder.name},\n\nTerima kasih telah berbelanja. Mengenai pesanan #${currentDiscOrder.orderNum} sebesar ${formattedTotal}, setelah kami periksa mutasi rekening, dana transfer ternyata BELUM MASUK / belum kami terima.\n\nMohon periksa kembali transaksi bank Anda atau silakan kirimkan ulang bukti transfer yang valid agar pesanan dapat diproses. Terima kasih! 🙏`;
    } else if (selectedType === 'insufficient') {
        msgBox.value = `[Toko Dewi Lestari 2]\nHalo Kak ${currentDiscOrder.name},\n\nTerima kasih telah berbelanja. Mengenai pesanan #${currentDiscOrder.orderNum}, total tagihan adalah ${formattedTotal}. Namun nominal transfer yang masuk ke rekening kami MASIH KURANG.\n\nMohon melakukan transfer kekurangannya dan mengonfirmasikan kepada kami agar pesanan dapat segera diproses & dikirim. Terima kasih! 🙏`;
    } else if (selectedType === 'custom') {
        msgBox.value = `[Toko Dewi Lestari 2]\nHalo Kak ${currentDiscOrder.name},\n\nTerkait pesanan #${currentDiscOrder.orderNum} (${formattedTotal}): `;
    }
}

function sendViaWhatsApp() {
    if (!currentDiscOrder || !currentDiscOrder.phone) {
        alert('Nomor WhatsApp pelanggan tidak tersedia');
        return;
    }
    const msg = document.getElementById('discrepancyMessage').value;
    let cleanPhone = currentDiscOrder.phone.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '62' + cleanPhone.substring(1);
    }
    const waUrl = 'https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(msg);
    window.open(waUrl, '_blank');
}
</script>

@endsection