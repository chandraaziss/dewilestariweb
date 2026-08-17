<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LAPORAN PENJUALAN</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; color: #000; margin: 0; padding: 0; }
        .document {
            width: 100%;
            padding: 40px;
            box-sizing: border-box;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
        }
        .subtitle {
            text-align: center;
            margin: 4px 0 24px;
        }
        .meta {
            margin-bottom: 22px;
            line-height: 1.7;
        }
        .meta span {
            display: block;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #000; padding: 10px 12px; }
        th { background: #e0e0e0; font-weight: bold; }
        td { vertical-align: top; }
        .text-right { text-align: right; }
        .summary { display: flex; justify-content: flex-start; gap: 20px; flex-wrap: wrap; margin-bottom: 36px; }
        .summary-item {
            border: 1px solid #000;
            padding: 10px 12px;
            min-width: 180px;
        }
        .summary-item strong { display: block; margin-bottom: 6px; }
        .signature { display: flex; justify-content: flex-end; margin-top: 40px; }
        .signature-box { width: 300px; line-height: 1.8; }
        .signature-box .name { margin-top: 80px; display: block; }
    </style>
</head>
<body>
    <div class="document">
        <p class="title">LAPORAN PENJUALAN</p>
        <p class="subtitle">Toko Dewi Lestari 2</p>
        <div class="meta">
            <span><strong>Periode:</strong> {{ $periodLabel ?? ($month ? \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') : 'Semua Periode') }}</span>
            <span><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y') }}</span>
        </div>
        <div class="summary">
            <div class="summary-item">
                <strong>Total Transaksi</strong>
                {{ $totalOrders }}
            </div>
            <div class="summary-item">
                <strong>Total Barang Terjual</strong>
                {{ $totalItemsSold }}
            </div>
            <div class="summary-item">
                <strong>Total Pendapatan</strong>
                Rp {{ number_format($totalSales,0,',','.') }}
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>No Pesanan</th>
                    <th>Nama Pembeli</th>
                    <th>Jumlah Item</th>
                    <th>Total Pembayaran</th>
                    <th>Metode Pembayaran</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                    <tr>
                        <td class="text-right">{{ $index + 1 }}</td>
                        <td>{{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->translatedFormat('d F Y') : '-' }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td class="text-right">{{ $order->items->sum('qty') }}</td>
                        <td class="text-right">Rp {{ number_format($order->total_amount,0,',','.') }}</td>
                        <td>{{ $order->payment_method ?? '-' }}</td>
                        <td>{{ ucwords($order->payment_status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-right">Tidak ada data penjualan untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 24px;">
            <h3>Daftar Produk Terjual</h3>
            <table>
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
                            <td class="text-right">{{ $product['quantity_sold'] }}</td>
                            <td class="text-right">Rp {{ number_format($product['revenue'],0,',','.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-right">Tidak ada produk terjual untuk periode ini.</td>
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
</body>
</html>
