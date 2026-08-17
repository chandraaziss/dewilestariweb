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
            max-width: 1000px;
            margin: 20px auto 40px;
            padding: 40px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(229, 231, 235, 0.6);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #10b981;
        }

        .report-brand {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 13px;
        }

        .report-brand strong {
            font-size: 18px;
            color: #10b981;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .report-title {
            font-size: 26px;
            font-weight: 800;
            text-align: center;
            color: #111827;
            letter-spacing: 0.5px;
        }

        .report-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(220px, 1fr));
            gap: 12px;
            margin: 0 0 24px;
            padding: 16px 20px;
            background: #f9fafb;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        .report-meta .meta-item strong {
            display: block;
            color: #4b5563;
            margin-bottom: 4px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-meta .meta-item span {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
            margin-bottom: 30px;
            background: #fff;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 180px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group select,
        .filter-group input[type="month"] {
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 13px;
            background: white;
            transition: border-color 0.2s;
        }

        .filter-group select:focus,
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

        .filter-group button {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            background: #10b981;
            color: white;
        }

        .filter-group button:hover {
            background: #059669;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            color: white;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
            color: white;
        }

        .btn-outline {
            background: white;
            color: #1f2937;
            border: 1px solid #d1d5db;
        }

        .btn-outline:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .summary-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .summary-card {
            background: linear-gradient(145deg, #ffffff, #f9fafb);
            border: 1px solid #e5e7eb;
            padding: 24px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .summary-card strong {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card .value {
            font-size: 26px;
            font-weight: 800;
            color: #111827;
        }

        .share-box {
            display: grid;
            grid-template-columns: repeat(2, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .share-card {
            padding: 24px 20px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s;
        }

        .share-card:first-child {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-color: #bbf7d0;
        }

        .share-card:last-child {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border-color: #bfdbfe;
        }

        .share-card:hover {
            transform: translateY(-2px);
        }

        .share-card strong {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            color: #4b5563;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .share-card .value {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
        }

        .share-card:first-child .value {
            color: #166534;
        }

        .share-card:last-child .value {
            color: #1e40af;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 24px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .admin-table th,
        .admin-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: middle;
        }

        .admin-table th {
            background: #f9fafb;
            color: #4b5563;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .admin-table td {
            font-size: 13.5px;
        }

        .admin-table td.numeric {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-weight: 500;
        }

        .admin-table tbody tr:last-child td {
            border-bottom: none;
        }

        .admin-table tbody tr:hover {
            background: #f9fafb;
        }

        .report-actions {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .signature-area {
            margin-top: 60px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            text-align: center;
        }

        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #111827;
            padding-top: 6px;
            font-weight: 600;
            display: inline-block;
            min-width: 200px;
        }

        .footer-note {
            margin-top: 32px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            border-left: 4px solid #10b981;
            font-size: 13px;
            line-height: 1.6;
            color: #4b5563;
        }

        @media print {

            body,
            .admin-container {
                background: white;
                font-family: 'Times New Roman', Times, serif;
                font-size: 12px;
            }

            .report-actions,
            .filter-form {
                display: none !important;
            }

            .report-document {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }

            .report-meta {
                border: none;
                background: none;
                padding: 0;
                margin-bottom: 16px;
            }

            .summary-card,
            .share-card {
                border: 1px solid #000;
                box-shadow: none;
                border-radius: 0;
                padding: 10px;
                background: none;
            }

            .summary-card .value,
            .share-card .value {
                font-size: 16px;
                color: #000;
            }

            .admin-table {
                border: 1px solid #000;
                border-radius: 0;
            }

            .admin-table th,
            .admin-table td {
                border-bottom: 1px solid #000;
                border-right: 1px solid #000;
                padding: 8px;
            }

            .admin-table th:last-child,
            .admin-table td:last-child {
                border-right: none;
            }

            .footer-note {
                background: none;
                border: none;
                border-top: 1px solid #000;
                border-radius: 0;
                padding: 10px 0 0 0;
            }
        }
    </style>

    <div class="admin-container">
        <div class="report-actions no-print" style="justify-content: flex-end;">
            <a href="/admin/reports" class="btn-custom btn-outline">⬅ Kembali</a>
            <a href="/admin/reports/suppliers/pdf?month={{ $month ?? now()->format('Y-m') }}&supplier_filter={{ $supplierFilter }}"
                class="btn-custom btn-warning">📄 Download PDF</a>
            <button type="button" class="btn-custom btn-outline" onclick="window.print()">🖨️ Cetak</button>
        </div>

        <div class="report-document">
            <div class="report-header">
                <div class="report-brand">
                    <strong>Toko Dewi Lestari 2</strong>
                    <span>Jl. Raya Cimindi No.59, Cimahi</span>
                    <span>Telp. 0812-2195-6759</span>
                    <span>Email: info@dewilestari2.com</span>
                </div>
                <div class="report-title">LAPORAN PENJUALAN SUPPLIER</div>
            </div>

            <div class="report-meta">
                <div class="meta-item">
                    <strong>Supplier</strong>
                    <span>{{ $supplierFilter === 'all' ? 'Semua Supplier' : $supplierFilter }}</span>
                </div>
                <div class="meta-item">
                    <strong>Periode</strong>
                    <span>{{ $periodLabel ?? ($month ? \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') : 'Semua Periode') }}</span>
                </div>
                <div class="meta-item">
                    <strong>Tanggal Cetak</strong>
                    <span>{{ now()->translatedFormat('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <strong>Status</strong>
                    <span>Rekap penjualan produk supplier</span>
                </div>
            </div>

            <form method="GET" action="/admin/reports/suppliers" class="filter-form no-print">
                <div class="filter-group">
                    <label>Supplier</label>
                    <select name="supplier_filter">
                        <option value="all" {{ $supplierFilter === 'all' ? 'selected' : '' }}>Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->name }}" {{ $supplierFilter === $supplier->name ? 'selected' : '' }}>
                                {{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>Bulan</label>
                    <input type="month" name="month" value="{{ old('month', $month ?? now()->format('Y-m')) }}">
                </div>
                <div class="filter-group">
                    <button type="submit">🔎 Tampilkan</button>
                </div>
            </form>

            {{-- ====== RINGKASAN KEUANGAN KESELURUHAN ====== --}}
            @php
                $grandTotalNilaiJual = collect($salesReport['supplier_breakdown'])->sum('total_nilai_jual');
                $grandTotalKeuntunganToko = collect($salesReport['supplier_breakdown'])->sum('total_keuntungan_toko');
                $grandTotalBayarSupplier = collect($salesReport['supplier_breakdown'])->sum('total_bayar_supplier');
            @endphp

            <div
                style="margin-bottom: 30px; border: 2px solid #10b981; border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);">
                <div
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; padding: 14px 20px; font-weight: 800; font-size: 15px; display: flex; align-items: center; justify-content: space-between;">
                    <span>📊 RINGKASAN KEUANGAN KESELURUHAN</span>
                    <span
                        style="font-size: 12px; font-weight: normal; background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 20px;">Dewi
                        Lestari 2</span>
                </div>
                <div
                    style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; divide-x: 1px solid #e5e7eb;">
                    <div style="padding: 18px 20px; border-bottom: 1px solid #f3f4f6;">
                        <div
                            style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #6b7280; letter-spacing: 0.5px;">
                            Total Nilai Jual (Omset)</div>
                        <div style="font-size: 22px; font-weight: 800; color: #2563eb; margin-top: 6px;">Rp
                            {{ number_format($grandTotalNilaiJual, 0, ',', '.') }}</div>
                    </div>
                    <div style="padding: 18px 20px; border-bottom: 1px solid #f3f4f6;">
                        <div
                            style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #6b7280; letter-spacing: 0.5px;">
                            Total Keuntungan Toko</div>
                        <div style="font-size: 22px; font-weight: 800; color: #10b981; margin-top: 6px;">Rp
                            {{ number_format($grandTotalKeuntunganToko, 0, ',', '.') }}</div>
                    </div>
                    <div style="padding: 18px 20px; background: #ecfdf5; border-bottom: 1px solid #f3f4f6;">
                        <div
                            style="font-size: 11px; text-transform: uppercase; font-weight: 800; color: #047857; letter-spacing: 0.5px;">
                            TOTAL DIBAYAR KE SUPPLIER</div>
                        <div style="font-size: 22px; font-weight: 800; color: #047857; margin-top: 6px;">Rp
                            {{ number_format($grandTotalBayarSupplier, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            {{-- ====== DETAIL PER SUPPLIER (TABEL UTAMA) ====== --}}
            @forelse($salesReport['supplier_breakdown'] as $supplierIndex => $supplier)
                <div
                    style="margin-bottom: 40px; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); page-break-inside: avoid;">

                    {{-- Header Supplier --}}
                    <div
                        style="background: #111827; color: white; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0; font-size: 16px; font-weight: 800; letter-spacing: 0.5px;">
                                🏢 LAPORAN SUPPLIER: <span
                                    style="color: #34d399;">{{ strtoupper($supplier['supplier_name']) }}</span>
                            </h3>
                        </div>
                        <div style="font-size: 12px; color: #9ca3af;">
                            Periode: <strong style="color: white;">{{ $periodLabel }}</strong>
                        </div>
                    </div>

                    {{-- Tabel Produk --}}
                    <div class="table-wrapper" style="overflow-x: auto;">
                        <table class="admin-table" style="margin-bottom: 0; width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                                    <th style="width: 4%; padding: 12px 10px; text-align: center;">No</th>
                                    <th style="width: 14%; padding: 12px 10px;">Tanggal Dibeli</th>
                                    <th style="padding: 12px 10px;">Nama Produk</th>
                                    <th style="text-align: center; padding: 12px 10px; width: 10%;">Ukuran Berat</th>
                                    <th style="text-align: center; padding: 12px 10px; width: 9%;">Sisa Stok</th>
                                    <th style="text-align: center; padding: 12px 10px; width: 8%;">Terjual</th>
                                    <th style="text-align: right; padding: 12px 10px; width: 12%;">Harga Titip</th>
                                    <th style="text-align: right; padding: 12px 10px; width: 12%;">Harga Jual</th>
                                    <th style="text-align: right; padding: 12px 10px; width: 12%;">Keuntungan</th>
                                    <th style="text-align: right; padding: 12px 10px; width: 14%;">Bayar ke Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rowNo = 0; @endphp
                                @forelse($supplier['products'] as $product)
                                    @php $rowNo++; @endphp
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 10px; text-align: center;">{{ $rowNo }}</td>
                                        <td style="padding: 12px 10px; font-size: 12px; color: #4b5563;">
                                            {{ $product['last_transaction_date'] ? \Carbon\Carbon::parse($product['last_transaction_date'])->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td style="padding: 12px 10px; font-weight: 600; color: #111827;">
                                            {{ $product['product_name'] }}</td>
                                        <td style="text-align: center; padding: 12px 10px; font-weight: bold; color: #2563eb;">
                                            {{ !empty($product['weight']) && $product['weight'] !== '-' ? $product['weight'] : '' }}</td>
                                        <td style="text-align: center; padding: 12px 10px; font-weight: 600; color: #4b5563;">
                                            {{ number_format($product['sisa_stok'] ?? 0, 0, ',', '.') }}</td>
                                        <td style="text-align: center; padding: 12px 10px; font-weight: bold; color: #111827;">
                                            {{ number_format($product['quantity_sold'], 0, ',', '.') }} pcs</td>
                                        <td style="text-align: right; padding: 12px 10px;">Rp
                                            {{ number_format($product['harga_titip'] ?? 0, 0, ',', '.') }}</td>
                                        <td style="text-align: right; padding: 12px 10px;">Rp
                                            {{ number_format($product['harga_jual'] ?? 0, 0, ',', '.') }}</td>
                                        <td
                                            style="text-align: right; padding: 12px 10px; font-weight: 600; color: {{ ($product['keuntungan'] ?? 0) >= 0 ? '#10b981' : '#ef4444' }};">
                                            Rp {{ number_format($product['keuntungan'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td style="text-align: right; padding: 12px 10px; font-weight: 800; color: #047857;">
                                            Rp {{ number_format($product['bayar_supplier'] ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" style="text-align: center; color: #6b7280; padding: 24px;">Belum ada data
                                            transaksi produk supplier ini pada periode terpilih</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(!empty($supplier['products']))
                                <tfoot>
                                    <tr style="font-weight: bold; background: #f9fafb; border-top: 2px solid #d1d5db;">
                                        <td colspan="5" style="text-align: left; padding: 14px 10px; font-size: 13px;">TOTAL
                                            {{ strtoupper($supplier['supplier_name']) }}</td>
                                        <td style="text-align: center; padding: 14px 10px; font-size: 14px;">
                                            {{ number_format($supplier['quantity_sold'], 0, ',', '.') }} pcs</td>
                                        <td style="padding: 14px 10px;">-</td>
                                        <td style="padding: 14px 10px;">-</td>
                                        <td style="text-align: right; padding: 14px 10px; font-size: 14px; color: #10b981;">Rp
                                            {{ number_format($supplier['total_keuntungan_toko'] ?? 0, 0, ',', '.') }}</td>
                                        <td style="text-align: right; padding: 14px 10px; font-size: 15px; color: #047857;">Rp
                                            {{ number_format($supplier['total_bayar_supplier'] ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    {{-- Sub-Ringkasan Supplier --}}
                    @if(!empty($supplier['products']))
                        <div
                            style="background: #f0fdf4; padding: 16px 20px; border-top: 1px solid #d1fae5; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                            <div style="font-size: 13px; color: #065f46;">
                                📈 <strong>Total Omzet Penjualan (Nilai Jual):</strong> <span style="font-weight: bold;">Rp
                                    {{ number_format($supplier['total_nilai_jual'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div style="font-size: 13.5px; color: #065f46;">
                                💰 <strong>Total Yang Harus Dibayar ke Supplier:</strong> <span
                                    style="color: #047857; font-weight: 800; font-size: 16px;">Rp
                                    {{ number_format($supplier['total_bayar_supplier'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- TABEL RETUR BARANG KADALUARSA SUPPLIER --}}
                    @if(!empty($supplier['manually_expired_products']) && count($supplier['manually_expired_products']) > 0)
                        <div style="margin-top: 24px; border-top: 2px dashed #fca5a5; padding: 20px;">
                            <h4 style="margin: 0 0 12px 0; color: #dc2626; font-size: 14.5px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                                <span>⚠️</span> TABEL RETUR / BARANG KADALUARSA ({{ strtoupper($supplier['supplier_name']) }})
                            </h4>
                            <div class="table-wrapper" style="overflow-x: auto;">
                                <table class="admin-table" style="margin-bottom: 0; width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background-color: #fef2f2; border-bottom: 2px solid #fecaca;">
                                            <th style="width: 5%; padding: 10px; text-align: center;">No</th>
                                            <th style="width: 16%; padding: 10px;">Tanggal Retur</th>
                                            <th style="padding: 10px;">Nama Produk</th>
                                            <th style="text-align: center; padding: 10px; width: 14%;">Ukuran</th>
                                            <th style="text-align: center; padding: 10px; width: 16%;">Jumlah Stok (Qty Retur)</th>
                                            <th style="text-align: right; padding: 10px; width: 18%;">Jumlah Harga (Rugi HPP)</th>
                                            <th style="padding: 10px;">Catatan / Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $returNo = 0; @endphp
                                        @foreach($supplier['manually_expired_products'] as $retur)
                                            @php $returNo++; @endphp
                                            <tr style="border-bottom: 1px solid #fee2e2;">
                                                <td style="padding: 10px; text-align: center;">{{ $returNo }}</td>
                                                <td style="padding: 10px; font-size: 12px; color: #4b5563;">
                                                    {{ !empty($retur['date_logged']) ? \Carbon\Carbon::parse($retur['date_logged'])->translatedFormat('d M Y') : '-' }}
                                                </td>
                                                <td style="padding: 10px; font-weight: 600; color: #111827;">{{ $retur['item_name'] }}</td>
                                                <td style="text-align: center; padding: 10px; font-weight: bold; color: #dc2626;">{{ $retur['weight'] ?: '-' }}</td>
                                                <td style="text-align: center; padding: 10px; font-weight: bold; color: #dc2626;">{{ number_format($retur['quantity'], 0, ',', '.') }} pcs</td>
                                                <td style="text-align: right; padding: 10px; font-weight: bold; color: #991b1b;">Rp {{ number_format($retur['loss_value'], 0, ',', '.') }}</td>
                                                <td style="padding: 10px; font-size: 12px; color: #64748b;">{{ $retur['description'] ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="font-weight: bold; background: #fef2f2; border-top: 2px solid #fca5a5;">
                                            <td colspan="4" style="padding: 12px 10px; text-align: left; font-size: 13px; color: #991b1b;">TOTAL RETUR / KADALUARSA</td>
                                            <td style="text-align: center; padding: 12px 10px; font-size: 13.5px; color: #dc2626;">{{ number_format($supplier['total_expired_qty'] ?? 0, 0, ',', '.') }} pcs</td>
                                            <td style="text-align: right; padding: 12px 10px; font-size: 14px; color: #991b1b;">Rp {{ number_format($supplier['total_expired_loss'] ?? 0, 0, ',', '.') }}</td>
                                            <td style="padding: 12px 10px;">-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div
                    style="text-align:center; padding: 40px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px;">
                    <span style="font-size: 18px; color: #6b7280; display: block; margin-bottom: 10px;">📦</span>
                    Tidak ada data supplier atau penjualan untuk ditampilkan.
                </div>
            @endforelse

            {{-- ====== CHART (NO-PRINT) ====== --}}
            <div class="no-print"
                style="margin-top: 40px; background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 40px;">
                <h3 class="section-title" style="margin-top: 0;">📊 Grafis Omzet Penjualan Per Supplier</h3>
                <div style="height: 280px; position: relative;">
                    <canvas id="supplierChart"></canvas>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('supplierChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($supplierChartLabels ?? []) !!},
                            datasets: [{
                                label: 'Total Nilai Jual (Rp)',
                                data: {!! json_encode($supplierChartValues ?? []) !!},
                                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                borderColor: '#059669',
                                borderWidth: 1.5,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>

            <div class="footer-note">
                <strong>Keterangan:</strong> Laporan ini merupakan rekapitulasi settlement produk supplier. Harga titip dan
                harga jual ditarik langsung dari manajemen stok produk.
            </div>

            <div class="signature-area">
                <div>
                    <p>Dibuat Oleh,</p>
                    <div style="width: 180px; height: 65px; border-bottom: 1px solid #000; margin: 0 auto 8px;"></div>
                    <div class="signature-line">Admin Toko Dewi Lestari 2</div>
                </div>
                <div>
                    <p>Perwakilan Supplier,</p>
                    <div style="width: 180px; height: 65px; border-bottom: 1px solid #000; margin: 0 auto 8px;"></div>
                    <div class="signature-line">Supplier</div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 25px; color: #9ca3af; font-size: 12px;">
                Laporan ini dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} | Toko Dewi Lestari 2
            </div>
        </div>
    </div>
@endsection