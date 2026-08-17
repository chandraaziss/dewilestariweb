@extends('layouts.app')

@section('content')

<div class="tracking-container" style="max-width: 600px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; color: #333; margin-bottom: 20px;">Lacak Pesanan Anda</h2>
    <p style="text-align: center; color: #666; margin-bottom: 30px;">Masukkan ID Tiket Pelacakan yang Anda dapatkan setelah melakukan pembayaran.</p>

    <form onsubmit="submitTrackOrderDedicated(event)" style="display: flex; gap: 10px; margin-bottom: 30px;">
        <input type="text" id="dedicatedTrackingTicketId" placeholder="Contoh: TRK-123456" required 
               style="flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">
        <button type="submit" style="padding: 12px 25px; background: #28a745; color: #fff; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
            Lacak
        </button>
    </form>

    <div id="dedicatedTrackingResult" style="padding: 20px; background: #f8f9fa; border-radius: 5px; border: 1px solid #eee; min-height: 100px; display: none;">
        <!-- Hasil pelacakan akan muncul di sini -->
    </div>
</div>

<script>
function submitTrackOrderDedicated(event) {
    event.preventDefault();
    const ticketId = document.getElementById('dedicatedTrackingTicketId').value.trim();
    if(!ticketId) return;

    const resultDiv = document.getElementById('dedicatedTrackingResult');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div style="text-align: center; color: #666;">Sedang mencari...</div>';

    fetch('/track-order/' + encodeURIComponent(ticketId), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                let statusColor = '#f39c12';
                if(data.status_label === 'Selesai') statusColor = '#28a745';
                if(data.status_label === 'Pengembalian') statusColor = '#dc3545';
                
                resultDiv.innerHTML = `
                    <h3 style="margin-top:0; color:#333;">Status Pesanan</h3>
                    <p style="margin:5px 0;"><strong>No Tiket:</strong> ${ticketId}</p>
                    <p style="margin:5px 0;"><strong>Nama:</strong> ${data.customer_name}</p>
                    <p style="margin:5px 0;"><strong>No Order:</strong> ${data.order_number}</p>
                    <p style="margin:5px 0;"><strong>Status:</strong> <span style="color: ${statusColor}; font-weight: bold; font-size: 18px;">${data.status_label}</span></p>
                    ${data.estimated_delivery ? `<p style="margin:8px 0; background:#e8f5e9; padding:8px 12px; border-radius:6px; color:#1b5e20; font-size:14px;"><strong>📅 Estimasi Pengiriman:</strong> ${data.estimated_delivery}</p>` : ''}
                    <a href="/track-order/${encodeURIComponent(ticketId)}" style="display:inline-block; margin-top:15px; padding:10px 20px; background:#2e7d32; color:white; text-decoration:none; border-radius:5px; font-weight:bold;">Lihat Detail Pelacakan →</a>
                `;
            } else {
                resultDiv.innerHTML = '<div style="color: #dc3545; text-align: center;"><strong>Error:</strong> ' + (data.message || 'Tiket tidak ditemukan') + '</div>';
            }
        })
        .catch(err => {
            resultDiv.innerHTML = '<div style="color: #dc3545; text-align: center;"><strong>Error:</strong> Gagal terhubung ke server</div>';
        });
}
</script>

@endsection
