@extends('admin.layout')

@section('content')
<style>
    .supplier-page-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        padding: 28px;
        border: 1px solid #e2e8f0;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        padding: 20px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }

    .stat-card strong {
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 6px;
    }

    .stat-card .stat-val {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    .supplier-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 36px;
    }

    .supplier-card-item {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 22px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .supplier-card-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }

    .item-tag {
        display: inline-block;
        background: #f1f5f9;
        color: #334155;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 4px;
        margin-bottom: 6px;
        border: 1px solid #e2e8f0;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 14px;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid #cbd5e1;
    }

    .orders-table th {
        background: #f1f5f9;
        padding: 14px 16px;
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        text-align: left;
        border-bottom: 2px solid #cbd5e1;
        border-right: 1px solid #cbd5e1;
    }

    .orders-table th:last-child {
        border-right: none;
    }

    .orders-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #cbd5e1;
        border-right: 1px solid #e2e8f0;
        font-size: 13.5px;
        color: #334155;
        vertical-align: top;
    }

    .orders-table td:last-child {
        border-right: none;
    }

    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde047;
    }

    .badge-completed {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
</style>

<div class="supplier-page-card">
    
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 class="admin-title" style="margin: 0; font-size: 24px; color: #1e293b;">🏭 Manajemen Supplier & Retur Produk</h1>
            <p style="color: #64748b; margin: 4px 0 0; font-size: 14px;">Kelola mitra supplier, pemesanan stok barang, dan pengajuan retur barang tidak sesuai.</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" onclick="openReturnModal()" class="btn" style="padding: 10px 18px; font-weight: bold; background: #dc2626; color: white; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                ⚠️ Input Retur Produk
            </button>
            <a href="/admin/supplier-stocks" class="btn btn-warning" style="padding: 10px 18px; font-weight: bold; text-decoration: none; border-radius: 8px;">← Kelola Stok</a>
            <a href="/admin/suppliers/manage" class="btn btn-success" style="padding: 10px 18px; font-weight: bold; background: #16a34a; color: white; text-decoration: none; border-radius: 8px;">⚙️ Kelola Supplier</a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 14px 18px; background: #dcfce7; color: #166534; border-radius: 10px; margin-bottom: 24px; border: 1px solid #86efac; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    @php
        $groupedReturns = $returns->groupBy(function($item) {
            if (!empty($item->supplier_order_id)) {
                return 'ORDER-' . $item->supplier_order_id;
            }
            if (!empty($item->return_number)) {
                return 'TICKET-' . $item->return_number;
            }
            return 'SINGLE-' . $item->id;
        });
    @endphp

    {{-- Ringkasan Statistik --}}
    <div class="stat-grid">
        <div class="stat-card" style="border-left: 4px solid #16a34a;">
            <strong>Mitra Supplier Active</strong>
            <div class="stat-val" style="color: #16a34a;">{{ count($suppliers) }} Mitra</div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <strong>Pesanan Menunggu Dikirim</strong>
            <div class="stat-val" style="color: #d97706;">{{ count($orders->where('status', 'pending')) }} Pesanan</div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #2563eb;">
            <strong>Pesanan Selesai / Diterima</strong>
            <div class="stat-val" style="color: #2563eb;">{{ count($orders->where('status', 'completed')) }} Pesanan</div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #dc2626;">
            <strong>Pengajuan Retur Supplier</strong>
            <div class="stat-val" style="color: #dc2626;">{{ count($groupedReturns) }} Dikirim</div>
        </div>
    </div>

    {{-- ====== SECTION 1: DAFTAR SUPPLIER ====== --}}
    <h2 style="font-size: 18px; color: #0f172a; margin-bottom: 16px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
        🏢 Daftar Supplier Mitra
    </h2>

    <div class="supplier-grid">
        @forelse($suppliers as $supplier)
            @php
                $supplierItems = [];
                foreach ($supplier->items ?? [] as $item) {
                    if (is_array($item)) {
                        $supplierItems[] = $item['name'] ?? '';
                    } elseif (is_string($item)) {
                        $supplierItems[] = $item;
                    }
                }
            @endphp
            <div class="supplier-card-item">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                        <div>
                            <span style="font-family: monospace; background: #dcfce7; color: #166534; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 800; border: 1px solid #86efac; display: inline-block; margin-bottom: 4px;">
                                SUPP-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}
                            </span>
                            <h3 style="margin: 0; font-size: 17px; color: #166534; font-weight: 800;">{{ $supplier->name }}</h3>
                        </div>
                        <span style="font-size: 11px; background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 12px; font-weight: 700;">
                            {{ $supplier->orders_count ?? 0 }} Orders
                        </span>
                    </div>

                    <div style="font-size: 13px; color: #475569; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        📞 <span>No. HP: <strong>{{ $supplier->phone ?? '-' }}</strong></span>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <span style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: bold; display: block; margin-bottom: 6px;">Katalog Produk Supplier:</span>
                        @forelse($supplierItems as $itemName)
                            @if($itemName !== '')
                                <span class="item-tag">📦 {{ $itemName }}</span>
                            @endif
                        @empty
                            <span style="color: #94a3b8; font-size: 12px;">Belum ada daftar produk.</span>
                        @endforelse
                    </div>
                </div>

                <div style="display: flex; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 14px; margin-top: 10px;">
                    <a href="/admin/suppliers/{{ $supplier->slug }}" class="btn btn-success" style="flex: 2; padding: 9px; font-size: 13px; font-weight: bold; background: #16a34a; color: white; text-decoration: none; border-radius: 8px; text-align: center;">
                        📲 Pesan Produk
                    </a>
                    <button type="button" onclick="openReturnModalWithSupplier({{ $supplier->id }})" class="btn" style="flex: 2; padding: 9px; font-size: 13px; font-weight: bold; background: #dc2626; color: white; border: none; border-radius: 8px; text-align: center; cursor: pointer;">
                        ⚠️ Retur Produk
                    </button>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 30px; text-align: center; color: #64748b; background: #f8fafc; border-radius: 12px;">
                Belum ada supplier mitra yang terdaftar.
            </div>
        @endforelse
    </div>

    {{-- ====== SECTION 2: DAFTAR PENGAJUAN RETUR PRODUK SUPPLIER ====== --}}
    <div style="margin-top: 36px; border-top: 2px solid #e2e8f0; padding-top: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
            <h2 style="font-size: 18px; color: #b91c1c; margin: 0; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                🔄 Daftar Pengajuan Retur Produk ke Supplier
            </h2>
            <button type="button" onclick="openReturnModal()" class="btn" style="padding: 8px 16px; font-size: 13px; font-weight: bold; background: #dc2626; color: white; border: none; border-radius: 8px; cursor: pointer;">
                ➕ Buat Pengajuan Retur Baru
            </button>
        </div>

        @if(count($returns) > 0)
            @php
                $groupedReturns = $returns->groupBy(function($item) {
                    if (!empty($item->supplier_order_id)) {
                        return 'ORDER-' . $item->supplier_order_id;
                    }
                    if (!empty($item->return_number)) {
                        return 'TICKET-' . $item->return_number;
                    }
                    return 'SINGLE-' . $item->id;
                });
            @endphp
            <div style="overflow-x: auto;">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">No. Retur</th>
                            <th style="width: 130px;">Tanggal</th>
                            <th>Supplier</th>
                            <th>Barang Retur</th>
                            <th style="text-align: center; width: 100px;">Total Qty</th>
                            <th>Surat Retur & Bukti</th>
                            <th style="text-align: center; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedReturns as $retNumber => $group)
                            @php
                                $firstRet = $group->first();
                                $totalQty = $group->sum('quantity');
                            @endphp
                            <tr>
                                <td style="font-weight: 800; color: #dc2626;">
                                    #{{ $firstRet->return_number }}
                                </td>
                                <td style="color: #64748b; font-size: 12.5px;">
                                    {{ $firstRet->created_at ? $firstRet->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                </td>
                                <td style="font-weight: 600; color: #1e293b;">
                                    {{ $firstRet->supplier->name ?? 'Supplier' }}
                                </td>
                                <td>
                                    @foreach($group as $it)
                                        <div style="margin-bottom: {{ $loop->last ? '0' : '10px' }}; border-bottom: {{ $loop->last ? 'none' : '1px solid #cbd5e1' }}; padding-bottom: {{ $loop->last ? '0' : '8px' }};">
                                            <strong>{{ $it->item_name }}</strong>
                                            @if($it->weight)
                                                <span style="font-size: 12px; color: #475569;">({{ $it->weight }})</span>
                                            @endif
                                            <span style="font-size: 12px; color: #dc2626; font-weight: bold; margin-left: 4px;">: {{ $it->quantity }} pcs</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td style="text-align: center; font-weight: bold; color: #dc2626; font-size: 14px;">
                                    {{ $totalQty }} pcs
                                </td>
                                <td style="font-size: 13px; color: #334155; max-width: 280px;">
                                    <div style="margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0;">
                                        <a href="/admin/supplier-returns/{{ $firstRet->id }}/ticket" class="btn" style="padding: 5px 11px; font-size: 11.5px; font-weight: bold; background: #0284c7; color: white; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            📑 Surat Retur
                                        </a>
                                    </div>
                                    @foreach($group as $it)
                                        <div style="margin-bottom: {{ $loop->last ? '0' : '10px' }}; border-bottom: {{ $loop->last ? 'none' : '1px solid #cbd5e1' }}; padding-bottom: {{ $loop->last ? '0' : '8px' }};">
                                            @if(count($group) > 1)
                                                <span style="font-weight: 600; color: #475569;">{{ $it->weight ?: $it->item_name }}:</span>
                                            @endif
                                            <span style="color: #991b1b; font-weight: 500;">{{ $it->reason }}</span>
                                            @if($it->proof_image)
                                                <div style="margin-top: 6px;">
                                                    <a href="{{ asset($it->proof_image) }}" target="_blank" title="Klik untuk lihat foto bukti retur">
                                                        <img src="{{ asset($it->proof_image) }}" alt="Foto Bukti" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; display: inline-block;">
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                        <a href="/admin/supplier-returns/{{ $firstRet->id }}/ticket" class="btn" style="padding: 6px 12px; font-size: 12px; font-weight: bold; background: #25d366; color: white; border-radius: 6px; text-decoration: none;">
                                            📱 Kirim WA
                                        </a>

                                        <form action="/admin/supplier-returns/{{ $firstRet->id }}" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus seluruh riwayat retur dalam tiket ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn" style="padding: 6px 10px; font-size: 12px; font-weight: bold; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="background: #fff5f5; padding: 24px; border-radius: 10px; border: 1px dashed #fecaca; text-align: center; color: #991b1b;">
                Belum ada pengajuan retur produk ke supplier. Klik <strong>"➕ Buat Pengajuan Retur Baru"</strong> untuk mengajukan pengembalian produk tidak sesuai.
            </div>
        @endif
    </div>

    {{-- ====== SECTION 3: RIWAYAT & STATUS PESANAN SUPPLIER ====== --}}
    <div style="margin-top: 36px; border-top: 2px solid #e2e8f0; padding-top: 24px;">
        <h2 style="font-size: 18px; color: #0f172a; margin-bottom: 14px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
            📑 Riwayat & Status Pemesanan Barang ke Supplier
        </h2>

        @if(count($orders) > 0)
            <div style="overflow-x: auto;">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">No. Invoice</th>
                            <th style="width: 150px;">Tanggal Pesanan</th>
                            <th>Nama Supplier</th>
                            <th>Rincian Produk Dipesan</th>
                            <th style="text-align: center; width: 180px;">Status Pesanan</th>
                            <th style="text-align: center; width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td style="font-weight: 800; color: #15803d;">
                                    #{{ $order->invoice_number }}
                                </td>
                                <td style="color: #64748b; font-size: 13px;">
                                    {{ $order->created_at ? $order->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                </td>
                                <td style="font-weight: 600; color: #1e293b;">
                                    {{ $order->supplier->name ?? 'Supplier' }}
                                </td>
                                <td>
                                    @php
                                        $itemList = [];
                                        foreach($order->items ?? [] as $it) {
                                            $itemList[] = ($it['name'] ?? '') . ' (' . ($it['size'] ?? 'std') . ': ' . ($it['quantity'] ?? '1') . ')';
                                        }
                                    @endphp
                                    <strong>{{ implode(', ', $itemList) }}</strong>
                                </td>
                                <td style="text-align: center;">
                                    @if($order->status === 'completed')
                                        <span class="badge-status badge-completed">
                                            ✅ Selesai (Telah Diterima)
                                        </span>
                                    @else
                                        <span class="badge-status badge-pending">
                                            ⏳ Menunggu Pesanan Datang
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                        @if($order->status !== 'completed')
                                            <form action="/admin/supplier-orders/{{ $order->id }}/complete" method="POST" style="margin: 0;">
                                                @csrf
                                                <button type="submit" class="btn btn-success"
                                                    style="padding: 6px 12px; font-size: 12px; font-weight: bold; background: #16a34a; color: white; border: none; border-radius: 6px; cursor: pointer;"
                                                    onclick="return confirm('Apakah pesanan ini sudah fisik diterima dari supplier?');">
                                                    ✅ Telah Diterima
                                                </button>
                                            </form>
                                            <button type="button" onclick="openReturnModalWithOrder({{ $order->id }}, {{ $order->supplier_id }}, '{{ addslashes($order->invoice_number) }}')" class="btn" style="padding: 6px 12px; font-size: 12px; font-weight: bold; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer;" title="Input barang rusak / dikomplain pada pesanan ini">
                                                🚨 Retur / Rusak
                                            </button>
                                        @endif

                                        <a href="/admin/supplier-orders/{{ $order->id }}/invoice" class="btn btn-info"
                                            style="padding: 6px 12px; font-size: 12px; font-weight: bold; background: #0284c7; color: white; border-radius: 6px; text-decoration: none;">
                                            👁️ Invoice
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="background: #f8fafc; padding: 24px; border-radius: 10px; border: 1px dashed #cbd5e1; text-align: center; color: #64748b;">
                Belum ada riwayat pesanan ke supplier.
            </div>
        @endif
    </div>

</div>

<!-- MODAL INPUT RETUR PRODUK -->
<div id="returnModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center; padding: 16px;">
    <div style="background: white; border-radius: 16px; width: 100%; max-width: 550px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
            <h3 style="margin: 0; font-size: 18px; color: #b91c1c; font-weight: 800;">⚠️ Form Input Retur Produk ke Supplier</h3>
            <button type="button" onclick="closeReturnModal()" style="background: none; border: none; font-size: 20px; font-weight: bold; cursor: pointer; color: #64748b;">✕</button>
        </div>

        <form action="/admin/supplier-returns/store" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 13px; color: #334155;">Pilih Supplier Mitra *</label>
                <select name="supplier_id" id="retur_supplier_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($suppliers as $sup)
                        @php
                            $supFlatItems = [];
                            foreach ($sup->items ?? [] as $it) {
                                $supFlatItems[] = [
                                    'name' => $it['name'] ?? '',
                                    'size' => $it['size'] ?? ($it['variant'] ?? ''),
                                    'quantity' => 100,
                                ];
                            }
                        @endphp
                        <option value="{{ $sup->id }}" data-items="{{ json_encode($supFlatItems) }}">{{ $sup->name }} ({{ $sup->phone ?? 'No HP -' }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 13px; color: #334155;">Pilih Invoice Pesanan (Belum Diterima)</label>
                <select name="supplier_order_id" id="retur_supplier_order_id" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
                    <option value="">-- Tanpa Invoice / Retur Umum --</option>
                    @foreach($orders as $ord)
                        @if($ord->status !== 'completed')
                            @php
                                $orderFlatItems = [];
                                foreach ($ord->items ?? [] as $it) {
                                    $itName = $it['name'] ?? '';
                                    $itSizeStr = $it['size'] ?? ($it['variant'] ?? '');
                                    $itQtyStr = $it['quantity'] ?? '1';

                                    $sizes = array_values(array_filter(array_map('trim', explode(',', $itSizeStr))));
                                    $quantities = array_values(array_filter(array_map('trim', explode(',', $itQtyStr))));

                                    if (count($sizes) > 1 || count($quantities) > 1) {
                                        $maxCount = max(count($sizes), count($quantities));
                                        for ($i = 0; $i < $maxCount; $i++) {
                                            $orderFlatItems[] = [
                                                'name' => $itName,
                                                'size' => $sizes[$i] ?? ($sizes[0] ?? ''),
                                                'quantity' => $quantities[$i] ?? ($quantities[0] ?? '1'),
                                            ];
                                        }
                                    } else {
                                        $orderFlatItems[] = [
                                            'name' => $itName,
                                            'size' => $itSizeStr,
                                            'quantity' => $itQtyStr,
                                        ];
                                    }
                                }
                            @endphp
                            <option value="{{ $ord->id }}" data-supplier-id="{{ $ord->supplier_id }}" data-items="{{ json_encode($orderFlatItems) }}">
                                #{{ $ord->invoice_number }} - {{ $ord->supplier->name ?? 'Supplier' }} ({{ $ord->created_at ? $ord->created_at->format('d M Y') : '' }})
                            </option>
                        @endif
                    @endforeach
                </select>
                <small style="color: #64748b; font-size: 11px; display: block; margin-top: 4px;">ℹ️ Pilih invoice untuk otomatis mendeteksi rincian produk & ukurannya.</small>
            </div>

            <div id="retur-items-wrapper" style="display:flex; flex-direction:column; gap:12px; margin-bottom:16px;">
                {{-- Rendered dynamically by JS --}}
            </div>

            <div style="margin-bottom: 20px;">
                <button type="button" class="btn" onclick="addReturItemRow()" style="padding: 9px 15px; font-weight: bold; background: #eff6ff; color: #1d4ed8; border: 1px dashed #93c5fd; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
                    ➕ Tambah Produk Retur Lain (Beda Ukuran/Varian)
                </button>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 13px; color: #334155;">Foto Bukti Kendala (Opsional)</label>
                <input type="file" name="proof_image" accept="image/*" style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeReturnModal()" class="btn" style="padding: 10px 18px; font-weight: bold; background: #e2e8f0; color: #475569; border: none; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" class="btn" style="padding: 10px 18px; font-weight: bold; background: #dc2626; color: white; border: none; border-radius: 8px; cursor: pointer;">
                    🚀 Buat & Kirim Retur
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let returItemIndex = 0;

function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function getAvailableItemsData() {
    const orderSelect = document.getElementById('retur_supplier_order_id');
    const selectedOrderOpt = (orderSelect && orderSelect.value) ? orderSelect.options[orderSelect.selectedIndex] : null;
    let itemsData = [];
    if (selectedOrderOpt && selectedOrderOpt.getAttribute('data-items')) {
        try {
            itemsData = JSON.parse(selectedOrderOpt.getAttribute('data-items'));
        } catch(e){}
    }

    if (itemsData.length === 0) {
        const supplierSelect = document.getElementById('retur_supplier_id');
        const selectedSupOpt = (supplierSelect && supplierSelect.value) ? supplierSelect.options[supplierSelect.selectedIndex] : null;
        if (selectedSupOpt && selectedSupOpt.getAttribute('data-items')) {
            try {
                itemsData = JSON.parse(selectedSupOpt.getAttribute('data-items'));
            } catch(e){}
        }
    }
    return itemsData;
}

function renderReturItemRow(index, defaultItem = null) {
    const itemsData = getAvailableItemsData();

    let initialName = defaultItem ? (defaultItem.name || '') : '';
    let initialSize = defaultItem ? (defaultItem.size || '') : '';
    let initialMaxQty = defaultItem ? (parseInt(String(defaultItem.quantity).replace(/[^0-9]/g, '')) || 1) : null;

    let selectOptionsHtml = '<option value="">-- Pilih Produk yang Ingin Diretur --</option>';
    if (itemsData.length > 0) {
        itemsData.forEach((it) => {
            const isSelected = (defaultItem && defaultItem.name === it.name && defaultItem.size === it.size) ? 'selected' : '';
            const label = it.name + (it.size ? ' (' + it.size + ')' : '') + (it.quantity ? ' - [Pesan: ' + it.quantity + ']' : '');
            const val = it.name + '|' + (it.size || '') + '|' + (it.quantity || '1');
            selectOptionsHtml += `<option value="${val}" ${isSelected}>${label}</option>`;
        });
    }

    const isFirst = (index === 0 && (!itemsData || itemsData.length <= 1));
    const card = document.createElement('div');
    card.className = 'retur-item-card';
    card.setAttribute('data-index', index);
    card.style.cssText = 'border: 1px solid #fca5a5; border-radius: 10px; padding: 14px; background: #fff5f5; border-left: 4px solid #ef4444; margin-bottom: 12px;';

    const qtyMaxAttr = initialMaxQty ? `max="${initialMaxQty}"` : '';

    card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px dashed #fca5a5; padding-bottom: 6px;">
            <strong style="color: #991b1b; font-size: 13px;">📦 Item Barang Rusak #${index + 1}</strong>
            ${!isFirst ? `<button type="button" onclick="this.closest('.retur-item-card').remove()" style="background:none; border:none; color:#dc2626; font-size:12px; font-weight:bold; cursor:pointer;">✕ Hapus Item Ini</button>` : ''}
        </div>

        ${itemsData.length > 0 ? `
        <div style="margin-bottom: 10px;" class="invoice-item-select-group">
            <label style="display: block; font-weight: bold; margin-bottom: 4px; font-size: 12.5px; color: #1e293b;">Pilih Produk dari Daftar *</label>
            <select class="form-control invoice-item-select" onchange="onReturProductSelected(this, ${index})" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; background: #ffffff;">
                ${selectOptionsHtml}
            </select>
        </div>
        ` : ''}

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 4px; font-size: 12.5px; color: #334155;">Nama Produk *</label>
                <input type="text" name="items[${index}][item_name]" id="retur_item_name_${index}" value="${escapeHtml(initialName)}" required placeholder="Nama produk" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; background: #ffffff;">
            </div>
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 4px; font-size: 12.5px; color: #334155;">Ukuran / Varian</label>
                <input type="text" name="items[${index}][weight]" id="retur_weight_${index}" value="${escapeHtml(initialSize)}" placeholder="Ukuran/Varian" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; background: #ffffff;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 4px; font-size: 12.5px; color: #334155;">Jumlah Retur (pcs) *</label>
                <input type="number" name="items[${index}][quantity]" id="retur_qty_${index}" min="1" ${qtyMaxAttr} value="1" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; background: #ffffff;">
            </div>
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 4px; font-size: 12.5px; color: #334155;">Alasan Retur / Kendala *</label>
                <input type="text" name="items[${index}][reason]" required placeholder="Alasan (kemasan bocor/melepem/rusak)" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; background: #ffffff;">
            </div>
        </div>
    `;

    return card;
}

function onReturProductSelected(selectElem, index) {
    const val = selectElem.value;
    if (!val) return;
    const parts = val.split('|');
    const name = parts[0] || '';
    const size = parts[1] || '';
    const qty = parts[2] || '';

    const nameInput = document.getElementById('retur_item_name_' + index);
    const weightInput = document.getElementById('retur_weight_' + index);
    const qtyInput = document.getElementById('retur_qty_' + index);

    if (nameInput) nameInput.value = name;
    if (weightInput) weightInput.value = size;
    if (qtyInput && qty) {
        const qtyNum = parseInt(qty.replace(/[^0-9]/g, '')) || 1;
        qtyInput.max = qtyNum;
        qtyInput.value = Math.min(parseInt(qtyInput.value) || 1, qtyNum);
    }
}

function refreshReturItemsWrapper() {
    const container = document.getElementById('retur-items-wrapper');
    if (!container) return;
    container.innerHTML = '';

    const itemsData = getAvailableItemsData();

    if (itemsData.length > 0) {
        itemsData.forEach((it, idx) => {
            returItemIndex = idx;
            const card = renderReturItemRow(idx, it);
            container.appendChild(card);
        });
    } else {
        returItemIndex = 0;
        container.appendChild(renderReturItemRow(0, null));
    }
}

function addReturItemRow() {
    const container = document.getElementById('retur-items-wrapper');
    if (!container) return;
    returItemIndex++;
    container.appendChild(renderReturItemRow(returItemIndex, null));
}

document.addEventListener('DOMContentLoaded', function() {
    const supplierSelect = document.getElementById('retur_supplier_id');
    const orderSelect = document.getElementById('retur_supplier_order_id');

    function filterInvoicesBySupplier() {
        if (!supplierSelect || !orderSelect) return;
        const selectedSupplierId = supplierSelect.value;

        for (let i = 0; i < orderSelect.options.length; i++) {
            const opt = orderSelect.options[i];
            if (!opt.value) {
                opt.style.display = '';
                opt.disabled = false;
                continue;
            }

            const optSupplierId = opt.getAttribute('data-supplier-id');
            if (!selectedSupplierId || optSupplierId == selectedSupplierId) {
                opt.style.display = '';
                opt.disabled = false;
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
            }
        }

        if (orderSelect.selectedOptions[0] && orderSelect.selectedOptions[0].disabled) {
            orderSelect.value = '';
        }
        refreshReturItemsWrapper();
    }

    if (supplierSelect) {
        supplierSelect.addEventListener('change', filterInvoicesBySupplier);
    }

    if (orderSelect) {
        orderSelect.addEventListener('change', refreshReturItemsWrapper);
    }

    window.filterInvoicesBySupplier = filterInvoicesBySupplier;
    window.refreshReturItemsWrapper = refreshReturItemsWrapper;
});

function openReturnModal() {
    document.getElementById('returnModal').style.display = 'flex';
    if (window.filterInvoicesBySupplier) window.filterInvoicesBySupplier();
    if (window.refreshReturItemsWrapper) window.refreshReturItemsWrapper();
}
function openReturnModalWithSupplier(supplierId) {
    document.getElementById('retur_supplier_id').value = supplierId;
    if (window.filterInvoicesBySupplier) window.filterInvoicesBySupplier();
    if (window.refreshReturItemsWrapper) window.refreshReturItemsWrapper();
    document.getElementById('returnModal').style.display = 'flex';
}
function openReturnModalWithOrder(orderId, supplierId, invoiceNumber) {
    document.getElementById('retur_supplier_id').value = supplierId;
    if (window.filterInvoicesBySupplier) window.filterInvoicesBySupplier();
    const orderSelect = document.getElementById('retur_supplier_order_id');
    if (orderSelect) {
        orderSelect.value = orderId;
    }
    if (window.refreshReturItemsWrapper) window.refreshReturItemsWrapper();
    document.getElementById('returnModal').style.display = 'flex';
}
function closeReturnModal() {
    document.getElementById('returnModal').style.display = 'none';
}
</script>
@endsection