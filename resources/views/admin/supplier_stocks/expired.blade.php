@extends('admin.layout')

@section('content')

<div class="admin-card">
    <h1 class="admin-title">
        ⚠️ Input Barang Kadaluarsa / Rusak
    </h1>

    <p style="color: #666; margin-bottom: 20px;">
        Gunakan form ini untuk mencatat barang dari supplier yang sudah kadaluarsa atau rusak. Stok produk akan dikurangi secara otomatis dan dicatat dalam laporan kerugian supplier.
    </p>

    @if(session('error'))
        <div style="padding: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #dc2626;">
            {{ session('error') }}
        </div>
    @endif

    <form action="/admin/supplier-stocks/expired" method="POST">
        @csrf

        <div class="form-group" style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <label style="font-weight: bold; color: #2e7d32; margin: 0;">Pilih Produk & Varian Ukuran *</label>
                <label style="font-size: 12px; color: #475569; font-weight: normal; cursor: pointer;">
                    <input type="checkbox" id="showAllCheckbox" style="accent-color: #2e7d32; cursor: pointer;"> Tampilkan Semua Produk
                </label>
            </div>
            <select name="variant_key" id="variant_key" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
                <option value="">-- Pilih Produk & Ukuran --</option>
                @php $expiredCount = 0; @endphp
                @foreach($stocks as $stock)
                    @php
                        $supplierName = $stock->supplier->name ?? 'Tanpa Supplier';
                    @endphp
                    @foreach($stock->variants ?? [] as $v)
                        @if((int)($v['available_quantity'] ?? 0) > 0)
                            @php
                                $isExpired = false;
                                if (!empty($v['expiry_date'])) {
                                    $days = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($v['expiry_date']), false);
                                    if ($days < 0) {
                                        $isExpired = true;
                                        $expiredCount++;
                                    }
                                }
                            @endphp
                            <option value="{{ $stock->id }}|{{ $v['weight'] }}" 
                                    data-available="{{ $v['available_quantity'] }}" 
                                    data-buy-price="{{ $v['buy_price'] }}"
                                    data-item-name="{{ $stock->item_name }}"
                                    data-weight="{{ $v['weight'] }}"
                                    data-expiry-date="{{ !empty($v['expiry_date']) ? \Carbon\Carbon::parse($v['expiry_date'])->format('Y-m-d') : date('Y-m-d') }}"
                                    data-is-expired="{{ $isExpired ? '1' : '0' }}"
                                    class="product-opt">
                                {{ $stock->item_name }} ({{ $v['weight'] }}) - [Supplier: {{ $supplierName }}] {{ $isExpired ? '🚨 (EXPIRED)' : '' }}
                            </option>
                        @endif
                    @endforeach
                @endforeach
            </select>
            <small id="expired-filter-notice" style="color: #64748b; margin-top: 6px; display: block; font-style: italic;">
                ℹ️ Saat ini dropdown hanya menampilkan varian yang <strong>sudah kadaluarsa</strong> ({{ $expiredCount }} varian). Centang "Tampilkan Semua Produk" jika ingin mencatat varian lain.
            </small>
        </div>

        <div id="stock-info-card" style="display: none; background: #f0fdf4; padding: 15px; border-radius: 8px; border: 1px solid #bbf7d0; margin-bottom: 20px; color: #166534; font-size: 14px;">
            <strong>Informasi Stok:</strong>
            <span style="display: block; margin-top: 5px;">Stok Tersedia: <strong id="info-available">0</strong> pcs</span>
            <span style="display: block;">Harga Beli (HPP): <strong>Rp <span id="info-hpp">0</span></strong></span>
        </div>

        <!-- Hidden input for created_at date -->
        <input type="hidden" name="created_at" id="created_at" value="{{ date('Y-m-d') }}">

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; color: #2e7d32; display: block; margin-bottom: 5px;">Tanggal Kadaluarsa Produk (Otomatis)</label>
            <div style="background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; color: #334155; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                <span>📅</span>
                <span id="display-date-text" style="font-weight: bold; color: #0f172a;">Pilih produk terlebih dahulu</span>
                <span style="font-size: 12px; color: #64748b; margin-left: auto;">(Otomatis dari Manajemen Produk)</span>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; color: #2e7d32; display: block; margin-bottom: 5px;">Jumlah Barang Kadaluarsa (Qty) *</label>
            <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Masukkan jumlah barang yang kadaluarsa/rusak" required min="1" disabled style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
        </div>

        <!-- Real-time Loss Preview Card -->
        <div id="loss-preview" style="display: none; background: #fff5f5; padding: 20px; border-radius: 10px; border: 1px solid #feb2b2; margin-bottom: 25px; color: #c53030;">
            <h3 style="margin: 0 0 10px 0; color: #9b2c2c; font-size: 15px; display: flex; align-items: center; gap: 6px;">
                <span>🛑</span> Estimasi Rincian Kerugian
            </h3>
            <ul style="margin: 0; padding-left: 20px; font-size: 13.5px; line-height: 1.6;">
                <li>Nama Barang: <strong id="preview-item-name">-</strong></li>
                <li>Ukuran: <strong id="preview-weight">-</strong></li>
                <li>Jumlah Kadaluarsa: <strong id="preview-qty">0</strong> pcs</li>
                <li>Harga Beli Satuan (HPP): <strong>Rp <span id="preview-hpp">0</span></strong></li>
                <li style="margin-top: 8px; font-size: 15px; border-top: 1px dashed #feb2b2; padding-top: 8px;">
                    <strong>Total Kerugian Toko & Supplier: <span style="font-size: 17px; font-weight: 800; color: #9b2c2c;">Rp <span id="preview-total-loss">0</span></span></strong>
                </li>
            </ul>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; color: #2e7d32; display: block; margin-bottom: 5px;">Catatan / Keterangan (Opsional)</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Rusak karena kemasan robek atau berjamur..." style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;"></textarea>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-danger" style="padding: 12px 24px; font-weight: bold; border-radius: 8px; border: none; cursor: pointer;">
                💾 Catat Kerugian Kadaluarsa
            </button>
            <a href="/admin/supplier-stocks" class="btn btn-outline" style="padding: 12px 24px; font-weight: bold; border-radius: 8px; border: 1px solid #d1d5db; text-decoration: none; text-align: center; color: #374151;">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('variant_key');
        const qtyInput = document.getElementById('quantity');
        const dateInput = document.getElementById('created_at');
        const infoCard = document.getElementById('stock-info-card');
        const infoAvailable = document.getElementById('info-available');
        const infoHpp = document.getElementById('info-hpp');

        const lossPreview = document.getElementById('loss-preview');
        const previewItemName = document.getElementById('preview-item-name');
        const previewWeight = document.getElementById('preview-weight');
        const previewQty = document.getElementById('preview-qty');
        const previewHpp = document.getElementById('preview-hpp');
        const previewTotalLoss = document.getElementById('preview-total-loss');

        const checkbox = document.getElementById('showAllCheckbox');
        
        function applyFilter() {
            const showAll = checkbox.checked;
            let visibleCount = 0;

            for (let i = 0; i < selectElement.options.length; i++) {
                const opt = selectElement.options[i];
                if (!opt.value) continue;

                if (showAll || opt.getAttribute('data-is-expired') === '1') {
                    opt.style.display = '';
                    opt.disabled = false;
                    visibleCount++;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                }
            }

            if (selectElement.selectedOptions[0] && selectElement.selectedOptions[0].disabled) {
                selectElement.value = '';
                selectElement.dispatchEvent(new Event('change'));
            }
        }

        if (checkbox) {
            checkbox.addEventListener('change', applyFilter);
        }
        applyFilter();

        function formatRupiah(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        selectElement.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                const available = parseInt(selectedOption.getAttribute('data-available'));
                const buyPrice = parseFloat(selectedOption.getAttribute('data-buy-price'));
                const itemName = selectedOption.getAttribute('data-item-name');
                const weight = selectedOption.getAttribute('data-weight');
                const expiryDate = selectedOption.getAttribute('data-expiry-date');

                qtyInput.disabled = false;
                qtyInput.max = available;
                qtyInput.value = available; // Auto-fill with available quantity!

                if (dateInput && expiryDate) {
                    dateInput.value = expiryDate;
                    const displayDateText = document.getElementById('display-date-text');
                    if (displayDateText) {
                        try {
                            const d = new Date(expiryDate);
                            const options = { day: 'numeric', month: 'long', year: 'numeric' };
                            displayDateText.textContent = d.toLocaleDateString('id-ID', options);
                        } catch(e) {
                            displayDateText.textContent = expiryDate;
                        }
                    }
                }

                infoAvailable.textContent = formatRupiah(available);
                infoHpp.textContent = formatRupiah(buyPrice);
                infoCard.style.display = 'block';

                previewItemName.textContent = itemName;
                previewWeight.textContent = weight;
                previewHpp.textContent = formatRupiah(buyPrice);
                updateLossPreview();
            } else {
                qtyInput.disabled = true;
                qtyInput.value = '';
                infoCard.style.display = 'none';
                lossPreview.style.display = 'none';
                const displayDateText = document.getElementById('display-date-text');
                if (displayDateText) {
                    displayDateText.textContent = 'Pilih produk terlebih dahulu';
                }
            }
        });

        qtyInput.addEventListener('input', updateLossPreview);

        function updateLossPreview() {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            if (!selectedOption || !selectedOption.value) return;

            const qty = parseInt(qtyInput.value) || 0;
            const buyPrice = parseFloat(selectedOption.getAttribute('data-buy-price')) || 0;
            const totalLoss = qty * buyPrice;

            previewQty.textContent = formatRupiah(qty);
            previewTotalLoss.textContent = formatRupiah(totalLoss);
            lossPreview.style.display = 'block';
        }
    });
</script>

@endsection
