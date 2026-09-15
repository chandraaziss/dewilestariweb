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
        <!-- Kop Surat Formal Premium (PDF Compatible Table) -->
        <table style="width: 100%; border: none; margin-bottom: 6px; border-collapse: collapse;">
            <tr style="border: none;">
                <td style="width: 95px; border: none; vertical-align: middle; padding: 0;">
                    <div style="width: 82px; height: 82px; border-radius: 50%; background: #facc15; border: 4px solid #15803d; text-align: center; color: #15803d; padding-top: 13px; box-sizing: border-box;">
                        <div style="font-size: 24px; font-weight: 900; line-height: 1;">DL</div>
                        <div style="font-size: 7.5px; font-weight: 800; text-transform: uppercase; margin-top: 2px;">Dewi Lestari 2</div>
                    </div>
                </td>
                <td style="border: none; text-align: right; vertical-align: middle; padding: 0;">
                    <h2 style="margin: 0; font-size: 24px; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 1px;">TOKO DEWI LESTARI 2</h2>
                    <div style="font-size: 11px; font-weight: bold; color: #15803d; margin-top: 2px; text-transform: uppercase;">Pusat Oleh-Oleh & Kuliner Khas Bandung / Jawa Barat</div>
                    <p style="margin: 4px 0 0; font-size: 11px; color: #334155;">Jl. Raya Cimindi No.59, Pasirkaliki, Kec. Cimahi Utara, Kota Cimahi, Jawa Barat 40535</p>
                    <p style="margin: 2px 0 0; font-size: 11px; color: #475569; font-weight: 600;">Telp: 0812-2195-6759 | Email: info@dewilestari2.com | Web: www.dewilestari2.com</p>
                </td>
            </tr>
        </table>

        <!-- Garis Kop Ganda Emas - Hijau -->
        <div style="height: 4px; background: #15803d; margin-top: 6px; border-radius: 2px;"></div>
        <div style="height: 2px; background: #eab308; margin-top: 2px; margin-bottom: 20px; border-radius: 1px;"></div>

        <!-- Banner Judul Sesuai Kop -->
        <div style="text-align: center; margin-bottom: 22px; background: #f0fdf4; padding: 12px 18px; border-radius: 8px; border: 1px solid #86efac; border-left: 5px solid #15803d;">
            <h1 style="font-size: 19px; font-weight: 900; letter-spacing: 6px; text-transform: uppercase; margin: 0; color: #0f172a;">
                L A P O R A N   P E N J U A L A N
            </h1>
            <div style="font-size: 11px; color: #166534; font-weight: 600; margin-top: 3px;">Dokumen Resmi Rekapitulasi Transaksi Penjualan Toko</div>
        </div>
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
                    <th>Detail Produk Yang Dibeli</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                    <tr>
                        <td class="text-right">{{ $index + 1 }}</td>
                        <td>{{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->translatedFormat('d F Y') : '-' }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td class="text-right">{{ $order->items->sum('qty') }} bungkus</td>
                        <td class="text-right">Rp {{ number_format($order->total_amount,0,',','.') }}</td>
                        <td>{{ $order->payment_method ?? '-' }}</td>
                        <td>
                            @foreach($order->items as $item)
                                @php
                                    $pName = $item->product->name ?? $item->product->item_name ?? 'Produk';
                                    $w = !empty($item->weight) ? " ({$item->weight})" : (!empty($item->product->weight) ? " ({$item->product->weight})" : '');
                                @endphp
                                <div>• {{ $pName }}{{ $w }} (x{{ $item->qty }} bungkus)</div>
                            @endforeach
                        </td>
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
