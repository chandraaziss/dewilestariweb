@extends('layouts.app')

@section('content')
    <style>
        body,
        .admin-container {
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(229, 231, 235, 0.6);
        }

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
        .report-title-banner {
            text-align: center;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            padding: 14px 20px;
            border-radius: 10px;
            border: 1px solid #86efac;
            border-left: 6px solid #15803d;
            box-shadow: 0 2px 8px rgba(21, 128, 61, 0.06);
        }

        .report-title-banner h1 {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 8px;
            text-transform: uppercase;
            margin: 0;
            color: #0f172a;
        }

        .report-title-banner .report-doc-code {
            font-size: 12px;
            color: #166534;
            font-weight: 600;
            margin-top: 4px;
            letter-spacing: 0.5px;
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

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-outline {
            background: white;
            color: #1f2937;
            border: 1px solid #d1d5db;
        }

        .btn-outline:hover {
            background: #f3f4f6;
        }

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

        .summary-item.revenue .value {
            color: #10b981;
        }

        .summary-item.loss .value {
            color: #dc2626;
        }

        .summary-item.net .value {
            color: #059669;
        }

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

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

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

        .no-print {
            margin-bottom: 24px;
        }

        @media print {

            body,
            .admin-container {
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

            .no-print,
            header,
            footer,
            .report-actions {
                display: none !important;
            }

            .summary-item {
                border: 1px solid #000;
                box-shadow: none;
                border-radius: 0;
                padding: 10px;
            }

            .summary-item .value {
                font-size: 16px;
                color: #000;
            }

            .report-table {
                border: 1px solid #000;
                border-radius: 0;
            }

            .report-table th,
            .report-table td {
                border-bottom: 1px solid #000;
                border-right: 1px solid #000;
                padding: 8px;
            }

            .report-table th:last-child,
            .report-table td:last-child {
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
        <div class="no-print" style="margin-bottom:24px;">
            <div
                style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:12px;">
                <a href="/admin/orders" class="btn-custom btn-outline">⬅ Kembali ke Pesanan</a>
                <div style="display:flex; gap:8px;">
                    <a href="/admin/reports/store/pdf?{{ $filterDetails['query_params'] ?? '' }}"
                        class="btn-custom btn-warning">📄 Download PDF</a>
                    <button onclick="window.print()" class="btn-custom btn-outline">🖨️ Print</button>
                </div>
            </div>

            <form method="GET" action="/admin/reports/store" class="filter-form"
                style="margin:0; background:#fff; padding:16px 20px; border-radius:12px; box-shadow:0 4px 6px rgba(0,0,0,0.05); display:flex; flex-wrap:wrap; align-items:flex-end; gap:14px; border:1px solid #e5e7eb;">
                <!-- 1. Pilih Bulan & Tahun (Selalu Tampil) -->
                <div>
                    <label style="font-weight:700; font-size:12.5px; color:#374151; display:block; margin-bottom:6px;">📅
                        Pilih Bulan & Tahun:</label>
                    <input type="month" name="month" id="store_month_picker"
                        value="{{ $filterDetails['selected_month'] ?? now()->format('Y-m') }}"
                        onchange="toggleStoreFilterInputs()"
                        style="padding:8px 12px; border-radius:8px; border:1px solid #d1d5db; font-size:13px; font-weight:600; background:#fff; height:38px; box-sizing:border-box;">
                </div>

                <!-- 2. Tipe Laporan (Bulanan, Harian, Mingguan, Custom) -->
                <div>
                    <label style="font-weight:700; font-size:12.5px; color:#374151; display:block; margin-bottom:6px;">📊
                        Tipe Laporan:</label>
                    <select name="filter_type" id="store_filter_type" onchange="toggleStoreFilterInputs()"
                        style="padding:8px 12px; border-radius:8px; border:1px solid #d1d5db; font-size:13px; font-weight:600; background:#f9fafb; outline:none; height:38px;">
                        <option value="monthly" {{ ($filterDetails['filter_type'] ?? 'monthly') === 'monthly' ? 'selected' : '' }}>📊 Bulanan (1 Bulan Penuh)</option>
                        <option value="daily" {{ ($filterDetails['filter_type'] ?? 'monthly') === 'daily' ? 'selected' : '' }}>📆 Harian (Pilih Tanggal)</option>
                        <option value="weekly" {{ ($filterDetails['filter_type'] ?? 'monthly') === 'weekly' ? 'selected' : '' }}>🗓️ Mingguan (Pilih Minggu Ke-X)</option>
                        <option value="custom" {{ ($filterDetails['filter_type'] ?? 'monthly') === 'custom' ? 'selected' : '' }}>🎯 Rentang Tanggal Custom</option>
                    </select>
                </div>

                <!-- 3. Input Daily (Date Picker) -->
                <div id="store_input_daily" class="store-filter-group" style="display:none;">
                    <label style="font-weight:700; font-size:12.5px; color:#374151; display:block; margin-bottom:6px;">Pilih
                        Tanggal Spesifik:</label>
                    <input type="date" name="date" value="{{ $filterDetails['selected_date'] ?? now()->format('Y-m-d') }}"
                        style="padding:8px 12px; border-radius:8px; border:1px solid #d1d5db; font-size:13px; height:38px; box-sizing:border-box;">
                </div>

                <!-- 4. Input Weekly (Minggu Ke-1 s/d 4) -->
                <div id="store_input_weekly" class="store-filter-group" style="display:none;">
                    <label style="font-weight:700; font-size:12.5px; color:#374151; display:block; margin-bottom:6px;">Pilih
                        Minggu Ke-:</label>
                    <select name="week_num"
                        style="padding:8px 12px; border-radius:8px; border:1px solid #d1d5db; font-size:13px; font-weight:600; background:#fff; height:38px;">
                        <option value="1" {{ ($filterDetails['week_num'] ?? 1) == 1 ? 'selected' : '' }}>Minggu Ke-1 </option>
                        <option value="2" {{ ($filterDetails['week_num'] ?? 1) == 2 ? 'selected' : '' }}>Minggu Ke-2 </option>
                        <option value="3" {{ ($filterDetails['week_num'] ?? 1) == 3 ? 'selected' : '' }}>Minggu Ke-3 </option>
                        <option value="4" {{ ($filterDetails['week_num'] ?? 1) == 4 ? 'selected' : '' }}>Minggu Ke-4 </option>
                    </select>
                </div>

                <!-- 5. Input Custom Range -->
                <div id="store_input_custom" class="store-filter-group" style="display:none; flex-wrap:wrap; gap:10px;">
                    <div>
                        <label
                            style="font-weight:700; font-size:12.5px; color:#374151; display:block; margin-bottom:6px;">Dari
                            Tanggal:</label>
                        <input type="date" name="start_date" value="{{ $filterDetails['start_date'] ?? '' }}"
                            style="padding:8px 12px; border-radius:8px; border:1px solid #d1d5db; font-size:13px; height:38px; box-sizing:border-box;">
                    </div>
                    <div>
                        <label
                            style="font-weight:700; font-size:12.5px; color:#374151; display:block; margin-bottom:6px;">Sampai
                            Tanggal:</label>
                        <input type="date" name="end_date" value="{{ $filterDetails['end_date'] ?? '' }}"
                            style="padding:8px 12px; border-radius:8px; border:1px solid #d1d5db; font-size:13px; height:38px; box-sizing:border-box;">
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn-custom btn-success" style="height:38px; padding:0 18px;">🔎 Tampilkan
                        Laporan</button>
                </div>
            </form>
        </div>

        <script>
            function toggleStoreFilterInputs() {
                const type = document.getElementById('store_filter_type').value;
                document.querySelectorAll('.store-filter-group').forEach(el => el.style.display = 'none');
                const target = document.getElementById('store_input_' + type);
                if (target) {
                    target.style.display = type === 'custom' ? 'flex' : 'block';
                }
            }
            document.addEventListener('DOMContentLoaded', toggleStoreFilterInputs);
        </script>

        <div class="report-document">
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
                    <p class="store-address">Jl. Raya Cimindi No.59, Pasirkaliki, Kec. Cimahi Utara, Kota Cimahi, Jawa Barat
                        40535</p>
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
            <div class="report-title-banner">
                <h1>L A P O R A N P E N J U A L A N</h1>
                <div class="report-doc-code">Dokumen Resmi Rekapitulasi Transaksi Penjualan Toko</div>
            </div>

            <div class="report-meta">
                <div class="report-meta-left">
                    <div><strong>Periode:</strong>
                        {{ $periodLabel ?? ($month ? \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') : 'Semua Periode') }}
                    </div>
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
                    <div class="value">{{ $totalItemsSold }} bungkus</div>
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
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $index => $order)
                        <tr>
                            <td class="text-right">{{ $index + 1 }}</td>
                            <td>{{ $order->paid_at ? \Carbon\Carbon::parse($order->paid_at)->translatedFormat('d F Y') : '-' }}
                            </td>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->customer_name }}</td>
                            <td class="text-right">{{ $order->items->sum('qty') }} bungkus</td>
                            <td class="text-right" style="font-weight: bold; color: #166534;">Rp
                                {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td style="text-align: center;">
                                <a href="/admin/reports/order-detail/{{ $order->id }}"
                                    style="display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; background: #0284c7; color: white; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 12.5px; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);">
                                    📦 Detail Produk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color: #888;">Tidak ada data penjualan untuk periode ini.
                            </td>
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
                                <td class="text-right">{{ $product['quantity_sold'] }} bungkus</td>
                                <td class="text-right">Rp {{ number_format($product['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color: #888;">Tidak ada produk terjual untuk periode
                                    ini.</td>
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
                                <td>{{ \Carbon\Carbon::parse($log->created_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}
                                </td>
                                <td><strong>{{ $log->supplierStock->name ?? 'Produk' }}</strong></td>
                                <td>{{ $log->weight ?? '-' }}</td>
                                <td class="text-right" style="color: #dc2626; font-weight: bold;">{{ $log->quantity }} bungkus
                                </td>
                                <td class="text-right">Rp {{ number_format($log->buy_price ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right" style="color: #dc2626; font-weight: bold;">Rp
                                    {{ number_format($log->total_loss ?? 0, 0, ',', '.') }}</td>
                                <td>{{ $log->description ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center; color: #888;">Tidak ada catatan barang kadaluarsa pada
                                    periode ini.</td>
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