@extends('admin.layout')

@section('content')
@php
    $supplierItems = [];
    foreach ($supplier->items ?? [] as $item) {
        if (is_array($item)) {
            $supplierItems[] = $item['name'] ?? '';
        } elseif (is_string($item)) {
            $supplierItems[] = $item;
        }
    }
    $reqItem = request('item');
    $reqSize = request('size') ?? request('variant');
    $hasReqMatch = false;

    if (!empty($reqItem)) {
        foreach ($supplierItems as $itemName) {
            if (strtolower(trim($itemName)) === strtolower(trim($reqItem))) {
                $hasReqMatch = true;
                break;
            }
        }
        if (!$hasReqMatch) {
            $supplierItems[] = $reqItem;
        }
    }

    $displayCards = [];
    foreach ($supplierItems as $itemName) {
        if ($itemName === '') continue;

        $isAutoSelected = !empty($reqItem) && (strtolower(trim($itemName)) === strtolower(trim($reqItem)));

        // Jika halaman dibuka dari pengajuan pesan ulang (?item=...), sembunyikan produk lain yang masih ada stoknya
        if (!empty($reqItem) && !$isAutoSelected) {
            continue;
        }

        if ($isAutoSelected && !empty($reqSize) && str_contains($reqSize, ',')) {
            $sizes = array_values(array_filter(array_map('trim', explode(',', $reqSize))));
            foreach ($sizes as $sz) {
                $displayCards[] = [
                    'name' => $itemName,
                    'size' => $sz,
                    'is_auto' => true,
                ];
            }
        } else {
            $displayCards[] = [
                'name' => $itemName,
                'size' => $isAutoSelected ? $reqSize : '',
                'is_auto' => $isAutoSelected,
            ];
        }
    }
@endphp

