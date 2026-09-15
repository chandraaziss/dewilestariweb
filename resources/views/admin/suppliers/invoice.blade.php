@extends('admin.layout')

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
    }

    .invoice-top-kop {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .brand-logo-circle {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        background: radial-gradient(circle, #ffeb3b 0%, #facc15 70%, #eab308 100%);
        border: 4px solid #2e7d32;
        box-shadow: 0 4px 10px rgba(46, 125, 50, 0.25), inset 0 0 10px rgba(255, 255, 255, 0.8);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #2e7d32;
        text-align: center;
        font-weight: 900;
        line-height: 1;
    }

    .brand-logo-circle .text-dl-icon {
        font-size: 26px;
        font-weight: 900;
        color: #2e7d32;
        letter-spacing: -1px;
        text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.9);
    }

    .brand-logo-circle .text-sub {
        font-size: 8px;
        font-weight: 800;
        color: #166534;
        margin-top: 1px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .brand-logo-circle .text-lestari {
        font-size: 10px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    .brand-logo-circle .text-number {
        font-size: 14px;
        color: #fef08a;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.6);
    }

    .kop-text-right {
        text-align: right;
    }

    .kop-text-right h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #000000;
        letter-spacing: 0.5px;
    }

    .kop-text-right p {
        margin: 4px 0 0;
        font-size: 12px;
        color: #334155;
    }

    .invoice-title-banner {
        text-align: center;
        margin-bottom: 20px;
    }

    .invoice-title-banner h1 {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: 12px;
        text-transform: uppercase;
        margin: 0;
        padding: 10px 0;
        border-top: 3px double #000000;
        border-bottom: 3px double #000000;
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
        line-height: 1.5;
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
        padding: 8px;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        text-align: center;
        background: #f8fafc;
    }

    .invoice-main-table td {
        border: 1px solid #000000;
        padding: 8px;
        font-size: 11.5px;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-bold { font-weight: bold; }

    .summary-section-box {
        border-left: 2px solid #000000;
        border-right: 2px solid #000000;
        border-bottom: 2px solid #000000;
        padding: 12px 16px;
        font-size: 12px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
    }

    .signature-area-formal {
        margin-top: 40px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
        text-align: center;
    }

    .sig-box-line {
        margin-top: 60px;
        border-top: 1px solid #000000;
        display: inline-block;
        width: 200px;
        font-weight: bold;
        padding-top: 4px;
    }

    @media print {
        body {
            background: #ffffff !important;
        }
        .admin-sidebar, .admin-navbar, .no-print {
            display: none !important;
        }
        .invoice-document {
            border: none;
            box-shadow: none;
            padding: 0;
            margin: 0;
            max-width: 100%;
        }
    }
</style>

<div class="admin-container">

    {{-- Action Bar (No Print) --}}
    <div class="no-print" style="max-width: 950px; margin: 0 auto 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <a href="/admin/suppliers/{{ $supplier->slug ?? 'all' }}" class="btn btn-warning" style="padding: 8px 16px; font-weight: 600; text-decoration: none; border-radius: 8px;">
            ← Kembali ke Katalog Supplier
        </a>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary" style="padding: 8px 18px; font-weight: bold; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                🖨️ Cetak / Download PDF
            </button>
            <a href="{{ $waUrl }}" target="_blank" class="btn btn-success" style="padding: 8px 18px; font-weight: bold; background: #16a34a; color: white; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
                📲 Kirim Pesanan via WhatsApp
            </a>
        </div>
    </div>

    {{-- Formal Invoice Document --}}
    <div class="invoice-document">
        
        {{-- Kop Atas --}}
        <div class="invoice-top-kop">
            <div class="brand-logo-circle">
                <div class="text-dl-icon">DL</div>
                <div class="text-sub">Dewi Lestari 2</div>
            </div>
            <div class="kop-text-right">
                <h2>Toko Dewi Lestari 2</h2>
                <p>Jl. Raya Cimindi No.59, Pasirkaliki, Kec. Cimahi Utara, Kota Cimahi, Jawa Barat 40535</p>
                <p>Telp: 0812-2195-6759 | Email: info@dewilestari2.com</p>
            </div>
        </div>

        {{-- Banner Title --}}
        <div class="invoice-title-banner">
            <h1>I N V O I C E</h1>
        </div>

        @php
            $returns = \App\Models\SupplierReturn::where('supplier_order_id', $order->id)->get();
            $totalReturCount = $returns->sum('quantity');

            $grandTotalOrderedPcs = 0;
            $grandTotalReturPcs = 0;
            $grandTotalPcs = 0;
            $grandTotalAmount = 0;
            $itemsData = [];

            // Expand items
            $flatItems = [];
            foreach ($order->items ?? [] as $item) {
                $itemName = $item['name'] ?? '';
                $itemSize = $item['size'] ?? ($item['variant'] ?? '');
                $qtyStr = $item['quantity'] ?? '1';

                $sizes = array_values(array_filter(array_map('trim', explode(',', $itemSize))));
                $quantities = array_values(array_filter(array_map('trim', explode(',', $qtyStr))));

                if (count($sizes) > 1 || count($quantities) > 1) {
                    $maxCount = max(count($sizes), count($quantities));
                    for ($i = 0; $i < $maxCount; $i++) {
                        $flatItems[] = [
                            'name' => $itemName,
                            'size' => $sizes[$i] ?? ($sizes[0] ?? ''),
                            'quantity' => $quantities[$i] ?? ($quantities[0] ?? '1'),
                        ];
                    }
                } else {
                    $flatItems[] = [
                        'name' => $itemName,
                        'size' => $itemSize,
                        'quantity' => $qtyStr,
                    ];
                }
            }

            foreach ($flatItems as $index => $item) {
                $itemName = $item['name'] ?? '';
                $itemSize = $item['size'] ?? '';
                $qtyStr = $item['quantity'] ?? '1';
                $qtyOrdered = (int) preg_replace('/[^0-9]/', '', $qtyStr);
                if ($qtyOrdered <= 0) $qtyOrdered = 1;

                // Match returns for this specific item & size
                $returQty = 0;
                foreach ($returns as $ret) {
                    if (strtolower(trim($ret->item_name)) === strtolower(trim($itemName))) {
                        if (!$itemSize || !$ret->weight || str_contains(strtolower($ret->weight), strtolower($itemSize)) || str_contains(strtolower($itemSize), strtolower($ret->weight))) {
                            $returQty += (int) $ret->quantity;
                        }
                    }
                }

                $qtyReceived = max(0, $qtyOrdered - $returQty);

                // Match with SupplierStock for price
                $matchStock = \App\Models\SupplierStock::whereRaw('LOWER(item_name) = ?', [strtolower(trim($itemName))])->first();
                $unitPrice = 0;

                if ($matchStock) {
                    $variants = is_array($matchStock->variants) ? $matchStock->variants : (json_decode($matchStock->variants, true) ?: []);
                    $matchedVariant = null;

                    if (!empty($itemSize) && count($variants) > 0) {
                        $targetSize = strtolower(trim($itemSize));
                        foreach ($variants as $v) {
                            $vWeight = strtolower(trim($v['weight'] ?? ''));
                            if (!empty($vWeight) && (str_contains($targetSize, $vWeight) || str_contains($vWeight, $targetSize))) {
                                $matchedVariant = $v;
                                break;
                            }
                        }
                    }

                    if (!$matchedVariant && count($variants) > 0) {
                        $matchedVariant = $variants[0];
                    }

                    if ($matchedVariant) {
                        if (!empty($matchedVariant['buy_price'])) {
                            $unitPrice = (float) $matchedVariant['buy_price'];
                        } elseif (!empty($matchedVariant['price'])) {
                            $unitPrice = (float) $matchedVariant['price'] * 0.75;
                        }
                    }
                }

                if ($unitPrice <= 0) {
                    $unitPrice = 15000;
                }

                $lineTotal = $qtyReceived * $unitPrice;

                $grandTotalOrderedPcs += $qtyOrdered;
                $grandTotalReturPcs += $returQty;
                $grandTotalPcs += $qtyReceived;
                $grandTotalAmount += $lineTotal;

                $itemsData[] = [
                    'no' => $index + 1,
                    'date' => $order->created_at ? $order->created_at->format('d/m/Y') : date('d/m/Y'),
                    'qty_ordered' => $qtyOrdered,
                    'qty_retur' => $returQty,
                    'qty_received' => $qtyReceived,
                    'item' => $itemName . (!empty($itemSize) ? ' (' . $itemSize . ')' : ''),
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }
        @endphp

        {{-- Top 3-Box Information Grid --}}
        <div class="invoice-top-grid">
            <div class="invoice-top-box">
                <span class="box-heading"><u>{{ $supplier->name ?? 'Supplier' }}</u></span>
                <div>Kawasan Industri Pangan No. 42</div>
                <div>Jl. Kliningan III, Turangga, Lengkong</div>
                <div>Kota Bandung, Jawa Barat 40264, Indonesia</div>
                <div style="margin-top: 4px;">Telp: {{ $supplier->phone ?? '-' }}</div>
            </div>

            <div class="invoice-top-box">
                <span class="box-heading"><u>Purchase Order :</u></span>
                <table class="po-table-info">
                    <tr>
                        <td style="width: 110px;">Nomor</td>
                        <td style="width: 10px;">:</td>
                        <td><strong>{{ $order->invoice_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>Tanggal Pesan</td>
                        <td>:</td>
                        <td>{{ $order->created_at ? $order->created_at->translatedFormat('d F Y') : date('d F Y') }}</td>
                    </tr>
                </table>
                <div class="po-grand-total">
                    TOTAL TAGIHAN DITERIMA: <span style="color: #166534;">Rp. {{ number_format($grandTotalAmount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="invoice-top-box">
                <span class="box-heading"><u>Alamat Kirim :</u></span>
                <div style="font-weight: bold; font-size: 12.5px; color: #1e293b;">Toko Dewi Lestari 2</div>
                <div>Jalan Raya Cimindi No.59, Pasirkaliki, Kec. Cimahi Utara</div>
                <div>Kota Cimahi, Jawa Barat 40535, Indonesia</div>
                <div style="margin-top: 4px;">Penerima: Admin Stok Toko Dewi Lestari 2</div>
            </div>
        </div>

        @if($totalReturCount > 0)
            <div style="background: #fef2f2; border: 2px solid #ef4444; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; color: #991b1b; font-size: 13px;">
                <div style="font-weight: bold; font-size: 14px; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                    <span>🚨</span> CATATAN RETUR / PEMERIKSAAN BARANG RUSAK
                </div>
                <div>
                    Terdapat komplain/retur produk sebanyak <strong>{{ $totalReturCount }} bungkus</strong>.
                    Tagihan invoice ini telah <strong>otomatis disesuaikan secara bersih</strong> hanya untuk <strong>{{ $grandTotalPcs }} bungkus barang yang diterima dalam kondisi baik</strong>.
                </div>
            </div>
        @endif

        {{-- Main Invoice Table --}}
        <table class="invoice-main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO.</th>
                    <th style="width: 12%;">TGL PESAN</th>
                    <th style="width: 10%;">QTY DIPESAN</th>
                    @if($totalReturCount > 0)
                        <th style="width: 10%; color: #dc2626;">RETUR</th>
                        <th style="width: 10%; color: #15803d;">DITERIMA</th>
                    @endif
                    <th style="width: {{ $totalReturCount > 0 ? '28%' : '38%' }};">ITEM PRODUK</th>
                    <th style="width: 13%;">HARGA SATUAN</th>
                    <th style="width: 14%;">TOTAL BAYAR</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itemsData as $row)
                    <tr>
                        <td class="text-center">{{ $row['no'] }}</td>
                        <td class="text-center">{{ $row['date'] }}</td>
                        <td class="text-center">{{ $row['qty_ordered'] }} bungkus</td>
                        @if($totalReturCount > 0)
                            <td class="text-center" style="color: #dc2626; font-weight: bold;">
                                {{ $row['qty_retur'] > 0 ? '-' . $row['qty_retur'] . ' bungkus' : '-' }}
                            </td>
                            <td class="text-center text-bold" style="color: #15803d;">
                                {{ $row['qty_received'] }} bungkus
                            </td>
                        @endif
                        <td><strong>{{ $row['item'] }}</strong></td>
                        <td class="text-right">Rp {{ number_format($row['unit_price'], 0, ',', '.') }}</td>
                        <td class="text-right text-bold">Rp {{ number_format($row['line_total'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $totalReturCount > 0 ? '8' : '6' }}" class="text-center" style="padding: 20px; color: #64748b;">
                            Tidak ada rincian item pesanan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Summary Box --}}
        <div class="summary-section-box">
            <div class="summary-row" style="border-bottom: 1px dashed #000000; padding-bottom: 6px;">
                <div>
                    <strong>Dipesan: {{ $grandTotalOrderedPcs }} Bungkus</strong>
                    @if($totalReturCount > 0)
                        <span style="color: #dc2626; font-weight: bold; margin-left: 8px;">(Retur/Rusak: {{ $grandTotalReturPcs }} Bungkus)</span>
                        <span style="color: #15803d; font-weight: bold; margin-left: 8px;">| Diterima Bersih: {{ $grandTotalPcs }} Bungkus</span>
                    @endif
                </div>
                <div><strong>TOTAL NET: Rp. {{ number_format($grandTotalAmount, 0, ',', '.') }}</strong></div>
            </div>
            <div class="summary-row" style="border-bottom: 1px solid #000000; padding: 6px 0; color: #475569;">
                <div>Ppn / Pph</div>
                <div>Rp. -</div>
            </div>
            <div class="summary-row" style="padding-top: 8px; font-size: 14px; font-weight: 800;">
                <div>GRAND TOTAL DIHUTANGKAN / DIBAYAR</div>
                <div style="color: #15803d;">Rp. {{ number_format($grandTotalAmount, 0, ',', '.') }}</div>
            </div>
        </div>

        @if(!empty($order->notes))
            <div style="margin-top: 16px; border: 1px solid #000000; padding: 10px 14px; font-size: 11.5px; background: #fffbeb;">
                <strong>Catatan Pesanan:</strong> {{ $order->notes }}
            </div>
        @endif

        {{-- Signature Area --}}
        <div class="signature-area-formal">
            <div>
                <div style="font-size: 11.5px;">Pemesan (Toko)</div>
                <div class="sig-box-line">Toko Dewi Lestari 2</div>
            </div>
            <div>
                <div style="font-size: 11.5px;">Penerima Pesanan (Supplier)</div>
                <div class="sig-box-line">{{ $supplier->name ?? 'Supplier' }}</div>
            </div>
        </div>

    </div>
</div>
@endsection
