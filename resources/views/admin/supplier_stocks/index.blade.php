@extends('admin.layout')

@section('content')
    <style>
        .stock-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 2px solid #cbd5e1;
        }

        .stock-table th,
        .stock-table td {
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            text-align: left;
            font-size: 14px;
        }

        .stock-table th {
            background: #f1f5f9;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 2px solid #94a3b8;
            font-size: 13px;
        }

        .stock-table tr:hover {
            background-color: #f1f5f9 !important;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-almost-expire {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            border-left: 3px solid #f59e0b;
            margin-top: 4px;
        }

        .alert-expired {
            display: inline-block;
            background: #fee2e2;
            color: #991b1b;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            border-left: 3px solid #dc2626;
            margin-top: 4px;
        }

        .tab-btn {
            padding: 12px 22px;
            font-weight: 600;
            font-size: 14px;
            color: #64748b;
            border: none;
            background: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .tab-btn:hover {
            color: #166534;
        }

        .tab-btn.active {
            color: #166534;
            font-weight: 700;
            border-bottom-color: #16a34a;
        }

        .empty-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            color: #64748b;
            margin-top: 15px;
        }
    </style>

    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 10px; flex-wrap: wrap;">
            <h1 class="admin-title" style="margin: 0;">📦 Pengelolaan Stok Barang (Supplier)</h1>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="/admin/supplier-stocks/expired-list" class="btn btn-outline"
                    style="border: 1px solid #dc2626; color: #dc2626; font-weight: bold; background: #fff5f5; text-decoration: none; padding: 10px 16px; border-radius: 8px;">📋
                    Daftar Barang Kadaluarsa</a>
                <a href="/admin/supplier-stocks/expired" class="btn btn-danger"
                    style="background: #dc2626; color: white; font-weight: bold; text-decoration: none; padding: 10px 16px; border-radius: 8px;">⚠️
                    Input Barang Kadaluarsa</a>
                <a href="/admin/supplier-stocks/create" class="btn btn-success"
                    style="background: #16a34a; color: white; font-weight: bold; text-decoration: none; padding: 10px 16px; border-radius: 8px;">+
                    Input Produk Baru</a>
            </div>
        </div>

        @if(session('success'))
            <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- ====== TAB NAVIGATION ====== --}}
        <div style="display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; overflow-x: auto;">
            <button type="button" class="tab-btn {{ request('tab') !== 'queue' && request('tab') !== 'logs' ? 'active' : '' }}" onclick="switchStockTab('active-tab', this)">
                🟢 Batch Utama / Aktif <span style="background: #dcfce7; color: #15803d; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">{{ count($activeStocks) }}</span>
            </button>
            <button type="button" class="tab-btn {{ request('tab') === 'queue' ? 'active' : '' }}" onclick="switchStockTab('queue-tab', this)">
                ⏳ Antrean Batch Selanjutnya <span style="background: #fef3c7; color: #b45309; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">{{ count($queuedStocks) }}</span>
            </button>
            <button type="button" class="tab-btn {{ request('tab') === 'logs' ? 'active' : '' }}" onclick="switchStockTab('logs-tab', this)">
                🕒 Riwayat Mutasi Stok <span style="background: #e2e8f0; color: #475569; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">{{ count($stockLogs) }}</span>
            </button>
        </div>

        {{-- ====== TAB 1: BATCH UTAMA / AKTIF ====== --}}
        <div id="active-tab" class="tab-content" style="display: {{ request('tab') !== 'queue' && request('tab') !== 'logs' ? 'block' : 'none' }};">
            @if(count($activeStocks) > 0)
                <div style="overflow-x: auto;">
                    <table class="stock-table">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 40px;">No</th>
                                <th style="text-align: center;">Gambar</th>
                                <th>Nama Barang</th>
                                <th>Nama Supplier</th>
                                <th style="background: #e2e8f0; border-left: 2px solid #94a3b8;">Ukuran</th>
                                <th style="background: #e2e8f0;">Harga Jual</th>
                                <th style="background: #e2e8f0;">Sisa Tersedia</th>
                                <th style="background: #e2e8f0;">Tgl Masuk</th>
                                <th style="background: #e2e8f0; border-right: 2px solid #94a3b8;">Tgl Expire</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeStocks as $index => $stock)
                                @php
                                    $variants = is_array($stock->variants) && count($stock->variants) > 0 ? $stock->variants : [[]];
                                    $variantCount = count($variants);

                                    $hasExpired = false;
                                    $hasAlmostExpired = false;
                                    $hasOutStock = false;
                                    $outStockWeights = [];

                                    foreach ($variants as $v) {
                                        $aq = (int) ($v['available_quantity'] ?? 0);
                                        if ($aq <= 10) {
                                            $hasOutStock = true;
                                            if (!empty($v['weight'])) {
                                                $outStockWeights[] = $v['weight'];
                                            }
                                        }
                                        if (!empty($v['expiry_date']) && $aq > 0) {
                                            $days = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($v['expiry_date']), false);
                                            if ($days < 0) {
                                                $hasExpired = true;
                                            } elseif ($days <= 5) {
                                                $hasAlmostExpired = true;
                                            }
                                        }
                                    }

                                    $bgStyle = ($index % 2 === 0) ? 'background-color: #ffffff;' : 'background-color: #f8fafc;';
                                    if ($hasExpired) {
                                        $bgStyle = 'background-color: #fef2f2;';
                                    } elseif ($hasAlmostExpired) {
                                        $bgStyle = 'background-color: #fffbeb;';
                                    }

                                    $targetUrl = $stock->supplier
                                        ? "/admin/suppliers/{$stock->supplier->slug}?item=" . urlencode($stock->item_name) . "&variant=" . urlencode(implode(', ', $outStockWeights))
                                        : "/admin/suppliers?item=" . urlencode($stock->item_name);
                                @endphp

                                @foreach($variants as $vIndex => $v)
                                    @php
                                        $aq = (int) ($v['available_quantity'] ?? 0);
                                        if ($aq <= 0) {
                                            $variantEntryDate = '-';
                                            $expDateText = '-';
                                            $expStyle = '';
                                        } else {
                                            $variantEntryDate = !empty($v['entry_date']) 
                                                ? \Carbon\Carbon::parse($v['entry_date'])->format('d M Y') 
                                                : ($stock->entry_date ? $stock->entry_date->format('d M Y') : $stock->created_at->format('d M Y'));
                                            
                                            $expDateText = '-';
                                            $expStyle = '';
                                            if (!empty($v['expiry_date'])) {
                                                $expDate = \Carbon\Carbon::parse($v['expiry_date']);
                                                $expDateText = $expDate->format('d M Y');
                                                $days = now()->startOfDay()->diffInDays($expDate, false);
                                                if ($days < 0) {
                                                    $expStyle = 'color: #dc2626; font-weight: bold;';
                                                } elseif ($days <= 5) {
                                                    $expStyle = 'color: #f59e0b; font-weight: bold;';
                                                }
                                            }
                                        }
                                        $isLastVariant = ($vIndex === $variantCount - 1);
                                        $rowBottomBorder = $isLastVariant ? 'border-bottom: 3px solid #64748b;' : 'border-bottom: 1px solid #cbd5e1;';
                                    @endphp
                                    <tr style="{{ $bgStyle }} {{ $rowBottomBorder }}">
                                        @if($vIndex === 0)
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; text-align: center; font-weight: bold; color: #475569; border-right: 1px solid #cbd5e1;">
                                                {{ $index + 1 }}
                                            </td>
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; text-align: center; border-right: 1px solid #cbd5e1;">
                                                @if($stock->image_path)
                                                    <img src="{{ asset($stock->image_path) }}" alt="{{ $stock->item_name }}"
                                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                                                @else
                                                    <span style="font-size: 1.8rem;">📦</span>
                                                @endif
                                            </td>
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; border-right: 1px solid #cbd5e1;">
                                                <span style="font-family: monospace; background: #e0f2fe; color: #0369a1; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 800; border: 1px solid #7dd3fc; display: inline-block; margin-bottom: 3px;">
                                                    STK-{{ str_pad($stock->id, 4, '0', STR_PAD_LEFT) }}
                                                </span><br>
                                                <strong style="font-size: 0.95rem; color: #0f172a;">{{ $stock->item_name }}</strong>
                                            </td>
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; font-weight: 500; color: #334155; border-right: 2px solid #94a3b8;">
                                                {{ $stock->supplier->name ?? 'Unknown' }}
                                            </td>
                                        @endif

                                        <td style="vertical-align: middle; border-right: 1px solid #cbd5e1; background: rgba(241, 245, 249, 0.4);">
                                            <span class="badge badge-success" style="background:#e8f5e9; color:#2e7d32; padding:4px 10px; border-radius:8px; font-weight:600; font-size:12px; display:inline-block; border: 1px solid #a7f3d0;">
                                                {{ $v['weight'] ?? '-' }}
                                            </span>
                                            @if(!empty($v['expiry_date']) && $aq > 0)
                                                @php
                                                    $vDays = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($v['expiry_date']), false);
                                                @endphp
                                                @if($vDays < 0)
                                                    <div style="margin-top: 4px;"><span class="alert-expired" style="font-size: 10px; padding: 2px 6px;">🚨 EXPIRE</span></div>
                                                @elseif($vDays <= 5)
                                                    <div style="margin-top: 4px;"><span class="alert-almost-expire" style="font-size: 10px; padding: 2px 6px;">⚠️ HAMPIR EXPIRE</span></div>
                                                @endif
                                            @endif
                                        </td>
                                        <td style="vertical-align: middle; font-weight: bold; color: #166534; font-size: 0.95rem; white-space: nowrap; border-right: 1px solid #cbd5e1; background: rgba(241, 245, 249, 0.4);">
                                            Rp {{ number_format($v['price'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td style="vertical-align: middle; white-space: nowrap; border-right: 1px solid #cbd5e1; background: rgba(241, 245, 249, 0.4);">
                                            @if($aq > 10)
                                                <span style="color: #166534; font-weight: bold; background: #dcfce7; padding: 4px 10px; border-radius: 6px; font-size: 13px; border: 1px solid #86efac; display:inline-block;">{{ $aq }} pcs</span>
                                            @elseif($aq > 0)
                                                <span style="color: #854d0e; font-weight: bold; background: #fef9c3; padding: 4px 10px; border-radius: 6px; font-size: 13px; border: 1px solid #fde047; display:inline-block;">{{ $aq }} pcs</span>
                                            @else
                                                <span style="color: #991b1b; font-weight: bold; background: #fee2e2; padding: 4px 10px; border-radius: 6px; font-size: 13px; border: 1px solid #fca5a5; display:inline-block;">Habis</span>
                                            @endif
                                        </td>
                                        <td style="vertical-align: middle; white-space: nowrap; border-right: 1px solid #cbd5e1; background: rgba(241, 245, 249, 0.4); color: #334155;">
                                            {{ $variantEntryDate }}
                                        </td>
                                        <td style="vertical-align: middle; white-space: nowrap; border-right: 2px solid #94a3b8; background: rgba(241, 245, 249, 0.4); {!! $expStyle !!}">
                                            {{ $expDateText }}
                                        </td>

                                        @if($vIndex === 0)
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; text-align: center; border-right: 1px solid #cbd5e1;">
                                                <span class="badge badge-success" style="background:#dcfce7; color:#166534; padding:5px 12px; border-radius:20px; font-weight:600; font-size:12px; border: 1px solid #86efac; display:inline-block;">
                                                    🟢 Aktif (Launching)
                                                </span>
                                            </td>
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle;">
                                                <div style="display:flex; flex-direction:column; gap:6px; min-width: 130px;">
                                                    @if($hasOutStock)
                                                        <a href="{{ $targetUrl }}" class="btn btn-success"
                                                            style="padding: 7px 12px; font-size: 12px; background: #16a34a; color: white; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 4px; font-weight: bold; border-radius: 6px; text-align: center; box-shadow: 0 1px 2px rgba(0,0,0,0.1);"
                                                            title="Ajukan Pesan Ulang ke Supplier">
                                                            📲 Ajukan Pesan Ulang
                                                        </a>
                                                    @endif

                                                    <div style="display:flex; gap:6px;">
                                                        <a href="/admin/supplier-stocks/{{ $stock->id }}/edit" class="btn btn-warning"
                                                            style="padding: 6px 10px; font-size: 12px; flex:1; text-align:center; font-weight:600;">Edit</a>
                                                        <form action="/admin/supplier-stocks/{{ $stock->id }}" method="POST" style="display:inline; flex:1;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px; width:100%; font-weight:600;"
                                                                onclick="return confirm('Yakin ingin menghapus stok barang ini?');">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-box">
                    <div style="font-size: 2.5rem; margin-bottom: 8px;">📦</div>
                    <h3 style="margin: 0 0 6px; color: #1e293b;">Belum Ada Barang Aktif</h3>
                    <p style="margin: 0 0 16px;">Seluruh batch barang aktif telah habis atau belum ada produk yang di-input.</p>
                    <a href="/admin/supplier-stocks/create" class="btn btn-success" style="padding: 9px 20px; font-weight: bold; background: #16a34a; color: white; text-decoration: none; border-radius: 6px;">+ Input Produk Baru</a>
                </div>
            @endif
        </div>

        {{-- ====== TAB 2: ANTREAN BATCH SELANJUTNYA ====== --}}
        <div id="queue-tab" class="tab-content" style="display: {{ request('tab') === 'queue' ? 'block' : 'none' }};">
            
            {{-- Header Action for Queue Tab --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; background: #f0f9ff; padding: 14px 20px; border-radius: 10px; border: 1px solid #bae6fd;">
                <div>
                    <strong style="color: #0369a1; font-size: 15px;">📦 Antrean Batch Produk Baru</strong>
                    <div style="font-size: 12.5px; color: #0284c7; margin-top: 2px;">
                        Batch baru yang di-input di sini akan mewarisi Nama Supplier, Nama Produk, Deskripsi, dan Gambar dari Batch Utama.
                    </div>
                </div>
                <button type="button" onclick="toggleBatchQueueForm()" class="btn btn-info" style="padding: 9px 18px; font-weight: bold; background: #0284c7; color: white; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    📦 + Input Batch Baru di Antrean
                </button>
            </div>

            {{-- Form Input Batch Antrean Baru --}}
            <div id="add-batch-queue-form" style="display: none; background: #ffffff; border: 2px solid #0284c7; padding: 22px; border-radius: 12px; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0f2fe; padding-bottom: 12px; margin-bottom: 16px;">
                    <h3 style="margin: 0; color: #0369a1; font-size: 16px; font-weight: 800;">
                        📝 Form Input Batch Antrean Baru
                    </h3>
                    <button type="button" onclick="toggleBatchQueueForm()" style="border: none; background: #e0f2fe; color: #0369a1; font-weight: bold; padding: 4px 10px; border-radius: 6px; cursor: pointer;">✕ Tutup Form</button>
                </div>

                <form action="/admin/supplier-stocks/store-batch-queue" method="POST">
                    @csrf
                    
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="font-size: 13px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 6px;">Pilih Pesanan Supplier (Yang Telah Diterima) *</label>
                        
                        @php $hasCompletedItems = false; @endphp
                        <select name="parent_stock_id" id="parent_stock_select" class="form-control" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #bae6fd; font-size: 14px;" onchange="onParentProductChange(this)">
                            <option value="">-- Pilih Barang dari Pesanan Supplier yang Telah Diterima --</option>
                            @foreach($completedOrders as $co)
                                @php
                                    $rawItems = $co->items ?? [];
                                    $expandedItems = [];
                                    $returns = \App\Models\SupplierReturn::where('supplier_order_id', $co->id)->get();

                                    foreach ($rawItems as $itIndex => $it) {
                                        if (!empty($it['processed'])) continue;

                                        $itemName = $it['name'] ?? '';
                                        $itemSize = $it['size'] ?? ($it['variant'] ?? '');
                                        $qtyStr = $it['quantity'] ?? '1';

                                        $sizes = array_values(array_filter(array_map('trim', explode(',', $itemSize))));
                                        $quantities = array_values(array_filter(array_map('trim', explode(',', $qtyStr))));

                                        if (count($sizes) > 1 || count($quantities) > 1) {
                                            $maxCount = max(count($sizes), count($quantities));
                                            for ($i = 0; $i < $maxCount; $i++) {
                                                $curSize = $sizes[$i] ?? ($sizes[0] ?? '250 gram');
                                                $rawQty = (int) preg_replace('/[^0-9]/', '', $quantities[$i] ?? ($quantities[0] ?? '1'));
                                                if ($rawQty <= 0) $rawQty = 1;

                                                $returQty = 0;
                                                foreach ($returns as $ret) {
                                                    if (strtolower(trim($ret->item_name)) === strtolower(trim($itemName))) {
                                                        if (!$curSize || !$ret->weight || str_contains(strtolower($ret->weight), strtolower($curSize)) || str_contains(strtolower($curSize), strtolower($ret->weight))) {
                                                            $returQty += (int) $ret->quantity;
                                                        }
                                                    }
                                                }

                                                $netQty = max(0, $rawQty - $returQty);

                                                $expandedItems[] = [
                                                    'orig_index' => $itIndex,
                                                    'name' => $itemName,
                                                    'size' => $curSize,
                                                    'quantity' => $netQty,
                                                ];
                                            }
                                        } else {
                                            $curSize = $itemSize ?: '250 gram';
                                            $rawQty = (int) preg_replace('/[^0-9]/', '', $qtyStr ?: '1');
                                            if ($rawQty <= 0) $rawQty = 1;

                                            $returQty = 0;
                                            foreach ($returns as $ret) {
                                                if (strtolower(trim($ret->item_name)) === strtolower(trim($itemName))) {
                                                    if (!$curSize || !$ret->weight || str_contains(strtolower($ret->weight), strtolower($curSize)) || str_contains(strtolower($curSize), strtolower($ret->weight))) {
                                                        $returQty += (int) $ret->quantity;
                                                    }
                                                }
                                            }

                                            $netQty = max(0, $rawQty - $returQty);

                                            $expandedItems[] = [
                                                'orig_index' => $itIndex,
                                                'name' => $itemName,
                                                'size' => $curSize,
                                                'quantity' => $netQty,
                                            ];
                                        }
                                    }
                                @endphp

                                @foreach($expandedItems as $exp)
                                    @php
                                        $itemName = $exp['name'];
                                        $itemSize = $exp['size'];
                                        $itemQty = max(0, (int) $exp['quantity']);
                                        $matchStock = $activeStocks->first(fn($as) => strtolower(trim($as->item_name)) === strtolower(trim($itemName)));
                                        if (!$matchStock) {
                                            $matchStock = $queuedStocks->first(fn($qs) => strtolower(trim($qs->item_name)) === strtolower(trim($itemName)));
                                        }
                                        $stockId = $matchStock ? $matchStock->id : ($activeStocks->first()->id ?? 0);
                                    @endphp
                                    @if(!empty($itemName) && $stockId > 0)
                                        @php $hasCompletedItems = true; @endphp
                                        <option value="{{ $stockId }}" data-weight="{{ $itemSize }}" data-qty="{{ $itemQty }}" data-order-id="{{ $co->id }}" data-item-index="{{ $exp['orig_index'] }}">
                                            ✅ Invoice #{{ $co->invoice_number }} — {{ $itemName }} (Ukuran: {{ $itemSize }}, {{ $itemQty }} pcs) — {{ $co->supplier->name ?? 'Supplier' }}
                                        </option>
                                    @endif
                                @endforeach
                            @endforeach
                        </select>
                        <input type="hidden" name="supplier_order_id" id="batch_order_id_input" value="">
                        <input type="hidden" name="order_item_index" id="batch_item_index_input" value="">

                        @if(!$hasCompletedItems)
                            <div style="background: #fffbeb; color: #b45309; padding: 12px 16px; border-radius: 8px; border: 1px solid #fde047; font-size: 13px; font-weight: bold; margin-top: 10px;">
                                ⚠️ Belum ada pesanan supplier yang berstatus "Telah Diterima". 
                                <br>Silakan lakukan pemesanan ke supplier dan tekan tombol <span style="background: #16a34a; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px;">✅ Telah Diterima</span> di menu <a href="/admin/suppliers" style="color: #0284c7; text-decoration: underline;">Manajemen Supplier</a> setelah barang fisik telah tiba.
                            </div>
                        @else
                            <small style="color: #0284c7; font-size: 12px; display: block; margin-top: 6px; font-weight: 600;">
                                ℹ️ Pilihan ini secara otomatis menyaring pesanan supplier yang berstatus <strong>"Telah Diterima"</strong>. Nama Supplier, Barang, dan Gambar otomatis mengikuti produk utama.
                            </small>
                        @endif
                    </div>

                    <div style="background: #f0f9ff; padding: 16px; border-radius: 10px; border: 1px solid #bae6fd; margin-bottom: 16px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px;">
                            <div>
                                <label style="font-size: 12px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 4px;">Ukuran *</label>
                                <input type="text" name="weight" id="batch_weight_input" class="form-control" placeholder="Contoh: 250 gram" required style="width: 100%; padding: 9px 12px; border-radius: 6px; border: 1px solid #bae6fd; font-size: 13.5px;">
                            </div>
                            <div>
                                <label style="font-size: 12px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 4px;">Jumlah Stok *</label>
                                <input type="number" name="initial_quantity" id="batch_qty_input" class="form-control" placeholder="Jumlah Pcs" required min="1" style="width: 100%; padding: 9px 12px; border-radius: 6px; border: 1px solid #bae6fd; font-size: 13.5px;">
                            </div>
                            <div>
                                <label style="font-size: 12px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 4px;">Harga Beli (Modal) *</label>
                                <input type="number" name="buy_price" class="form-control buy-price-q-input" placeholder="Harga Beli" required min="0" oninput="calculateSellingPriceQ(this)" style="width: 100%; padding: 9px 12px; border-radius: 6px; border: 1px solid #bae6fd; font-size: 13.5px;">
                            </div>
                            <div>
                                <label style="font-size: 12px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 4px;">Harga Jual (+30% Otomatis) *</label>
                                <input type="number" name="price" class="form-control sell-price-q-input" placeholder="Harga Jual" required min="0" style="width: 100%; padding: 9px 12px; border-radius: 6px; border: 1px solid #bae6fd; font-size: 13.5px;">
                            </div>
                            <div>
                                <label style="font-size: 12px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 4px;">Tgl Masuk Batch *</label>
                                <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 9px 12px; border-radius: 6px; border: 1px solid #bae6fd; font-size: 13.5px;">
                            </div>
                            <div>
                                <label style="font-size: 12px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 4px;">Tgl Expire (Opsional)</label>
                                <input type="date" name="expiry_date" class="form-control" style="width: 100%; padding: 9px 12px; border-radius: 6px; border: 1px solid #bae6fd; font-size: 13.5px;">
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" onclick="toggleBatchQueueForm()" class="btn btn-outline" style="padding: 9px 18px; font-weight: 600; border: 1px solid #cbd5e1; border-radius: 8px;">Batal</button>
                        <button type="submit" class="btn btn-info" style="padding: 9px 24px; background: #0284c7; color: white; border: none; font-weight: bold; border-radius: 8px; cursor: pointer;">
                            📦 Simpan ke Antrean Batch
                        </button>
                    </div>
                </form>
            </div>

            @if(count($queuedStocks) > 0)
                <div style="overflow-x: auto;">
                    <table class="stock-table">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 40px;">No</th>
                                <th style="text-align: center;">Gambar</th>
                                <th>Nama Barang</th>
                                <th>Nama Supplier</th>
                                <th style="background: #e2e8f0; border-left: 2px solid #94a3b8;">Ukuran</th>
                                <th style="background: #e2e8f0;">Harga Jual</th>
                                <th style="background: #e2e8f0;">Sisa Tersedia</th>
                                <th style="background: #e2e8f0;">Tgl Masuk Batch</th>
                                <th style="background: #e2e8f0; border-right: 2px solid #94a3b8;">Tgl Expire</th>
                                <th style="text-align: center;">Status Antrean</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($queuedStocks as $index => $stock)
                                @php
                                    $variants = is_array($stock->variants) && count($stock->variants) > 0 ? $stock->variants : [[]];
                                    $variantCount = count($variants);
                                @endphp

                                @foreach($variants as $vIndex => $v)
                                    @php
                                        $aq = (int) ($v['available_quantity'] ?? 0);
                                        if ($aq <= 0) {
                                            $variantEntryDate = '-';
                                            $expDateText = '-';
                                        } else {
                                            $variantEntryDate = !empty($v['entry_date']) 
                                                ? \Carbon\Carbon::parse($v['entry_date'])->format('d M Y') 
                                                : ($stock->entry_date ? $stock->entry_date->format('d M Y') : $stock->created_at->format('d M Y'));
                                            
                                            $expDateText = '-';
                                            if (!empty($v['expiry_date'])) {
                                                $expDateText = \Carbon\Carbon::parse($v['expiry_date'])->format('d M Y');
                                            }
                                        }
                                        $isLastVariant = ($vIndex === $variantCount - 1);
                                        $rowBottomBorder = $isLastVariant ? 'border-bottom: 3px solid #0284c7;' : 'border-bottom: 1px solid #bae6fd;';
                                    @endphp
                                    <tr style="background-color: #f0f9ff; {{ $rowBottomBorder }}">
                                        @if($vIndex === 0)
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; text-align: center; font-weight: bold; color: #0369a1; border-right: 1px solid #bae6fd;">
                                                @if($index === 0)
                                                    <span style="background: #0284c7; color: white; border-radius: 50%; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.3);" title="Prioritas Pertama Antrean (Pertama Didiinput)">1</span>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; text-align: center; border-right: 1px solid #bae6fd;">
                                                @if($stock->image_path)
                                                    <img src="{{ asset($stock->image_path) }}" alt="{{ $stock->item_name }}"
                                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #bae6fd;">
                                                @else
                                                    <span style="font-size: 1.8rem;">📦</span>
                                                @endif
                                            </td>
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; border-right: 1px solid #bae6fd;">
                                                <strong style="font-size: 0.95rem; color: #0369a1;">{{ $stock->item_name }}</strong>
                                                <br>
                                                @if($index === 0)
                                                    <span style="font-size: 11px; background: #e0f2fe; color: #0284c7; padding: 2px 6px; border-radius: 4px; font-weight: 700; border: 1px solid #bae6fd;">⭐ Prioritas No. 1</span>
                                                @else
                                                    <span style="font-size: 11px; color: #0284c7; font-weight: 600;">📦 Batch Antrean #{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; font-weight: 500; color: #334155; border-right: 2px solid #0284c7;">
                                                {{ $stock->supplier->name ?? 'Unknown' }}
                                            </td>
                                        @endif

                                        <td style="vertical-align: middle; border-right: 1px solid #bae6fd;">
                                            <span class="badge" style="background:#e0f2fe; color:#0369a1; padding:4px 10px; border-radius:8px; font-weight:600; font-size:12px; display:inline-block; border: 1px solid #7dd3fc;">
                                                {{ $v['weight'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle; font-weight: bold; color: #0369a1; font-size: 0.95rem; white-space: nowrap; border-right: 1px solid #bae6fd;">
                                            Rp {{ number_format($v['price'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td style="vertical-align: middle; white-space: nowrap; border-right: 1px solid #bae6fd;">
                                            <span style="color: #0369a1; font-weight: bold; background: #e0f2fe; padding: 4px 10px; border-radius: 6px; font-size: 13px; border: 1px solid #7dd3fc; display:inline-block;">{{ $aq }} pcs</span>
                                        </td>
                                        <td style="vertical-align: middle; white-space: nowrap; border-right: 1px solid #bae6fd; color: #334155;">
                                            {{ $variantEntryDate }}
                                        </td>
                                        <td style="vertical-align: middle; white-space: nowrap; border-right: 2px solid #0284c7;">
                                            {{ $expDateText }}
                                        </td>

                                        @if($vIndex === 0)
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle; text-align: center; border-right: 1px solid #bae6fd;">
                                                <span class="badge" style="background:#fef3c7; color:#92400e; padding:5px 12px; border-radius:20px; font-weight:600; font-size:12px; border: 1px solid #fde047; display:inline-block;" title="Varian beda harga menantikan stok lama habis">
                                                    ⏳ Menunggu Batch Habis
                                                </span>
                                            </td>
                                            <td rowspan="{{ $variantCount }}" style="vertical-align: middle;">
                                                <div style="display:flex; flex-direction:column; gap:6px; min-width: 140px;">
                                                    <form action="/admin/supplier-stocks/{{ $stock->id }}/launch" method="POST" style="margin:0;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary"
                                                            style="padding: 7px 10px; font-size: 12px; background: #2563eb; color: white; border: none; font-weight: bold; border-radius: 6px; width: 100%; cursor: pointer;"
                                                            onclick="return confirm('Apakah Anda yakin ingin meluncurkan batch stok ini ke toko secara manual sekarang?');">
                                                            🚀 Launching Sekarang
                                                        </button>
                                                    </form>

                                                    <div style="display:flex; gap:6px;">
                                                        <a href="/admin/supplier-stocks/{{ $stock->id }}/edit" class="btn btn-warning"
                                                            style="padding: 6px 10px; font-size: 12px; flex:1; text-align:center; font-weight:600;">Edit</a>
                                                        <form action="/admin/supplier-stocks/{{ $stock->id }}" method="POST" style="display:inline; flex:1;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px; width:100%; font-weight:600;"
                                                                onclick="return confirm('Yakin ingin menghapus stok antrean ini?');">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-box">
                    <div style="font-size: 2.5rem; margin-bottom: 8px;">⏳</div>
                    <h3 style="margin: 0 0 6px; color: #1e293b;">Tidak Ada Antrean Batch</h3>
                    <p style="margin: 0 0 16px;">Seluruh batch barang yang masuk saat ini sedang aktif atau telah langsung dirilis.</p>
                    <button type="button" onclick="toggleBatchQueueForm()" class="btn btn-info" style="padding: 9px 20px; font-weight: bold; background: #0284c7; color: white; border: none; border-radius: 6px; cursor: pointer;">
                        📦 + Input Batch Baru di Antrean
                    </button>
                </div>
            @endif
        </div>

        {{-- ====== TAB 3: RIWAYAT MUTASI STOK ====== --}}
        <div id="logs-tab" class="tab-content" style="display: {{ request('tab') === 'logs' ? 'block' : 'none' }};">
            @if(count($stockLogs) > 0)
                <div style="overflow-x: auto;">
                    <table class="stock-table">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 40px;">No</th>
                                <th>Tanggal & Waktu</th>
                                <th>Nama Barang & Ukuran</th>
                                <th style="text-align: center;">Tipe Mutasi</th>
                                <th>Keterangan / Alasan</th>
                                <th style="text-align: right; width: 140px;">Perubahan Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockLogs as $index => $log)
                                @php
                                    $typeBadge = 'badge-success';
                                    $typeLabel = 'STOK MASUK';
                                    if ($log->type === 'out') {
                                        $typeBadge = 'badge-danger';
                                        $typeLabel = 'STOK KELUAR';
                                    } elseif ($log->type === 'adjustment') {
                                        $typeBadge = 'badge-warning';
                                        $typeLabel = 'PENYESUAIAN';
                                    } elseif ($log->type === 'launch') {
                                        $typeBadge = 'badge-info';
                                        $typeLabel = 'LAUNCH BATCH';
                                    }
                                @endphp
                                <tr style="background: {{ $index % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $index + 1 }}</td>
                                    <td style="color: #334155; font-weight: 500; white-space: nowrap;">
                                        {{ $log->created_at ? $log->created_at->translatedFormat('d F Y \p\u\k\u\l H.i') : '-' }}
                                    </td>
                                    <td>
                                        <strong style="color: #0f172a;">{{ $log->supplierStock->item_name ?? 'Produk' }}</strong>
                                        <span style="font-size: 12px; color: #166534; font-weight: bold;">({{ $log->weight ?? '-' }})</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge {{ $typeBadge }}" style="font-size: 11px; letter-spacing: 0.5px;">
                                            {{ $typeLabel }}
                                        </span>
                                    </td>
                                    <td style="color: #475569; font-size: 13.5px;">
                                        {{ $log->description ?? '-' }}
                                    </td>
                                    <td style="text-align: right; font-weight: 800; font-size: 14px;">
                                        @if($log->type === 'out')
                                            <span style="color: #dc2626;">-{{ $log->quantity }} pcs</span>
                                        @else
                                            <span style="color: #16a34a;">+{{ $log->quantity }} pcs</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-box">
                    <div style="font-size: 2.5rem; margin-bottom: 8px;">🕒</div>
                    <h3 style="margin: 0 0 6px; color: #1e293b;">Belum Ada Mutasi Stok</h3>
                    <p style="margin: 0;">Riwayat perubahan dan perpindahan stok barang akan otomatis tercatat di sini.</p>
                </div>
            @endif
        </div>

    </div>

    <script>
    function switchStockTab(tabId, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active');
        });
        document.getElementById(tabId).style.display = 'block';
        btn.classList.add('active');
    }

    function toggleBatchQueueForm() {
        const form = document.getElementById('add-batch-queue-form');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            form.style.display = 'none';
        }
    }

    function calculateSellingPriceQ(input) {
        const form = input.closest('form');
        if (!form) return;
        const sellInput = form.querySelector('.sell-price-q-input');
        if (!sellInput) return;
        const buyVal = parseFloat(input.value) || 0;
        if (buyVal > 0) {
            sellInput.value = Math.round(buyVal * 1.30);
        }
    }

    function onParentProductChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption) return;
        const defaultWeight = selectedOption.getAttribute('data-weight');
        const defaultQty = selectedOption.getAttribute('data-qty');
        const orderId = selectedOption.getAttribute('data-order-id') || '';
        const itemIndex = selectedOption.getAttribute('data-item-index') || '';

        const weightInput = document.getElementById('batch_weight_input');
        const qtyInput = document.getElementById('batch_qty_input');
        const orderIdInput = document.getElementById('batch_order_id_input');
        const itemIndexInput = document.getElementById('batch_item_index_input');

        if (weightInput && defaultWeight) {
            weightInput.value = defaultWeight;
        }
        if (qtyInput && defaultQty) {
            qtyInput.value = defaultQty;
        }
        if (orderIdInput) {
            orderIdInput.value = orderId;
        }
        if (itemIndexInput) {
            itemIndexInput.value = itemIndex;
        }
    }
    </script>
@endsection