<div class="admin-card">
    <div class="admin-header">
        <h1 class="admin-title">📦 Katalog Produk: {{ $supplier['name'] }}</h1>
        <a href="/admin/suppliers" class="btn btn-warning">← Kembali</a>
    </div>

    @if(!empty($reqItem))
        <div style="background:#fef3c7; color:#92400e; padding:12px 16px; border-radius:10px; margin-bottom:20px; border-left:4px solid #f59e0b; font-weight:bold;">
            ⚠️ Pengajuan Pesan Ulang Produk: "{{ $reqItem }}" {{ !empty($reqSize) ? "($reqSize)" : '' }}. Silakan tentukan jumlah pesanan untuk masing-masing ukuran lalu klik Kirim Pesanan.
        </div>
    @endif

    <p>Di halaman ini, Anda dapat mengisi detail pesanan (Nama Produk, Ukuran, Jumlah) dan mengirimkan pesan pesanan secara langsung ke WhatsApp supplier.</p>

    <div style="background:#f1f8e9; padding:20px; border-radius:16px; margin-bottom:24px; border:1px solid #c8e6c9;">
        <h2 style="margin-top:0; color:#2e7d32; font-size:1.2rem;">🏢 Informasi Supplier</h2>
        <p style="margin:4px 0;"><strong>Nama:</strong> {{ $supplier->name ?? '' }}</p>
        <p style="margin:4px 0;"><strong>Telepon:</strong> {{ $supplier->phone ?? '-' }}</p>
    </div>

    <form action="/admin/suppliers/{{ $supplier->slug }}/order" method="POST" class="supplier-form">
        @csrf

        <h2 style="margin-top:0; color:#1e293b; font-size:1.3rem; margin-bottom:6px;">📝 Pesan Produk ke Supplier</h2>
        <p style="color:#64748b; margin-bottom: 20px; font-size:0.95rem;">Lengkapi informasi nama produk, ukuran, dan jumlah yang ingin dipesan di bawah ini.</p>

        <div id="order-items-container" style="display:flex; flex-direction:column; gap:16px; margin-bottom:24px;">
            @foreach($displayCards as $index => $card)
                @php
                    $isAutoSelected = $card['is_auto'];
                @endphp
                <div class="order-item-card" style="border: {{ $isAutoSelected ? '2px solid #16a34a' : '1px solid #e2e8f0' }}; border-radius: 12px; padding: 18px; background: {{ $isAutoSelected ? '#f0fdf4' : '#ffffff' }}; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px dashed #e2e8f0;">
                        <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; color: #1e293b; font-size: 1rem;">
                            <input type="checkbox" name="items[{{ $index }}][selected]" value="1" class="item-checkbox" style="width: 18px; height: 18px; accent-color: #16a34a;" {{ $isAutoSelected ? 'checked' : '' }}>
                            <span>Pilih Produk Ini {{ $isAutoSelected ? ' (Stok Low/Habis)' : '' }}</span>
                        </label>
                        <span class="badge badge-success" style="background:#e8f5e9; color:#2e7d32; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">Katalog Supplier</span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#475569;">Nama Produk <span style="color:#e11d48;">*</span></label>
                            <input type="text" name="items[{{ $index }}][name]" value="{{ $card['name'] }}" class="form-control" placeholder="Masukkan nama produk" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#475569;">Ukuran</label>
                            <input type="text" name="items[{{ $index }}][size]" value="{{ $card['size'] }}" class="form-control" placeholder="250gr, 500gr, 1kg, Pouch" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#475569;">Jumlah Dipesan</label>
                            <input type="text" name="items[{{ $index }}][quantity]" class="form-control" placeholder="Contoh: 20 bungkus" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;" {{ $isAutoSelected ? 'autofocus' : '' }}>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 24px;">
            <button type="button" class="btn btn-warning" onclick="addCustomOrderItem()" style="display: flex; align-items: center; gap: 6px; padding: 9px 16px; font-size: 14px; font-weight:600;">
                ➕ Tambah Produk Lain
            </button>
        </div>

        <button type="submit" class="btn btn-success" style="padding: 12px 28px; font-size: 1rem; font-weight: bold; border-radius: 8px; background: #16a34a; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            📄 Buat Invoice & Kirim Pesanan ke WhatsApp Supplier
        </button>
    </form>

    {{-- ====== RIWAYAT INVOICE PESANAN SUPPLIER ====== --}}
    <div style="margin-top: 40px; padding-top: 24px; border-top: 2px solid #e2e8f0;">
        <h2 style="margin-top:0; color:#1e293b; font-size:1.3rem; margin-bottom:14px; display: flex; align-items: center; gap: 8px;">
            📑 Riwayat Invoice Pesanan Supplier
        </h2>
        
        @if(isset($supplier->orders) && count($supplier->orders) > 0)
            <div style="overflow-x: auto;">
                <table class="admin-table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                            <th style="padding: 12px 16px; font-size: 13px; font-weight: 700; color: #475569;">No. Invoice</th>
                            <th style="padding: 12px 16px; font-size: 13px; font-weight: 700; color: #475569;">Tanggal Pesanan</th>
                            <th style="padding: 12px 16px; font-size: 13px; font-weight: 700; color: #475569;">Detail Item</th>
                            <th style="padding: 12px 16px; font-size: 13px; font-weight: 700; color: #475569; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplier->orders as $order)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; font-weight: bold; color: #166534;">
                                    #{{ $order->invoice_number }}
                                </td>
                                <td style="padding: 12px 16px; color: #64748b; font-size: 13px;">
                                    {{ $order->created_at ? $order->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                                </td>
                                <td style="padding: 12px 16px; color: #334155; font-size: 13px;">
                                    @php
                                        $itemList = [];
                                        foreach($order->items ?? [] as $it) {
                                            $itemList[] = ($it['name'] ?? '') . ' (' . ($it['size'] ?? 'std') . ': ' . ($it['quantity'] ?? '1') . ')';
                                        }
                                    @endphp
                                    {{ implode(', ', $itemList) }}
                                </td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    <a href="/admin/supplier-orders/{{ $order->id }}/invoice" class="btn btn-info" style="padding: 6px 14px; font-size: 12px; font-weight: bold; background: #0284c7; color: white; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                        👁️ Lihat / Cetak Invoice
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; font-size: 13.5px; text-align: center;">
                Belum ada riwayat pesanan/invoice untuk supplier ini.
            </div>
        @endif
    </div>
</div>

<script>
let itemIndex = {{ count($displayCards) }};

function addCustomOrderItem() {
    const container = document.getElementById('order-items-container');
    const card = document.createElement('div');
    card.className = 'order-item-card';
    card.style.cssText = 'border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);';
    card.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px dashed #e2e8f0;">
            <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; cursor: pointer; color: #1e293b; font-size: 1rem;">
                <input type="checkbox" name="items[${itemIndex}][selected]" value="1" checked class="item-checkbox" style="width: 18px; height: 18px; accent-color: #16a34a;">
                <span>Produk Tambahan #${itemIndex + 1}</span>
            </label>
            <button type="button" onclick="this.closest('.order-item-card').remove()" class="btn btn-danger" style="padding: 5px 12px; font-size: 12px; border-radius: 6px;">Hapus Item</button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#475569;">Nama Produk <span style="color:#e11d48;">*</span></label>
                <input type="text" name="items[${itemIndex}][name]" class="form-control" placeholder="Masukkan nama produk" required style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#475569;">Ukuran</label>
                <input type="text" name="items[${itemIndex}][size]" class="form-control" placeholder="250gr, 500gr, 1kg, Pouch" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#475569;">Jumlah Dipesan</label>
                <input type="text" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Contoh: 20 bungkus" style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
            </div>
        </div>
    `;
    container.appendChild(card);
    itemIndex++;
}
</script>
@endsection

