@extends('layouts.app')

@section('content')
<style>
    body, .admin-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        font-size: 13px;
        color: #1f2937;
    }

    .report-document {
        max-width: 950px;
        margin: 20px auto 40px;
        padding: 40px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid rgba(229, 231, 235, 0.6);
    }

    .report-title {
        font-size: 26px;
        font-weight: 800;
        text-align: center;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
        color: #111827;
    }

    .report-subtitle {
        font-size: 15px;
        color: #6b7280;
        text-align: center;
        margin-bottom: 30px;
    }

    .report-meta {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        background: #f9fafb;
        padding: 16px 20px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    .report-meta-left {
        line-height: 1.8;
        font-size: 14px;
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-group label {
        font-weight: 600;
        font-size: 12px;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-group input[type="month"] {
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        background: white;
        transition: border-color 0.2s;
    }

    .filter-group input[type="month"]:focus {
        border-color: #10b981;
        outline: none;
    }

    .btn-custom {
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-success { background: #10b981; color: white; }
    .btn-success:hover { background: #059669; }
    
    .btn-warning { background: #f59e0b; color: white; }
    .btn-warning:hover { background: #d97706; }
    
    .btn-danger { background: #ef4444; color: white; }
    .btn-danger:hover { background: #dc2626; }

    .btn-outline { background: white; color: #1f2937; border: 1px solid #d1d5db; }
    .btn-outline:hover { background: #f3f4f6; }

    .summary-box {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 36px;
    }

    .summary-item {
        background: linear-gradient(145deg, #ffffff, #f9fafb);
        border: 1px solid #e5e7eb;
        padding: 20px 16px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s;
    }
    
    .summary-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .summary-item strong {
        display: block;
        margin-bottom: 8px;
        color: #6b7280;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .summary-item .value {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
    }

    .summary-item.revenue .value { color: #10b981; }
    .summary-item.loss .value { color: #dc2626; }
    .summary-item.net .value { color: #059669; }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 16px;
        margin-top: 32px;
    }

    .report-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 24px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .report-table th,
    .report-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e5e7eb;
    }

    .report-table th {
        background: #f9fafb;
        text-align: left;
        font-weight: 600;
        color: #4b5563;
        font-size: 12.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .report-table td {
        vertical-align: middle;
        font-size: 13px;
    }

    .report-table tbody tr:last-child td {
        border-bottom: none;
    }

    .report-table tbody tr:hover {
        background: #f9fafb;
    }

    .text-right { text-align: right !important; }
    .text-center { text-align: center !important; }

    .signature {
        display: flex;
        justify-content: flex-end;
        margin-top: 60px;
    }

    .signature-box {
        text-align: center;
        width: 250px;
        line-height: 1.6;
    }

    .signature-box .name {
        margin-top: 80px;
        font-weight: 700;
        display: block;
        border-bottom: 1px solid #111827;
        margin-bottom: 4px;
    }

    .no-print { margin-bottom: 24px; }

    @media print {
        body, .admin-container {
            background: white;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
        }
        .report-document {
            margin: 0;
            padding: 0;
            box-shadow: none;
            border: none;
            max-width: 100%;
        }
        .no-print, header, footer, .report-actions {
            display: none !important;
        }
        .summary-item {
            border: 1px solid #000;
            box-shadow: none;
            border-radius: 0;
            padding: 10px;
        }
        .summary-item .value { font-size: 16px; color: #000; }
        .report-table {
            border: 1px solid #000;
            border-radius: 0;
        }
        .report-table th, .report-table td {
            border-bottom: 1px solid #000;
            border-right: 1px solid #000;
            padding: 8px;
        }
        .report-table th:last-child, .report-table td:last-child {
            border-right: none;
        }
        .report-meta {
            border: none;
            background: none;
            padding: 0;
            margin-bottom: 20px;
        }
    }
</style>

<div class="admin-container">
    <div class="no-print" style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:24px; align-items:flex-end;">
        <a href="/admin/orders" class="btn-custom btn-outline">⬅ Kembali ke Pesanan</a>
        <form method="GET" action="/admin/reports/store" class="filter-form" style="margin:0; background:#fff; padding:12px 16px; border-radius:12px; box-shadow:0 4px 6px rgba(0,0,0,0.05); display:flex; align-items:flex-end; gap:12px; border:1px solid #e5e7eb;">
            <div class="filter-group">
                <label for="month">Filter Bulan</label>
                <input type="month" id="month" name="month" value="{{ old('month', $month ?? now()->format('Y-m')) }}">
            </div>
            <button type="submit" class="btn-custom btn-success">🔎 Tampilkan</button>
            <a href="/admin/reports/store/pdf?month={{ $month ?? now()->format('Y-m') }}" class="btn-custom btn-warning">📄 Download PDF</a>
        </form>
        <button onclick="window.print()" class="btn-custom btn-outline">🖨️ Print</button>
    </div>

    <div class="report-document">
        <div class="report-title">LAPORAN PENJUALAN & KERUGIAN TOKO</div>
        <div class="report-subtitle">Toko Dewi Lestari 2</div>

        <div class="report-meta">
            <div class="report-meta-left">
                <div><strong>Periode:</strong> {{ $periodLabel ?? ($month ? \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') : 'Semua Periode') }}</div>
                <div><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y') }}</div>
            </div>
        </div>

        <div class="summary-box">
            <div class="summary-item">
                <strong>Total Transaksi</strong>
                <div class="value">{{ $totalOrders }}</div>
            </div>
            <div class="summary-item">
                <strong>Barang Terjual</strong>
                <div class="value">{{ $totalItemsSold }} pcs</div>
            </div>
            <div class="summary-item revenue">
                <strong>Total Pendapatan Kotor</strong>
                <div class="value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item loss">
                <strong>Kerugian Kadaluarsa</strong>
                <div class="value">Rp {{ number_format($totalExpiredLoss ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item net">
                <strong>Pendapatan Bersih</strong>
                <div class="value">Rp {{ number_format($netSales ?? $totalSales, 0, ',', '.') }}</div>
            </div>
        </div>

        <h3 class="section-title">🛒 Riwayat Penjualan Toko</h3>
        <table class="report-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>No Pesanan</th>
                    <th>Nama Pembeli</th>
                    <th>Jumlah Item</th>
                    <th>Total Bayar</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                    <tr>
                        <td class="text-right">{{ $index + 1 }}</td>
                        <td>{{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->translatedFormat('d F Y') : '-' }}</td>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->customer_name }}</td>
                        <td class="text-right">{{ $order->items->sum('qty') }}</td>
                        <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td><span style="background: #d4edda; color: #155724; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">{{ ucwords($order->payment_status) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color: #888;">Tidak ada data penjualan untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 40px;">
            <h3 class="section-title">📦 Rekap Produk Terjual</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($soldProducts as $index => $product)
                        <tr>
                            <td class="text-right">{{ $index + 1 }}</td>
                            <td>{{ $product['product_name'] }}</td>
                            <td class="text-right">{{ $product['quantity_sold'] }} pcs</td>
                            <td class="text-right">Rp {{ number_format($product['revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color: #888;">Tidak ada produk terjual untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 40px;">
            <h3 class="section-title" style="color: #dc2626;">🚨 Rekap Barang Kadaluarsa (Kerugian Toko)</h3>
            
            <table class="report-table">
                <thead>
                    <tr style="background: #fef2f2;">
                        <th>No</th>
                        <th>Tanggal Catat</th>
                        <th>Nama Produk</th>
                        <th>Varian / Ukuran</th>
                        <th>Qty Kadaluarsa</th>
                        <th>Estimasi Modal / Beli</th>
                        <th>Total Kerugian Toko</th>
                        <th>Keterangan / Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expiredLogs as $index => $log)
                        <tr>
                            <td class="text-right">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                            <td><strong>{{ $log->supplierStock->name ?? 'Produk' }}</strong></td>
                            <td>{{ $log->weight ?? '-' }}</td>
                            <td class="text-right" style="color: #dc2626; font-weight: bold;">{{ $log->quantity }} pcs</td>
                            <td class="text-right">Rp {{ number_format($log->buy_price ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right" style="color: #dc2626; font-weight: bold;">Rp {{ number_format($log->total_loss ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $log->description ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; color: #888;">Tidak ada catatan barang kadaluarsa pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="signature">
            <div class="signature-box">
                <div>Cimahi, {{ now()->translatedFormat('d F Y') }}</div>
                <div>Mengetahui,</div>
                <div class="name">(...............................)</div>
                <div>Pemilik Toko</div>
            </div>
        </div>
    </div>
</div>
@endsection
