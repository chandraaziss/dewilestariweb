@extends('admin.layout')

@section('content')

<div class="admin-card">
    <h1 class="admin-title">
        ✏️ Edit Catatan Barang Kadaluarsa
    </h1>

    <p style="color: #666; margin-bottom: 20px;">
        Ubah jumlah atau catatan barang kadaluarsa. Perubahan jumlah akan otomatis menyesuaikan stok yang tersedia di sistem.
    </p>

    @if(session('error'))
        <div style="padding: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #dc2626;">
            {{ session('error') }}
        </div>
    @endif

    <form action="/admin/supplier-stocks/expired/{{ $log->id }}" method="POST">
        @csrf
        @method('PUT')

        @php
            $itemName = $log->supplierStock->item_name ?? 'Produk Dihapus';
            $supplierName = $log->supplierStock->supplier->name ?? 'Tanpa Supplier';
        @endphp

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; color: #374151; display: block; margin-bottom: 5px;">Produk & Ukuran Varian</label>
            <input type="text" class="form-control" value="{{ $itemName }} ({{ $log->weight ?: '-' }}) - [Supplier: {{ $supplierName }}]" readonly style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; background: #f3f4f6; color: #4b5563; font-weight: bold;">
        </div>

        <div style="background: #f0fdf4; padding: 15px; border-radius: 8px; border: 1px solid #bbf7d0; margin-bottom: 20px; color: #166534; font-size: 14px;">
            <strong>Informasi Harga:</strong>
            <span style="display: block; margin-top: 5px;">Harga Beli (HPP): <strong>Rp {{ number_format($log->buy_price, 0, ',', '.') }}</strong></span>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; color: #2e7d32; display: block; margin-bottom: 5px;">Tanggal Retur / Kadaluarsa *</label>
            <input type="date" name="created_at" class="form-control" value="{{ old('created_at', $log->created_at ? $log->created_at->format('Y-m-d') : date('Y-m-d')) }}" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; color: #2e7d32; display: block; margin-bottom: 5px;">Jumlah Barang Kadaluarsa (Qty) *</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', $log->quantity) }}" required min="1" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: bold; color: #2e7d32; display: block; margin-bottom: 5px;">Catatan / Keterangan (Opsional)</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Rusak karena kemasan robek atau berjamur..." style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">{{ old('description', $log->description) }}</textarea>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-warning" style="padding: 12px 24px; font-weight: bold; border-radius: 8px; border: none; cursor: pointer; background: #f59e0b; color: white;">
                💾 Simpan Perubahan
            </button>
            <a href="/admin/supplier-stocks/expired-list" class="btn btn-outline" style="padding: 12px 24px; font-weight: bold; border-radius: 8px; border: 1px solid #d1d5db; text-decoration: none; text-align: center; color: #374151;">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
