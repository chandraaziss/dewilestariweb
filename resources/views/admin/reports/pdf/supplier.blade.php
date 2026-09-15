<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LAPORAN SUPPLIER E-COMMERCE</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; }
        .document { width: 100%; padding: 15px; box-sizing: border-box; }
        .title { text-align: center; font-size: 15px; font-weight: bold; margin: 0; color: #0f172a; text-transform: uppercase; }
        .subtitle { text-align: center; margin: 2px 0 14px; font-size: 11px; color: #475569; }
        
        .meta { margin-bottom: 15px; line-height: 1.5; background: #f8fafc; padding: 8px 12px; border-radius: 4px; border: 1px solid #cbd5e1; }
        .meta span { display: block; font-size: 9.5px; }

        .summary-box { border: 2px solid #10b981; margin-bottom: 20px; background: #fff; }
        .summary-header { background: #10b981; color: #fff; padding: 6px 10px; font-weight: bold; font-size: 11px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; }
        .summary-highlight { background: #ecfdf5; font-weight: bold; }

        .supplier-block { margin-bottom: 25px; page-break-inside: avoid; border: 1px solid #cbd5e1; }
        .supplier-header { background: #111827; color: #fff; padding: 6px 10px; font-size: 11px; font-weight: bold; }

        table.supplier-table { width: 100%; border-collapse: collapse; }
        table.supplier-table th, table.supplier-table td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; font-size: 9px; }
        table.supplier-table th { background: #f8fafc; font-weight: bold; color: #334155; }
        .numeric { text-align: right; }
        .center { text-align: center; }
        .footer-row { background: #f8fafc; font-weight: bold; }

        .supplier-foot { background: #ecfdf5; padding: 6px 10px; font-size: 9.5px; border-top: 1px solid #cbd5e1; }

        .signature-area { margin-top: 25px; width: 100%; }
        .signature-area td { width: 50%; text-align: center; vertical-align: top; padding: 5px; }
        .sig-space { height: 45px; }
        .sig-line { border-top: 1px solid #000; display: inline-block; width: 150px; font-weight: bold; font-size: 9px; padding-top: 3px; }

        .print-footer { text-align: center; font-size: 8.5px; color: #94a3b8; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="document">
        <p class="title">LAPORAN PENJUALAN SUPPLIER</p>
        <p class="subtitle">Toko Dewi Lestari 2 (E-Commerce Settlement)</p>

        <div class="meta">
            <span><strong>Supplier:</strong> {{ $supplierFilter === 'all' ? 'Semua Supplier' : $supplierFilter }}</span>
            <span><strong>Periode:</strong> {{ $periodLabel ?? ($month ? \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') : 'Semua Periode') }}</span>
            <span><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }}</span>
        </div>

        @php
            $grandTotalNilaiJual = collect($salesReport['supplier_breakdown'])->sum('total_nilai_jual');
            $grandTotalKeuntunganToko = collect($salesReport['supplier_breakdown'])->sum('total_keuntungan_toko');
            $grandTotalBayarSupplier = collect($salesReport['supplier_breakdown'])->sum('total_bayar_supplier');
        @endphp

        <div class="summary-box">
            <div class="summary-header">📊 RINGKASAN KEUANGAN KESELURUHAN</div>
            <table class="summary-table">
                <tr>
                    <td style="width: 33%;"><strong>Total Nilai Jual (Omset)</strong><br><span style="font-size: 11px; font-weight: bold; color: #2563eb;">Rp {{ number_format($grandTotalNilaiJual, 0, ',', '.') }}</span></td>
                    <td style="width: 33%;"><strong>Total Keuntungan Toko</strong><br><span style="font-size: 11px; font-weight: bold; color: #10b981;">Rp {{ number_format($grandTotalKeuntunganToko, 0, ',', '.') }}</span></td>
                    <td class="summary-highlight" style="width: 34%;"><strong>TOTAL DIBAYAR KE SUPPLIER</strong><br><span style="font-size: 11px; font-weight: bold; color: #047857;">Rp {{ number_format($grandTotalBayarSupplier, 0, ',', '.') }}</span></td>
                </tr>
            </table>
        </div>

        @forelse($salesReport['supplier_breakdown'] as $index => $supplier)
            <div class="supplier-block">
                <div class="supplier-header">🏢 LAPORAN SUPPLIER: {{ strtoupper($supplier['supplier_name']) }}</div>

                <table class="supplier-table">
                    <thead>
                        <tr>
                            <th class="center" style="width: 4%;">No</th>
                            <th style="width: 14%;">Tanggal Dibeli</th>
                            <th>Nama Produk</th>
                            <th class="center" style="width: 10%;">Ukuran Berat</th>
                            <th class="center" style="width: 8%;">Sisa Stok</th>
                            <th class="center" style="width: 8%;">Terjual</th>
                            <th class="numeric" style="width: 12%;">Harga Titip</th>
                            <th class="numeric" style="width: 12%;">Harga Jual</th>
                            <th class="numeric" style="width: 12%;">Keuntungan</th>
                            <th class="numeric" style="width: 14%;">Bayar ke Supplier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNo = 0; @endphp
                        @forelse($supplier['products'] as $product)
                            @php $rowNo++; @endphp
                            <tr>
                                <td class="center">{{ $rowNo }}</td>
                                <td>{{ $product['last_transaction_date'] ? \Carbon\Carbon::parse($product['last_transaction_date'])->translatedFormat('d M Y') : '-' }}</td>
                                <td style="font-weight: bold;">{{ $product['product_name'] }}</td>
                                <td class="center">{{ !empty($product['weight']) && $product['weight'] !== '-' ? $product['weight'] : '' }}</td>
                                <td class="center">{{ number_format($product['sisa_stok'] ?? 0, 0, ',', '.') }}</td>
                                <td class="center">{{ number_format($product['quantity_sold'], 0, ',', '.') }} bungkus</td>
                                <td class="numeric">Rp {{ number_format($product['harga_titip'] ?? 0, 0, ',', '.') }}</td>
                                <td class="numeric">Rp {{ number_format($product['harga_jual'] ?? 0, 0, ',', '.') }}</td>
                                <td class="numeric" style="color: #10b981;">Rp {{ number_format($product['keuntungan'] ?? 0, 0, ',', '.') }}</td>
                                <td class="numeric" style="font-weight: bold; color: #047857;">Rp {{ number_format($product['bayar_supplier'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="center">Belum ada transaksi produk supplier ini</td></tr>
                        @endforelse
                    </tbody>
                    @if(!empty($supplier['products']))
                        <tfoot>
                            <tr class="footer-row">
                                <td colspan="5">TOTAL {{ strtoupper($supplier['supplier_name']) }}</td>
                                <td class="center">{{ number_format($supplier['quantity_sold'], 0, ',', '.') }} bungkus</td>
                                <td class="center">-</td>
                                <td class="center">-</td>
                                <td class="numeric" style="color: #10b981;">Rp {{ number_format($supplier['total_keuntungan_toko'] ?? 0, 0, ',', '.') }}</td>
                                <td class="numeric" style="color: #047857;">Rp {{ number_format($supplier['total_bayar_supplier'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>

                @if(!empty($supplier['products']))
                    <div class="supplier-foot">
                        <strong>Total Omzet Penjualan (Nilai Jual):</strong> Rp {{ number_format($supplier['total_nilai_jual'] ?? 0, 0, ',', '.') }} &nbsp;|&nbsp;
                        <strong>Total Yang Harus Dibayar ke Supplier:</strong> Rp {{ number_format($supplier['total_bayar_supplier'] ?? 0, 0, ',', '.') }}
                    </div>
                @endif

                @if(!empty($supplier['manually_expired_products']) && count($supplier['manually_expired_products']) > 0)
                    <div style="margin-top: 10px; border-top: 1px dashed #ef4444; padding-top: 8px;">
                        <div style="font-weight: bold; color: #dc2626; font-size: 10px; margin-bottom: 5px;">⚠️ TABEL RETUR / BARANG KADALUARSA ({{ strtoupper($supplier['supplier_name']) }})</div>
                        <table class="supplier-table">
                            <thead>
                                <tr style="background: #fef2f2;">
                                    <th class="center" style="width: 5%;">No</th>
                                    <th style="width: 16%;">Tanggal Retur</th>
                                    <th>Nama Produk</th>
                                    <th class="center" style="width: 12%;">Ukuran</th>
                                    <th class="center" style="width: 16%;">Jumlah Stok</th>
                                    <th class="numeric" style="width: 20%;">Jumlah Harga (Rugi HPP)</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rNo = 0; @endphp
                                @foreach($supplier['manually_expired_products'] as $retur)
                                    @php $rNo++; @endphp
                                    <tr>
                                        <td class="center">{{ $rNo }}</td>
                                        <td>{{ !empty($retur['date_logged']) ? \Carbon\Carbon::parse($retur['date_logged'])->translatedFormat('d M Y') : '-' }}</td>
                                        <td style="font-weight: bold;">{{ $retur['item_name'] }}</td>
                                        <td class="center">{{ $retur['weight'] ?: '-' }}</td>
                                        <td class="center" style="color: #dc2626; font-weight: bold;">{{ number_format($retur['quantity'], 0, ',', '.') }} bungkus</td>
                                        <td class="numeric" style="color: #991b1b; font-weight: bold;">Rp {{ number_format($retur['loss_value'], 0, ',', '.') }}</td>
                                        <td style="font-size: 8.5px;">{{ $retur['description'] ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: bold; background: #fef2f2;">
                                    <td colspan="4">TOTAL RETUR / KADALUARSA</td>
                                    <td class="center" style="color: #dc2626;">{{ number_format($supplier['total_expired_qty'] ?? 0, 0, ',', '.') }} bungkus</td>
                                    <td class="numeric" style="color: #991b1b;">Rp {{ number_format($supplier['total_expired_loss'] ?? 0, 0, ',', '.') }}</td>
                                    <td>-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        @empty
            <div class="center" style="padding: 20px; border: 1px solid #cbd5e1;">Belum ada data supplier</div>
        @endforelse

        <table class="signature-area">
            <tr>
                <td>
                    <p>Dibuat Oleh,</p>
                    <div class="sig-space"></div>
                    <div class="sig-line">Admin Toko Dewi Lestari 2</div>
                </td>
                <td>
                    <p>Perwakilan Supplier,</p>
                    <div class="sig-space"></div>
                    <div class="sig-line">Supplier</div>
                </td>
            </tr>
        </table>

        <div class="print-footer">
            Laporan ini dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} | Toko Dewi Lestari 2
        </div>
    </div>
</body>
</html>
