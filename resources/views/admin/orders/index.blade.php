@extends('admin.layout')

@section('content')

<div class="admin-card">

    <div class="admin-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
            <h1 class="admin-title" style="margin: 0; font-size: 22px; color: #166534; font-weight: 800;">
                📦 Daftar Pesanan Pelanggan
            </h1>
            <small style="color: #64748b; font-size: 13px; font-weight: 600; display: block; margin-top: 4px;">
                ℹ️ Kasir dapat memverifikasi pembayaran Transfer Bank dan memantau status pesanan pelanggan.
            </small>
        </div>
        <a href="/courier/deliveries" class="btn btn-success" style="padding: 9px 18px; font-weight: bold; border-radius: 8px;">
            🛵 Ke Panel Pengiriman Kurir
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border: 1px solid #c3e6cb; display: flex; align-items: center; gap: 8px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No Order</th>
                    <th>Pembeli</th>
                    <th>Telepon</th>
                    <th>Total</th>
                    <th>Status Pengiriman</th>
                    <th>Metode & Status Bayar</th>
                    <th>Verifikasi Kasir</th>
                    <th>Tanggal</th>
                </tr>
            </thead>

            <tbody>
            @forelse($orders as $order)
            <tr>
                <td>
                    <span style="font-family: monospace; background: #fef3c7; color: #92400e; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 800; border: 1px solid #fde047; display: inline-block; margin-bottom: 3px;">
                        ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                    </span><br>
                    <strong>#{{ $order->order_number }}</strong><br>
                    @if($order->tracking_ticket_id)
                        <div style="margin-top:4px; font-size:11px; background:#e3f2fd; padding:3px 6px; border-radius:4px; display:inline-block; border:1px solid #90caf9; cursor:pointer;" title="Klik untuk salin" onclick="navigator.clipboard.writeText('{{ $order->tracking_ticket_id }}'); alert('Tiket disalin: {{ $order->tracking_ticket_id }}');">
                            🎫 Tiket: {{ $order->tracking_ticket_id }}
                        </div>
                    @endif
                </td>

                <td style="font-weight: 600;">{{ $order->customer_name }}</td>

                <td>{{ $order->customer_phone }}</td>

                <td style="font-weight: bold; color: #166534;">
                    Rp {{ number_format($order->total_amount,0,',','.') }}
                </td>

                <td>
                    @php
                        $isPickup = (empty($order->delivery_address) || str_contains(strtolower($order->delivery_option ?? ''), 'ambil di toko') || str_contains(strtolower($order->delivery_option ?? ''), 'pickup'));
                        $currentStatus = $order->status ?? 'Menunggu Kurir';
                        $badgeBg = '#f0fdf4';
                        $badgeColor = '#166534';
                        $badgeIcon = '📦';

                        if ($isPickup) {
                            $badgeBg = '#e0f2fe'; $badgeColor = '#0369a1'; $badgeIcon = '🏪';
                            $statusLabel = 'Ambil di Toko';
                        } elseif ($currentStatus == 'Menunggu Kurir' || $currentStatus == 'pending') {
                            $badgeBg = '#fef3c7'; $badgeColor = '#b45309'; $badgeIcon = '⏳';
                            $statusLabel = 'Menunggu Kurir';
                        } elseif ($currentStatus == 'Diambil Kurir') {
                            $badgeBg = '#dbeafe'; $badgeColor = '#1e40af'; $badgeIcon = '🎒';
                            $statusLabel = 'Diambil Kurir';
                        } elseif ($currentStatus == 'Dalam Perjalanan' || $currentStatus == 'shipped' || $currentStatus == 'almost_arrived') {
                            $badgeBg = '#e0e7ff'; $badgeColor = '#4338ca'; $badgeIcon = '🛵';
                            $statusLabel = 'Dalam Perjalanan';
                        } elseif ($currentStatus == 'Terkirim' || $currentStatus == 'completed' || $currentStatus == 'Selesai') {
                            $badgeBg = '#d1fae5'; $badgeColor = '#065f46'; $badgeIcon = '✅';
                            $statusLabel = 'Terkirim';
                        } else {
                            $statusLabel = ucfirst($currentStatus);
                        }
                    @endphp
                    <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 6px 12px; border-radius: 12px; font-weight: bold; font-size: 12px; display: inline-block; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        {{ $badgeIcon }} {{ $statusLabel }}
                    </span>
                </td>

                <td>
                    @if($order->payment_method === 'transfer_bank')
                        <div style="font-size: 11px; font-weight: bold; color: #0284c7; margin-bottom: 4px;">
                            🏦 Transfer Bank (BCA)
                        </div>
                        @if($order->payment_status == 'paid')
                            <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; display: inline-block;">
                                ✅ Paid (Lunas)
                            </span>
                        @elseif($order->payment_status == 'pending_verification')
                            <span style="background: #fffbeb; color: #b45309; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; border: 1px solid #fde047; display: inline-block;">
                                ⏳ Verifikasi Kasir
                            </span>
                        @elseif($order->payment_status == 'failed')
                            <span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; display: inline-block;">
                                ❌ Ditolak
                            </span>
                        @else
                            <span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; display: inline-block;">
                                Unpaid
                            </span>
                        @endif
                    @else
                        <div style="font-size: 11px; font-weight: bold; color: #64748b; margin-bottom: 4px;">
                            💳 Midtrans Online
                        </div>
                        @if($order->payment_status == 'paid')
                            <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; display: inline-block;">Paid</span>
                        @else
                            <span style="background: #f8d7da; color: #721c24; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; display: inline-block;">Unpaid</span>
                        @endif
                    @endif
                </td>

                <td>
                    @if($order->payment_method === 'transfer_bank')
                        <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-start;">
                            @if($order->transfer_proof)
                                <button type="button" class="btn" onclick="showProofModal('{{ asset($order->transfer_proof) }}', '{{ $order->order_number }}')" style="padding: 4px 10px; font-size: 11.5px; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: bold; border-radius: 6px; cursor: pointer;">
                                    📸 Lihat Struk
                                </button>
                            @else
                                <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Belum ada struk</span>
                            @endif

                            @if($order->payment_status !== 'paid')
                                <form action="/admin/orders/{{ $order->id }}/verify-payment" method="POST" style="margin: 0; display: inline-block;">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success" style="padding: 5px 10px; font-size: 11.5px; font-weight: bold; background: #16a34a; color: white; border: none; border-radius: 6px; cursor: pointer;" onclick="return confirm('Apakah Anda yakin ingin menyetujui & mengkonfirmasi pembayaran transfer bank pesanan #{{ $order->order_number }} ini?');">
                                        ✅ Setujui Pembayaran
                                    </button>
                                </form>
                                <button type="button" class="btn" onclick="openDiscrepancyModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ addslashes($order->customer_name) }}', '{{ $order->customer_phone }}', '{{ $order->total_amount }}')" style="padding: 4px 10px; font-size: 11px; font-weight: bold; background: #f59e0b; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                    ⚠️ Laporkan Kendala / Chat
                                </button>
                            @else
                                <span style="font-size: 11.5px; color: #166534; font-weight: bold;">✅ Terverifikasi</span>
                                <button type="button" class="btn" onclick="openDiscrepancyModal('{{ $order->id }}', '{{ $order->order_number }}', '{{ addslashes($order->customer_name) }}', '{{ $order->customer_phone }}', '{{ $order->total_amount }}')" style="padding: 3px 8px; font-size: 10.5px; font-weight: bold; background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; margin-top: 2px;">
                                    💬 Chat Pelanggan
                                </button>
                            @endif
                        </div>
                    @else
                        <span style="color: #94a3b8; font-size: 12px;">Otomatis (Midtrans)</span>
                    @endif
                </td>

                <td style="font-size: 12px; color: #64748b;">
                    {{ \Carbon\Carbon::parse($order->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 30px; color: #888;">
                    Belum ada pesanan
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- Modal Pratinjau Struk Transfer Bank -->
<div id="proofModal" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center; padding:16px;">
    <div style="background:white; border-radius:16px; max-width:450px; width:100%; padding:24px; text-align:center; box-shadow:0 20px 40px rgba(0,0,0,0.3);">
        <h3 id="proofModalTitle" style="margin-top:0; color:#1e293b; font-size:16px; font-weight:bold;">📸 Struk Transfer Bank</h3>
        <div style="margin: 16px 0; max-height: 350px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px;">
            <img id="proofModalImg" src="" alt="Bukti Transfer" style="max-width:100%; height:auto; border-radius:6px;">
        </div>
        <button type="button" onclick="closeProofModal()" class="btn" style="padding: 8px 20px; font-weight: bold; background: #64748b; color: white; border: none; border-radius: 8px; cursor: pointer;">Tutup</button>
    </div>
</div>

<script>
function showProofModal(imgSrc, orderNum) {
    document.getElementById('proofModalTitle').innerText = '📸 Struk Transfer Bank #' + orderNum;
    document.getElementById('proofModalImg').src = imgSrc;
    document.getElementById('proofModal').style.display = 'flex';
}
function closeProofModal() {
    document.getElementById('proofModal').style.display = 'none';
}
</script>

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