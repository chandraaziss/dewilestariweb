@extends('admin.layout')

@section('content')
<style>
    .ticket-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        max-width: 750px;
        margin: 20px auto;
        padding: 36px;
        border: 1px solid #e2e8f0;
    }
    .ticket-header {
        text-align: center;
        border-bottom: 2px dashed #cbd5e1;
        padding-bottom: 20px;
        margin-bottom: 24px;
    }
    .ticket-title {
        font-size: 22px;
        font-weight: 800;
        color: #b91c1c;
        margin: 0 0 6px 0;
    }
    .meta-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        color: #475569;
    }
    .detail-box {
        background: #fff5f5;
        border: 1px solid #fecaca;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
    }
    .wa-btn {
        display: block;
        width: 100%;
        background: #25d366;
        color: white;
        text-align: center;
        padding: 16px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(37, 211, 102, 0.4);
        transition: transform 0.2s, background 0.2s;
    }
    .wa-btn:hover {
        background: #20ba5a;
        transform: translateY(-2px);
        color: white;
    }
</style>

<div class="ticket-card">
    <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;" class="no-print">
        <a href="/admin/suppliers" style="color: #64748b; text-decoration: none; font-weight: bold; font-size: 14px;">← Kembali ke Manajemen Supplier</a>
        <button onclick="window.print()" style="padding: 7px 15px; font-weight: bold; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
            🖨️ Cetak / Download Surat Retur (PDF)
        </button>
    </div>

    @if(session('success'))
        <div style="padding: 14px; background: #dcfce7; color: #166534; border-radius: 10px; margin-bottom: 20px; border: 1px solid #86efac; font-weight: bold;" class="no-print">
            {{ session('success') }}
        </div>
    @endif

    <div class="ticket-header">
        <h1 class="ticket-title">📄 SURAT BUKTI RETUR PRODUK SUPPLIER</h1>
        <p style="color: #64748b; margin: 0; font-size: 14px;">Dokumen Resmi Pengembalian / Retur Produk Tidak Sesuai (Toko Dewi Lestari 2)</p>
    </div>

    <div class="meta-row">
        <span>No. Tiket Retur:</span>
        <strong style="color: #b91c1c; font-size: 16px;">#{{ $return->return_number }}</strong>
    </div>
    <div class="meta-row">
        <span>Tanggal Pengajuan:</span>
        <strong>{{ $return->created_at ? $return->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB</strong>
    </div>
    <div class="meta-row">
        <span>Mitra Supplier:</span>
        <strong>{{ $supplier->name ?? 'Supplier' }} ({{ $supplier->phone ?? '-' }})</strong>
    </div>

    <div class="detail-box">
        <h3 style="margin: 0 0 14px 0; font-size: 16px; color: #991b1b; border-bottom: 1px solid #fca5a5; padding-bottom: 8px;">
            📦 Detail Barang Ditinggalkan / Retur ({{ count($relatedReturns ?? [$return]) }} Item)
        </h3>
        
        @foreach($relatedReturns ?? [$return] as $index => $item)
            <div style="padding: 14px; background: #ffffff; border-radius: 10px; border: 1px solid #fca5a5; margin-bottom: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="font-weight: bold; color: #991b1b; margin-bottom: 8px; font-size: 15px; display: flex; justify-content: space-between;">
                    <span>Item #{{ $index + 1 }}: {{ $item->item_name }}</span>
                    <span style="color: #dc2626;">{{ $item->quantity }} bungkus</span>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                    <tr>
                        <td style="padding: 4px 0; color: #475569; width: 140px;">Ukuran / Varian:</td>
                        <td style="padding: 4px 0; font-weight: bold; color: #0f172a;">{{ $item->weight ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #475569;">Jumlah Retur:</td>
                        <td style="padding: 4px 0; font-weight: bold; color: #b91c1c; font-size: 15px;">{{ $item->quantity }} bungkus</td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #475569; vertical-align: top;">Alasan Kendala:</td>
                        <td style="padding: 4px 0; font-weight: 600; color: #334155; line-height: 1.5;">{{ $item->reason }}</td>
                    </tr>
                    @if($item->proof_image)
                    <tr>
                        <td style="padding: 8px 0; color: #475569; vertical-align: top;">Foto Bukti:</td>
                        <td style="padding: 8px 0;">
                            <img src="{{ asset($item->proof_image) }}" alt="Bukti Retur" style="max-width: 180px; border-radius: 8px; border: 1px solid #cbd5e1;">
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        @endforeach
    </div>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
        <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 8px; font-size: 13px;">📝 Preview Pesan WhatsApp ke Supplier:</label>
        <pre style="white-space: pre-wrap; font-family: inherit; font-size: 13px; color: #1e293b; margin: 0; background: white; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1;">{{ $message }}</pre>
    </div>

    <a href="{{ $waUrl }}" target="_blank" class="wa-btn">
        📱 Kirim Pengajuan Retur ke WhatsApp Supplier ({{ $supplier->phone ?? '' }})
    </a>
</div>
@endsection
