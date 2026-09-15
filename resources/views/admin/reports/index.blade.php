@extends('layouts.app')

@section('content')

<style>
    .report-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 24px;
    }

    .report-subtitle {
        color: #666;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .report-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .report-card {
        background: white;
        padding: 20px 22px;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .report-card.filter-card {
        margin-top: 28px;
        margin-bottom: 24px;
        padding-top: 24px;
    }

    .report-card h3 {
        color: #4b5563;
        font-size: 0.95rem;
        margin-bottom: 8px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .report-card h2 {
        color: #2e7d32;
        font-size: 1.35rem;
        margin: 0;
        font-weight: 800;
    }

    .best-seller-box {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        padding: 12px 0;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: end;
        margin-bottom: 0;
        padding: 16px;
        background: white;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 160px;
    }

    .filter-group label {
        font-size: 0.95rem;
        color: #1f2937;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .filter-group select,
    .filter-group input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.95rem;
        background: #fff;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        background: white;
        border: 1px solid #d9e7d7;
    }

    .admin-table thead th,
    .admin-table tbody td {
        padding: 13px 15px;
        border: 1px solid #d9e7d7;
        vertical-align: middle;
        text-align: left;
        white-space: nowrap;
        line-height: 1.4;
        height: 48px;
    }

    .admin-table thead th {
        background: #2e7d32;
        color: white;
        font-weight: 800;
        text-align: center;
        font-size: 0.95rem;
    }

    .admin-table tbody tr:nth-child(even) {
        background: #f9fdf7;
    }

    .admin-table tbody td:first-child {
        width: 60px;
        text-align: center;
        font-weight: 600;
        color: #444;
    }

    .admin-table tbody td:nth-child(2) {
        text-align: left;
    }

    .admin-table tbody td:nth-child(3) {
        text-align: center;
        font-weight: 600;
    }

    .admin-table tbody td:last-child {
        text-align: right;
        font-weight: 700;
        color: #2e7d32;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .admin-container {
            margin: 20px auto;
            padding: 0;
        }

        body {
            background: white;
        }

        .report-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .admin-table th {
            background: #000 !important;
            color: white !important;
        }
    }
</style>

<div class="admin-container">
    <div class="report-actions no-print">
        <a href="/admin/orders" class="btn btn-success">🧾 Lihat Pesanan</a>
        <a href="/admin/supplier-stocks" class="btn btn-warning">📦 Kelola Stok</a>
        <button type="button" class="btn btn-danger" onclick="window.print()">🖨️ Cetak Laporan</button>
        <a href="/admin/reports/export?report_type={{ $reportType }}&month={{ $month ?? now()->format('Y-m') }}&supplier_filter={{ $supplierFilter }}" class="btn btn-success">⬇️ Unduh CSV</a>
    </div>

    {{-- Kop Surat Formal Premium Gold-Emerald Stripe --}}
    <div style="display: flex; align-items: center; gap: 24px; padding-bottom: 12px;">
        <div style="flex-shrink: 0;">
            <div style="width: 88px; height: 88px; border-radius: 50%; background: radial-gradient(circle, #ffeb3b 0%, #facc15 70%, #eab308 100%); border: 4px solid #15803d; box-shadow: 0 6px 14px rgba(21, 128, 61, 0.25), inset 0 0 10px rgba(255, 255, 255, 0.8); display: flex; flex-direction: column; align-items: center; justify-content: center; color: #15803d; text-align: center; font-weight: 900; line-height: 1;">
                <div style="font-size: 28px; font-weight: 900; color: #15803d; letter-spacing: -1px; text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.9);">DL</div>
                <div style="font-size: 8.5px; font-weight: 800; color: #166534; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px;">Dewi Lestari 2</div>
            </div>
        </div>
        <div style="flex-grow: 1; text-align: right;">
            <h2 style="margin: 0; font-size: 26px; font-weight: 900; color: #0f172a; letter-spacing: 1px; text-transform: uppercase; line-height: 1.1;">TOKO DEWI LESTARI 2</h2>
            <div style="font-size: 12px; font-weight: 700; color: #15803d; margin-top: 3px; letter-spacing: 0.5px; text-transform: uppercase;">Pusat Oleh-Oleh & Kuliner Khas Bandung / Jawa Barat</div>
            <p style="margin: 4px 0 0; font-size: 12px; color: #334155; line-height: 1.4;">Jl. Raya Cimindi No.59, Pasirkaliki, Kec. Cimahi Utara, Kota Cimahi, Jawa Barat 40535</p>
            <div style="font-size: 11.5px; color: #475569; margin-top: 4px; font-weight: 600; display: flex; justify-content: flex-end; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span>📞 0812-2195-6759</span>
                <span style="color: #15803d; font-weight: bold;">•</span>
                <span>✉️ info@dewilestari2.com</span>
                <span style="color: #15803d; font-weight: bold;">•</span>
                <span>🌐 www.dewilestari2.com</span>
            </div>
        </div>
    </div>

    {{-- Garis Kop Ganda Emas - Hijau --}}
    <div style="margin-top: 8px; margin-bottom: 24px;">
        <div style="height: 5px; background: linear-gradient(90deg, #15803d 0%, #166534 60%, #eab308 100%); border-radius: 2px;"></div>
        <div style="height: 2px; background: #eab308; margin-top: 3px; border-radius: 1px;"></div>
    </div>

    {{-- Banner Judul Laporan --}}
    <div style="text-align: center; margin-bottom: 24px; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 14px 20px; border-radius: 10px; border: 1px solid #86efac; border-left: 6px solid #15803d; box-shadow: 0 2px 8px rgba(21, 128, 61, 0.06);">
        <h1 style="font-size: 20px; font-weight: 900; letter-spacing: 8px; text-transform: uppercase; margin: 0; color: #0f172a;">
            L A P O R A N   P E N J U A L A N
        </h1>
        <div style="font-size: 12px; color: #166534; font-weight: 600; margin-top: 4px; letter-spacing: 0.5px;">Dokumen Resmi Rekapitulasi Transaksi Penjualan Toko</div>
    </div>

    <div class="report-cards">
        <div class="report-card">
            <h3>Total Pesanan</h3>
            <h2>{{ $totalOrders }}</h2>
        </div>

        <div class="report-card">
            <h3>Total Produk Terjual</h3>
            <h2>{{ $totalItemsSold }}</h2>
        </div>

        <div class="report-card">
            <h3>Total Penjualan</h3>
            <h2>Rp {{ number_format($totalSales,0,',','.') }}</h2>
        </div>

        <div class="report-card">
            <h3>Bagian Toko (70%)</h3>
            <h2>Rp {{ number_format($shopShare,0,',','.') }}</h2>
        </div>

        <div class="report-card">
            <h3>Bagian Supplier (30%)</h3>
            <h2>Rp {{ number_format($supplierShare,0,',','.') }}</h2>
        </div>
    </div>

    <div class="report-card" style="margin-bottom: 20px;">
        <h3>🏆 Best Seller</h3>
        <div class="best-seller-box">
            <strong style="color: #2e7d32;">{{ $salesReport['best_seller']['product_name'] }}</strong>
            <span>{{ $salesReport['best_seller']['quantity_sold'] }} unit terjual</span>
            <span>Omzet: Rp {{ number_format($salesReport['best_seller']['revenue'],0,',','.') }}</span>
        </div>
    </div>

    <div class="report-card filter-card no-print">
        <h3 style="margin-bottom: 14px; font-size: 1rem; color: #1f2937;">🔎 Filter Laporan</h3>
        <form method="GET" action="/admin/reports" class="filter-form" style="margin-bottom: 0; padding: 0; box-shadow: none; background: transparent;">
            <div class="filter-group">
                <label>Jenis Laporan</label>
                <select name="report_type">
                    <option value="sales" {{ $reportType === 'sales' ? 'selected' : '' }}>Laporan Penjualan Toko</option>
                    <option value="supplier" {{ $reportType === 'supplier' ? 'selected' : '' }}>Laporan Supplier</option>
                    <option value="supplier_products" {{ $reportType === 'supplier_products' ? 'selected' : '' }}>Laporan Produk Supplier</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Supplier</label>
                <select name="supplier_filter">
                    <option value="all" {{ $supplierFilter === 'all' ? 'selected' : '' }}>Semua Supplier</option>
                    <option value="Supplier Dodol" {{ $supplierFilter === 'Supplier Dodol' ? 'selected' : '' }}>Supplier Dodol</option>
                    <option value="Supplier Kripik" {{ $supplierFilter === 'Supplier Kripik' ? 'selected' : '' }}>Supplier Kripik</option>
                    <option value="Supplier Bolu" {{ $supplierFilter === 'Supplier Bolu' ? 'selected' : '' }}>Supplier Bolu</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Pilih Bulan</label>
                <input type="month" name="month" value="{{ $month ?? now()->format('Y-m') }}">
            </div>

            <div class="filter-group">
                <button type="submit" class="btn btn-success">🔎 Tampilkan</button>
            </div>
        </form>
    </div>

    @if($reportType === 'supplier' || $reportType === 'supplier_products')
        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Pendapatan</th>
                        <th>Bagian Toko 70%</th>
                        <th>Bagian Supplier 30%</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesReport['supplier_breakdown'] as $index => $supplier)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $supplier['supplier_name'] }}</td>
                            <td>{{ $supplier['quantity_sold'] }}</td>
                            <td>Rp {{ number_format($supplier['total_revenue'],0,',','.') }}</td>
                            <td>Rp {{ number_format($supplier['shop_share'],0,',','.') }}</td>
                            <td>Rp {{ number_format($supplier['supplier_share'],0,',','.') }}</td>
                        </tr>

                        @if($reportType === 'supplier_products')
                            @foreach($supplier['products'] as $product)
                                <tr style="background: #fffdf5;">
                                    <td></td>
                                    <td style="padding-left: 24px;">↳ {{ $product['product_name'] }}</td>
                                    <td>{{ $product['quantity_sold'] }}</td>
                                    <td>Rp {{ number_format($product['revenue'],0,',','.') }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endif
                    @empty
                        <tr>
                            <td colspan="6">Belum ada data supplier</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Terjual</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesReport['products'] as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product['product_name'] }}</td>
                            <td>{{ $product['quantity_sold'] }}</td>
                            <td>Rp {{ number_format($product['revenue'],0,',','.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Belum ada data penjualan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
</div>

@endsection