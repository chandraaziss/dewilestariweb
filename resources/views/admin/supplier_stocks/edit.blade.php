@extends('admin.layout')

@section('content')

<div class="admin-card">
    <h1 class="admin-title">
        ✏️ Edit Barang Masuk dari Supplier
    </h1>

    <form action="/admin/supplier-stocks/{{ $stock->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama Supplier</label>
            <select name="supplier_id" class="form-control" required>
                <option value="">-- Pilih Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ $stock->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="item_name" class="form-control" placeholder="Contoh: Keripik Singkong" value="{{ $stock->item_name }}" required>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 20px;">
            <h3 style="margin-top: 0; color: #2e7d32; display: flex; justify-content: space-between; align-items: center;">
                <span>Varian Ukuran & Harga</span>
                <button type="button" class="btn btn-success btn-sm" onclick="addVariantRow()" style="padding: 5px 12px; font-size: 13px; font-weight: 600;">+ Tambah Varian</button>
            </h3>
            
            <div id="variants-container">
                @php
                    $variants = $stock->variants ?? [];
                    if (empty($variants)) {
                        $variants = [['weight' => '', 'initial_quantity' => 1, 'available_quantity' => 1, 'buy_price' => 0, 'price' => 0, 'expiry_date' => null]];
                    }
                @endphp
                @foreach($variants as $index => $v)
                    <div class="variant-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-end;">
                        <input type="hidden" name="variants[{{ $index }}][available_quantity]" value="{{ $v['available_quantity'] ?? $v['initial_quantity'] ?? 0 }}">
                        
                        <div style="flex: 2;">
                            <label style="font-size: 12px; font-weight: bold; color: #555;">Ukuran *</label>
                            <input type="text" name="variants[{{ $index }}][weight]" class="form-control" placeholder="Contoh: 250 gram" value="{{ $v['weight'] ?? '' }}" required>
                        </div>
                        <div style="flex: 1.5;">
                            <label style="font-size: 12px; font-weight: bold; color: #555;">Jumlah Stok *</label>
                            <input type="number" name="variants[{{ $index }}][initial_quantity]" class="form-control" placeholder="Stok" value="{{ $v['initial_quantity'] ?? 0 }}" required min="1">
                        </div>
                        <div style="flex: 2;">
                            <label style="font-size: 12px; font-weight: bold; color: #555;">Harga Beli (Modal) *</label>
                            <input type="number" name="variants[{{ $index }}][buy_price]" class="form-control buy-price-input" placeholder="Harga Beli" value="{{ (int) ($v['buy_price'] ?? (($v['price'] ?? 0) * 0.7)) }}" required min="0" oninput="calculateSellingPrice(this)">
                        </div>
                        <div style="flex: 2;">
                            <label style="font-size: 12px; font-weight: bold; color: #166534;">Harga Jual (+30% Otomatis) *</label>
                            <input type="number" name="variants[{{ $index }}][price]" class="form-control sell-price-input" placeholder="Harga Jual" value="{{ (int) ($v['price'] ?? 0) }}" required min="0">
                        </div>
                        <div style="flex: 2;">
                            <label style="font-size: 12px; font-weight: bold; color: #555;">Tgl Masuk *</label>
                            <input type="date" name="variants[{{ $index }}][entry_date]" class="form-control" value="{{ !empty($v['entry_date']) ? \Carbon\Carbon::parse($v['entry_date'])->format('Y-m-d') : ($stock->entry_date ? $stock->entry_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>
                        <div style="flex: 2;">
                            <label style="font-size: 12px; font-weight: bold; color: #555;">Tgl Expire (Opsional)</label>
                            <input type="date" name="variants[{{ $index }}][expiry_date]" class="form-control" value="{{ !empty($v['expiry_date']) ? \Carbon\Carbon::parse($v['expiry_date'])->format('Y-m-d') : '' }}">
                        </div>
                        <div style="flex: 0.5;">
                            <button type="button" class="btn btn-danger" onclick="removeVariantRow(this)" style="width: 100%;">&times;</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi Produk</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Masukkan deskripsi produk..." required>{{ $stock->description }}</textarea>
        </div>

        <div class="form-group">
            <label>Gambar Produk</label>
            @if($stock->image_path)
                <div style="margin-bottom: 10px;">
                    <img src="{{ asset($stock->image_path) }}" alt="{{ $stock->item_name }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ccc;">
                    <small style="display: block; color: #666;">Gambar saat ini</small>
                </div>
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
            <small style="color: #666;">Biarkan kosong jika tidak ingin mengubah gambar.</small>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 15px; margin-bottom: 15px;">
                <input type="checkbox" name="is_active" value="1" {{ $stock->is_active ? 'checked' : '' }}>
                <strong style="color: #2e7d32;">Tampilkan Produk di Halaman Depan</strong>
            </label>
        </div>

        <script>
        function calculateSellingPrice(input) {
            const row = input.closest('.variant-row');
            if (!row) return;
            const sellInput = row.querySelector('.sell-price-input');
            if (!sellInput) return;
            const buyVal = parseFloat(input.value) || 0;
            if (buyVal > 0) {
                sellInput.value = Math.round(buyVal * 1.30);
            }
        }

        let variantIndex = {{ count($variants) }};
        function addVariantRow() {
            const container = document.getElementById('variants-container');
            const todayStr = new Date().toISOString().split('T')[0];
            const newRow = document.createElement('div');
            newRow.className = 'variant-row';
            newRow.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-end;';
            newRow.innerHTML = `
                <div style="flex: 2;">
                    <input type="text" name="variants[${variantIndex}][weight]" class="form-control" placeholder="Contoh: 250 gram" required>
                </div>
                <div style="flex: 1.5;">
                    <input type="number" name="variants[${variantIndex}][initial_quantity]" class="form-control" placeholder="Stok" required min="1">
                </div>
                <div style="flex: 2;">
                    <input type="number" name="variants[${variantIndex}][buy_price]" class="form-control buy-price-input" placeholder="Harga Beli" required min="0" oninput="calculateSellingPrice(this)">
                </div>
                <div style="flex: 2;">
                    <input type="number" name="variants[${variantIndex}][price]" class="form-control sell-price-input" placeholder="Harga Jual (+30%)" required min="0">
                </div>
                <div style="flex: 2;">
                    <input type="date" name="variants[${variantIndex}][entry_date]" class="form-control" value="${todayStr}" required>
                </div>
                <div style="flex: 2;">
                    <input type="date" name="variants[${variantIndex}][expiry_date]" class="form-control">
                </div>
                <div style="flex: 0.5;">
                    <button type="button" class="btn btn-danger" onclick="removeVariantRow(this)" style="width: 100%;">&times;</button>
                </div>
            `;
            container.appendChild(newRow);
            variantIndex++;
            updateRemoveButtons();
        }

        function addBatchRow() {
            const container = document.getElementById('variants-container');
            const todayStr = new Date().toISOString().split('T')[0];
            const existingWeightInput = container.querySelector('input[name*="[weight]"]');
            const defaultWeight = existingWeightInput ? existingWeightInput.value : '';

            const newRow = document.createElement('div');
            newRow.className = 'variant-row';
            newRow.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-end; background: #f0f9ff; padding: 10px; border-radius: 8px; border: 1px solid #bae6fd;';
            newRow.innerHTML = `
                <div style="flex: 2;">
                    <label style="font-size: 11px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 2px;">📦 Ukuran Batch Baru *</label>
                    <input type="text" name="variants[${variantIndex}][weight]" class="form-control" placeholder="Contoh: 250 gram" value="${defaultWeight}" required>
                </div>
                <div style="flex: 1.5;">
                    <label style="font-size: 11px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 2px;">Jumlah Stok *</label>
                    <input type="number" name="variants[${variantIndex}][initial_quantity]" class="form-control" placeholder="Stok" required min="1">
                </div>
                <div style="flex: 2;">
                    <label style="font-size: 11px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 2px;">Harga Beli (Modal) *</label>
                    <input type="number" name="variants[${variantIndex}][buy_price]" class="form-control buy-price-input" placeholder="Harga Beli" required min="0" oninput="calculateSellingPrice(this)">
                </div>
                <div style="flex: 2;">
                    <label style="font-size: 11px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 2px;">Harga Jual (+30%) *</label>
                    <input type="number" name="variants[${variantIndex}][price]" class="form-control sell-price-input" placeholder="Harga Jual" required min="0">
                </div>
                <div style="flex: 2;">
                    <label style="font-size: 11px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 2px;">Tgl Masuk Batch *</label>
                    <input type="date" name="variants[${variantIndex}][entry_date]" class="form-control" value="${todayStr}" required>
                </div>
                <div style="flex: 2;">
                    <label style="font-size: 11px; font-weight: bold; color: #0369a1; display: block; margin-bottom: 2px;">Tgl Expire (Opsional)</label>
                    <input type="date" name="variants[${variantIndex}][expiry_date]" class="form-control">
                </div>
                <div style="flex: 0.5;">
                    <button type="button" class="btn btn-danger" onclick="removeVariantRow(this)" style="width: 100%;">&times;</button>
                </div>
            `;
            container.appendChild(newRow);
            variantIndex++;
            updateRemoveButtons();
        }

        function removeVariantRow(button) {
            const row = button.closest('.variant-row');
            row.remove();
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.variant-row');
            rows.forEach((row, index) => {
                const btn = row.querySelector('.btn-danger');
                if (rows.length === 1) {
                    btn.style.display = 'none';
                } else {
                    btn.style.display = 'block';
                }
            });
        }
        
        // Init
        updateRemoveButtons();
        </script>

        <button type="submit" class="btn btn-success">
            Simpan Perubahan
        </button>
        <a href="/admin/supplier-stocks" class="btn btn-warning" style="margin-left:10px;">Batal</a>
    </form>
</div>
@endsection
