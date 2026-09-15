@extends('admin.layout')

@section('content')

    <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <h1 class="admin-title" style="margin: 0;">
                ⚠️ Catat Barang / Rusak / Berjamur
            </h1>
            <a href="/admin/supplier-stocks" class="btn btn-outline" style="padding: 8px 16px; border-radius: 8px; border: 1px solid #d1d5db; text-decoration: none; font-weight: bold; color: #374151; font-size: 13px;">
                ⬅ Kembali ke Stok
            </a>
        </div>

        <p style="color: #666; margin-bottom: 20px;">
            Gunakan form ini untuk mencatat barang dari supplier yang kadaluarsa, berjamur, atau rusak fisik. Pilih produk terlebih dahulu, kemudian pilih ukuran varian barang yang bermasalah.
        </p>

        @if(session('error'))
            <div style="padding: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #dc2626;">
                {{ session('error') }}
            </div>
        @endif

        <form action="/admin/supplier-stocks/expired" method="POST" id="expiredForm">
            @csrf
            <input type="hidden" name="variant_key" id="variant_key" value="" required>

            <!-- Langkah 1: Pilih Produk -->
            <div class="form-group" style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="font-weight: bold; color: #2e7d32; margin: 0; font-size: 14.5px;">1. Pilih Produk Barang *</label>
                    <span style="font-size: 12px; color: #166534; background: #dcfce7; padding: 2px 10px; border-radius: 12px; font-weight: 600; border: 1px solid #86efac;">
                        📦 Stok Produk Aktif
                    </span>
                </div>
                <select id="product_select" class="form-control" style="width: 100%; padding: 12px; border: 1.5px solid #2e7d32; border-radius: 8px; font-size: 14px; font-weight: 600; outline: none; background: white; cursor: pointer;" onchange="onProductChange(this.value)">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($stocks as $stock)
                        @php
                            $supplierName = $stock->supplier->name ?? 'Tanpa Supplier';
                            $isSelected = (isset($selectedStockId) && (int)$selectedStockId === (int)$stock->id);
                        @endphp
                        <option value="{{ $stock->id }}" {{ $isSelected ? 'selected' : '' }}>
                            {{ $stock->item_name }} — [Supplier: {{ $supplierName }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Langkah 2: Pilihan Ukuran Varian Produk (Interactive Size Cards) -->
            <div id="size_section" style="display: none; margin-bottom: 25px; background: #f0fdf4; padding: 18px; border-radius: 12px; border: 1.5px solid #86efac;">
                <label style="font-weight: bold; color: #166534; display: block; margin-bottom: 10px; font-size: 14.5px;">
                    ⚖️ 2. Pilih Ukuran Varian Produk Ini *
                </label>
                <div id="size_cards_wrapper" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                    <!-- Cards dipopulasikan lewat JS -->
                </div>
            </div>

            <div id="stock-info-card" style="display: none; background: #ecfdf5; padding: 15px; border-radius: 10px; border: 1px solid #a7f3d0; margin-bottom: 20px; color: #166534; font-size: 14px;">
                <strong style="color: #065f46;">Informasi Stok Terpilih (Ukuran: <span id="info-selected-weight">-</span>):</strong>
                <div style="display: flex; gap: 20px; margin-top: 6px; flex-wrap: wrap;">
                    <div>Sisa Stok Tersedia: <strong id="info-available" style="color: #15803d; font-size: 16px;">0</strong> bungkus</div>
                    <div>Harga Beli (HPP): <strong>Rp <span id="info-hpp">0</span></strong></div>
                </div>
            </div>

            <!-- Hidden input for created_at date -->
            <input type="hidden" name="created_at" id="created_at" value="{{ date('Y-m-d') }}">

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: bold; color: #2e7d32; display: block; margin-bottom: 5px;">Tanggal Kadaluarsa Produk (Otomatis)</label>
                <div style="background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #cbd5e1; color: #334155; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                    <span>📅</span>
                    <span id="display-date-text" style="font-weight: bold; color: #0f172a;">Pilih produk & ukuran terlebih dahulu</span>
                    <span style="font-size: 12px; color: #64748b; margin-left: auto;">(Otomatis dari Stok Gudang)</span>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: bold; color: #2e7d32; display: block; margin-bottom: 5px;">Jumlah Barang Kadaluarsa / Berjamur / Rusak (Qty) *</label>
                <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Contoh: 2" required min="1" disabled style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-weight: bold; font-size: 15px;">
                <small style="color: #64748b; margin-top: 4px; display: block;">Masukkan jumlah pasti unit/bungkus barang yang rusak atau kadaluarsa (misal: 2 bungkus).</small>
            </div>

            <!-- Real-time Loss Preview Card -->
            <div id="loss-preview" style="display: none; background: #fff5f5; padding: 20px; border-radius: 10px; border: 1px solid #feb2b2; margin-bottom: 25px; color: #c53030;">
                <h3 style="margin: 0 0 10px 0; color: #9b2c2c; font-size: 15px; display: flex; align-items: center; gap: 6px;">
                    <span>🛑</span> Estimasi Rincian Kerugian Barang
                </h3>
                <ul style="margin: 0; padding-left: 20px; font-size: 13.5px; line-height: 1.6;">
                    <li>Nama Barang: <strong id="preview-item-name">-</strong></li>
                    <li>Ukuran Varian: <strong id="preview-weight">-</strong></li>
                    <li>Jumlah Rusak/Kadaluarsa: <strong id="preview-qty" style="font-size: 15px; color: #991b1b;">0</strong> bungkus</li>
                    <li>Harga Beli Satuan (HPP): <strong>Rp <span id="preview-hpp">0</span></strong></li>
                    <li style="margin-top: 8px; font-size: 15px; border-top: 1px dashed #feb2b2; padding-top: 8px;">
                        <strong>Total Kerugian Toko: <span style="font-size: 17px; font-weight: 800; color: #9b2c2c;">Rp <span id="preview-total-loss">0</span></span></strong>
                    </li>
                </ul>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: bold; color: #2e7d32; display: block; margin-bottom: 5px;">Catatan / Keterangan (Opsional)</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Contoh: 2 bungkus berjamur / kemasan robek..." style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;"></textarea>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-danger" style="padding: 12px 24px; font-weight: bold; border-radius: 8px; border: none; cursor: pointer; background: #dc2626; color: white;">
                    💾 Catat Kerugian Barang
                </button>
                <a href="/admin/supplier-stocks" class="btn btn-outline" style="padding: 12px 24px; font-weight: bold; border-radius: 8px; border: 1px solid #d1d5db; text-decoration: none; text-align: center; color: #374151;">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        const stocksData = @json($stocks);
        let currentStock = null;
        let selectedVariant = null;

        const initialStockId = "{{ $selectedStockId ?? '' }}";
        const initialWeight = "{{ $selectedWeight ?? '' }}";

        function formatRupiah(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function onProductChange(stockId) {
            const sizeSection = document.getElementById('size_section');
            const wrapper = document.getElementById('size_cards_wrapper');
            const variantKeyInput = document.getElementById('variant_key');
            const qtyInput = document.getElementById('quantity');
            const infoCard = document.getElementById('stock-info-card');
            const lossPreview = document.getElementById('loss-preview');
            const displayDateText = document.getElementById('display-date-text');

            wrapper.innerHTML = '';
            variantKeyInput.value = '';
            selectedVariant = null;
            qtyInput.disabled = true;
            qtyInput.value = '';
            infoCard.style.display = 'none';
            lossPreview.style.display = 'none';
            displayDateText.textContent = 'Pilih ukuran varian produk terlebih dahulu';

            if (!stockId) {
                sizeSection.style.display = 'none';
                return;
            }

            currentStock = stocksData.find(s => s.id == stockId);
            if (!currentStock || !currentStock.variants || currentStock.variants.length === 0) {
                sizeSection.style.display = 'none';
                return;
            }

            sizeSection.style.display = 'block';

            let targetWeightToSelect = initialWeight;

            currentStock.variants.forEach((v) => {
                const isAvailable = (parseInt(v.available_quantity) || 0) > 0;
                const card = document.createElement('div');
                card.className = 'size-card';
                card.setAttribute('data-weight', v.weight);
                card.style.cssText = `
                    padding: 12px 16px;
                    border-radius: 10px;
                    border: 2px solid ${isAvailable ? '#bbf7d0' : '#e2e8f0'};
                    background: ${isAvailable ? '#ffffff' : '#f8fafc'};
                    cursor: ${isAvailable ? 'pointer' : 'not-allowed'};
                    transition: all 0.2s ease;
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
                `;

                let isExpired = false;
                if (v.expiry_date) {
                    const expDate = new Date(v.expiry_date);
                    if (expDate < new Date().setHours(0,0,0,0)) {
                        isExpired = true;
                    }
                }

                card.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-weight: bold; font-size: 15px; color: ${isAvailable ? '#14532d' : '#94a3b8'};">
                            ⚖️ ${v.weight}
                        </span>
                        <span style="font-size: 11px; font-weight: bold; padding: 2px 8px; border-radius: 12px; background: ${isAvailable ? '#dcfce7' : '#f1f5f9'}; color: ${isAvailable ? '#15803d' : '#64748b'};">
                            ${isAvailable ? 'Stok: ' + v.available_quantity + ' bgk' : 'Habis'}
                        </span>
                    </div>
                    <div style="font-size: 12.5px; color: #475569;">
                        HPP: <strong>Rp ${formatRupiah(v.buy_price || 0)}</strong>
                    </div>
                    ${v.expiry_date ? `<div style="font-size: 11.5px; color: ${isExpired ? '#dc2626' : '#166534'};">📅 Expire: <strong>${v.expiry_date}</strong> ${isExpired ? '🚨 (EXPIRED)' : ''}</div>` : ''}
                `;

                if (isAvailable) {
                    card.onclick = function() {
                        selectSizeVariant(v, card);
                    };
                }

                wrapper.appendChild(card);

                // Auto select if target weight matches OR default to first available variant
                if (isAvailable && (!targetWeightToSelect || targetWeightToSelect.toLowerCase() === v.weight.toLowerCase())) {
                    if (!selectedVariant) {
                        selectSizeVariant(v, card);
                    }
                }
            });

            // Fallback: if no variant selected yet, select first available
            if (!selectedVariant) {
                const firstAvailable = currentStock.variants.find(v => (parseInt(v.available_quantity) || 0) > 0);
                if (firstAvailable) {
                    const cards = wrapper.querySelectorAll('.size-card');
                    cards.forEach(c => {
                        if (c.getAttribute('data-weight') === firstAvailable.weight) {
                            selectSizeVariant(firstAvailable, c);
                        }
                    });
                }
            }
        }

        function selectSizeVariant(variant, cardElement) {
            selectedVariant = variant;
            const variantKeyInput = document.getElementById('variant_key');
            variantKeyInput.value = `${currentStock.id}|${variant.weight}`;

            // Highlight selected card
            const cards = document.querySelectorAll('.size-card');
            cards.forEach(c => {
                c.style.borderColor = '#bbf7d0';
                c.style.background = '#ffffff';
                c.style.boxShadow = '0 1px 3px rgba(0,0,0,0.04)';
            });

            cardElement.style.borderColor = '#16a34a';
            cardElement.style.background = '#f0fdf4';
            cardElement.style.boxShadow = '0 0 0 3px rgba(22, 163, 74, 0.2)';

            // Update info card & date
            const qtyInput = document.getElementById('quantity');
            const infoCard = document.getElementById('stock-info-card');
            const infoSelectedWeight = document.getElementById('info-selected-weight');
            const infoAvailable = document.getElementById('info-available');
            const infoHpp = document.getElementById('info-hpp');
            const displayDateText = document.getElementById('display-date-text');

            qtyInput.disabled = false;
            qtyInput.max = variant.available_quantity;
            if (!qtyInput.value || parseInt(qtyInput.value) <= 0) {
                qtyInput.value = 1;
            }

            infoSelectedWeight.textContent = variant.weight;
            infoAvailable.textContent = formatRupiah(variant.available_quantity);
            infoHpp.textContent = formatRupiah(variant.buy_price || 0);
            infoCard.style.display = 'block';

            if (variant.expiry_date) {
                try {
                    const d = new Date(variant.expiry_date);
                    const options = { day: 'numeric', month: 'long', year: 'numeric' };
                    displayDateText.textContent = d.toLocaleDateString('id-ID', options);
                } catch (e) {
                    displayDateText.textContent = variant.expiry_date;
                }
            } else {
                displayDateText.textContent = 'Tidak diatur';
            }

            updateLossPreview();
        }

        function updateLossPreview() {
            if (!currentStock || !selectedVariant) return;

            const qtyInput = document.getElementById('quantity');
            const lossPreview = document.getElementById('loss-preview');
            const previewItemName = document.getElementById('preview-item-name');
            const previewWeight = document.getElementById('preview-weight');
            const previewQty = document.getElementById('preview-qty');
            const previewHpp = document.getElementById('preview-hpp');
            const previewTotalLoss = document.getElementById('preview-total-loss');

            const qty = parseInt(qtyInput.value) || 0;
            const buyPrice = parseFloat(selectedVariant.buy_price) || 0;
            const totalLoss = qty * buyPrice;

            previewItemName.textContent = currentStock.item_name;
            previewWeight.textContent = selectedVariant.weight;
            previewQty.textContent = formatRupiah(qty);
            previewHpp.textContent = formatRupiah(buyPrice);
            previewTotalLoss.textContent = formatRupiah(totalLoss);
            lossPreview.style.display = 'block';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const qtyInput = document.getElementById('quantity');
            if (qtyInput) {
                qtyInput.addEventListener('input', updateLossPreview);
            }

            const productSelect = document.getElementById('product_select');
            if (productSelect && productSelect.value) {
                onProductChange(productSelect.value);
            }
        });
    </script>

@endsection