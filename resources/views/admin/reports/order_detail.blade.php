@extends('layouts.app')

@section('content')
<style>
    .invoice-document {
        max-width: 950px;
        margin: 20px auto 40px;
        background: #ffffff;
        padding: 40px;
        border: 1px solid #111827;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #000000;
        border-radius: 8px;
    }

    .invoice-top-kop {
        display: flex;
        justify-content: space-between;
    /* Kop Surat Formal Premium Gold-Emerald Stripe */
    .report-header-kop {
        display: flex;
        align-items: center;
        gap: 24px;
        padding-bottom: 12px;
    }

    .kop-logo-wrapper {
        flex-shrink: 0;
    }

    .brand-logo-circle {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: radial-gradient(circle, #ffeb3b 0%, #facc15 70%, #eab308 100%);
        border: 4px solid #15803d;
        box-shadow: 0 6px 14px rgba(21, 128, 61, 0.25), inset 0 0 10px rgba(255, 255, 255, 0.8);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #15803d;
        text-align: center;
        font-weight: 900;
        line-height: 1;
    }

    .brand-logo-circle .text-dl-icon {
        font-size: 28px;
        font-weight: 900;
        color: #15803d;
        letter-spacing: -1px;
        text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.9);
    }

    .brand-logo-circle .text-sub {
        font-size: 8.5px;
        font-weight: 800;
        color: #166534;
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kop-info-wrapper {
        flex-grow: 1;
        text-align: right;
    }

    .store-name {
        margin: 0;
        font-size: 26px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: 1px;
        text-transform: uppercase;
        line-height: 1.1;
    }

    .store-tagline {
        font-size: 12px;
        font-weight: 700;
        color: #15803d;
        margin-top: 3px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .store-address {
        margin: 4px 0 0;
        font-size: 12px;
        color: #334155;
        line-height: 1.4;
    }

    .store-contacts {
        font-size: 11.5px;
        color: #475569;
        margin-top: 4px;
        font-weight: 600;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .store-contacts .dot {
        color: #15803d;
        font-weight: bold;
    }

    /* Garis Kop Ganda Emas - Hijau */
    .kop-stripe-divider {
        margin-top: 8px;
        margin-bottom: 24px;
    }

    .kop-stripe-divider .stripe-thick {
        height: 5px;
        background: linear-gradient(90deg, #15803d 0%, #166534 60%, #eab308 100%);
        border-radius: 2px;
    }

    .kop-stripe-divider .stripe-gold {
        height: 2px;
        background: #eab308;
        margin-top: 3px;
        border-radius: 1px;
    }

    /* Banner Judul Laporan */
    .invoice-title-banner {
        text-align: center;
        margin-bottom: 24px;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        padding: 14px 20px;
        border-radius: 10px;
        border: 1px solid #86efac;
        border-left: 6px solid #15803d;
        box-shadow: 0 2px 8px rgba(21, 128, 61, 0.06);
    }

    .invoice-title-banner h1 {
        font-size: 19px;
        font-weight: 900;
        letter-spacing: 8px;
        text-transform: uppercase;
        margin: 0;
        color: #0f172a;
    }

    .invoice-title-banner .report-doc-code {
        font-size: 12px;
        color: #166534;
        font-weight: 600;
        margin-top: 4px;
        letter-spacing: 0.5px;
    }

    .invoice-top-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1fr;
        border: 2px solid #000000;
        margin-bottom: 24px;
    }

    .invoice-top-box {
        padding: 14px;
        font-size: 11.5px;
        line-height: 1.6;
        border-right: 1px solid #000000;
    }

    .invoice-top-box:last-child {
        border-right: none;
    }

    .box-heading {
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 8px;
        font-size: 12px;
        display: block;
        text-transform: uppercase;
    }

    .po-table-info {
        width: 100%;
        border-collapse: collapse;
    }

    .po-table-info td {
        padding: 2px 0;
        font-size: 11.5px;
        vertical-align: top;
    }

    .po-grand-total {
        margin-top: 10px;
        padding-top: 6px;
        border-top: 1px solid #000000;
        font-weight: bold;
        font-size: 12.5px;
    }

    .invoice-main-table {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid #000000;
        margin-bottom: 0;
    }

    .invoice-main-table th {
        border: 1px solid #000000;
        padding: 10px 8px;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        text-align: center;
        background: #f8fafc;
    }

    .invoice-main-table td {
        border: 1px solid #000000;
        padding: 10px 8px;
        font-size: 11.5px;
    }

    @media print {
        body {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .no-print, header, nav, .sidebar, .footer {
            display: none !important;
        }
        .invoice-document {
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
            padding: 20px !important;
            max-width: 100% !important;
        }
    }
</style>

<div class="admin-container" style="max-width: 1000px; margin: 0 auto; padding: 25px 20px;">

    {{-- Breadcrumb & Back Link (Hidden in Print) --}}
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <a href="/admin/reports/store" style="display: inline-flex; align-items: center; gap: 6px; color: #166534; font-weight: 700; text-decoration: none; font-size: 14px; background: #e8f5e9; padding: 8px 16px; border-radius: 8px; border: 1px solid #c8e6c9;">
            ← Kembali ke Laporan Penjualan Toko
        </a>
        <button onclick="window.print()" style="display: inline-flex; align-items: center; gap: 6px; color: white; font-weight: 700; font-size: 13.5px; background: #0284c7; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);">
            🖨️ Cetak Rincian Ini
        </button>
    </div>

    {{-- Formal Document Container (Formatted like Invoice) --}}
    <div class="invoice-document">
        
        {{-- Kop Surat Formal Premium Gold-Emerald Stripe --}}
        <div class="report-header-kop">
            <div class="kop-logo-wrapper">
                <div class="brand-logo-circle">
                    <div class="text-dl-icon">DL</div>
                    <div class="text-sub">Dewi Lestari 2</div>
                </div>
            </div>
            <div class="kop-info-wrapper">
                <h2 class="store-name">TOKO DEWI LESTARI 2</h2>
                <div class="store-tagline">Pusat Oleh-Oleh & Kuliner Khas Bandung / Jawa Barat</div>
                <p class="store-address">Jl. Raya Cimindi No.59, Pasirkaliki, Kec. Cimahi Utara, Kota Cimahi, Jawa Barat 40535</p>
                <div class="store-contacts">
                    <span>📞 0812-2195-6759</span>
                    <span class="dot">•</span>
                    <span>✉️ info@dewilestari2.com</span>
                    <span class="dot">•</span>
                    <span>🌐 www.dewilestari2.com</span>
                </div>
            </div>
        </div>

        {{-- Garis Kop Ganda Emas - Hijau --}}
        <div class="kop-stripe-divider">
            <div class="stripe-thick"></div>
            <div class="stripe-gold"></div>
        </div>

        {{-- Banner Judul Laporan --}}
        <div class="invoice-title-banner">
            <h1>R I N C I A N   P R O D U K   D I B E L I</h1>
            <div class="report-doc-code">Dokumen Resmi Rekapitulasi Rincian Pembelian Pelanggan</div>
        </div>

        {{-- Metadata Info Grid Box --}}
        <div class="invoice-top-grid">
            <div class="invoice-top-box">
                <span class="box-heading">INFORMASI PELANGGAN :</span>
                <strong>{{ $order->customer_name }}</strong><br>
                No. HP / WA: {{ $order->customer_phone ?: '-' }}<br>
                Alamat Kirim: {{ $order->delivery_address ?: 'Ambil di Toko / Tanpa Alamat' }}
            </div>

            <div class="invoice-top-box">
                <span class="box-heading">DETAIL TRANSAKSI :</span>
                <table class="po-table-info">
                    <tr>
                        <td style="width: 105px;">Nomor Pesanan</td>
                        <td>: <strong>{{ $order->order_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>Tanggal Bayar</td>
                        <td>: {{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->translatedFormat('d F Y, H:i') . ' WIB' : ($order->created_at ? $order->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-') }}</td>
                    </tr>
                    <tr>
                        <td>Metode Pengiriman</td>
                        <td>: {{ $order->delivery_option ?: 'Kurir Toko' }}</td>
                    </tr>
                </table>
                <div class="po-grand-total">
                    TOTAL PEMBAYARAN: <span style="color: #15803d;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="invoice-top-box">
                <span class="box-heading">STATUS PEMBAYARAN :</span>
                <div style="background: #dcfce7; color: #15803d; border: 1.5px solid #86efac; padding: 8px 12px; border-radius: 6px; font-weight: bold; font-size: 13px; text-align: center; margin-top: 6px;">
                    ✅ LUNAS (PAID)
                </div>
                <div style="font-size: 11px; color: #64748b; margin-top: 8px; text-align: center;">
                    Metode: <strong>{{ strtoupper($order->payment_method ?? 'ONLINE') }}</strong>
                </div>
            </div>
        </div>

        {{-- Detail Products Table --}}
        <table class="invoice-main-table">
            <thead>
                <tr>
                    <th style="width: 45px;">NO.</th>
                    <th>ITEM PRODUK</th>
                    <th style="width: 130px;">UKURAN VARIAN</th>
                    <th style="width: 130px;">HARGA SATUAN</th>
                    <th style="width: 110px;">QTY DIPESAN</th>
                    <th style="width: 140px;">TOTAL BAYAR</th>
                </tr>
            </thead>
            <tbody>
                @php $totalQty = 0; @endphp
                @foreach($order->items as $idx => $item)
                    @php
                        $pName = $item->product->name ?? $item->product->item_name ?? $item->item_name ?? 'Produk';
                        $w = !empty($item->weight) ? $item->weight : ($item->product->weight ?? '-');
                        $totalQty += $item->qty;
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                        <td style="font-weight: bold;">{{ $pName }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $w }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $item->qty }} bungkus</td>
                        <td style="text-align: right; font-weight: bold; color: #15803d;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: bold; border-top: 2px solid #000000;">
                    <td colspan="4" style="text-align: right; font-size: 12px; padding: 10px;">TOTAL KESELURUHAN PRODUK:</td>
                    <td style="text-align: center; font-size: 12.5px; padding: 10px; color: #15803d;">{{ $totalQty }} bungkus</td>
                    <td style="text-align: right; font-size: 13.5px; padding: 10px; color: #15803d;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Bottom Actions (Hidden in Print) --}}
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 40px;">
        <a href="/admin/reports/store" style="display: inline-flex; align-items: center; gap: 6px; color: #475569; font-weight: 700; text-decoration: none; font-size: 13.5px; background: white; padding: 10px 20px; border-radius: 8px; border: 1px solid #cbd5e1;">
            ← Kembali ke Laporan Penjualan
        </a>
        <button onclick="window.print()" style="display: inline-flex; align-items: center; gap: 6px; color: white; font-weight: 700; font-size: 13.5px; background: #0284c7; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);">
            🖨️ Cetak Rincian Ini
        </button>
    </div>

</div>
@endsection
