@extends('admin.layout')

@section('content')
<div class="admin-card">
    <div class="admin-header">
        <h1 class="admin-title">🏷️ Manajemen Stok Supplier</h1>
        <a href="/admin/suppliers" class="btn btn-warning">← Kembali ke Supplier</a>
    </div>

    <p>Gunakan halaman ini untuk melihat list supplier dan menambahkan supplier baru beserta produk yang dijualnya.</p>

    @if(session('success'))
        <div style="background:#dff0d8; color:#3c763d; padding:12px 16px; border-radius:10px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display:grid; gap:24px;">
        <div style="background:#f9f9f9; padding:20px; border-radius:16px;">
            <h2 style="margin-top:0;">Supplier yang Terdaftar</h2>
            <div style="display:grid; gap:16px;">
                @foreach($suppliers as $supplier)
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
                    <div style="padding:16px; border:1px solid #e0e0e0; border-radius:12px; background:white;">
                        <h3 style="margin-top:0;">{{ $supplier->name ?? '' }}</h3>
                        <p style="margin:0 0 8px 0; color:#555;"><strong>Produk:</strong></p>
                        <ul style="margin:0 0 12px 18px; color:#444;">
                            @foreach($supplierItems as $itemName)
                                @if($itemName !== '')
                                    <li>{{ $itemName }}</li>
                                @endif
                            @endforeach
                        </ul>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="/admin/suppliers/{{ $supplier->slug }}" class="btn btn-success">Lihat Detail</a>
                            <a href="/admin/suppliers/{{ $supplier->slug }}/edit" class="btn btn-warning">Edit</a>
                            <form action="/admin/suppliers/{{ $supplier->slug }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus supplier ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="background:#fff8e1; padding:20px; border-radius:16px;">
            <h2 style="margin-top:0;">Tambah Supplier Baru</h2>
            <p>Masukkan nama supplier dan daftar produk yang dijualnya.</p>
            <form action="/admin/suppliers/manage" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Supplier</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Jaya Rasa" value="Jaya Rasa" required>
                </div>
                <div class="form-group">
                    <label>Telepon Supplier</label>
                    <input type="text" name="phone" class="form-control" placeholder="Contoh: 6281234567893">
                </div>
                <div class="form-group">
                    <label>Produk yang dijual</label>
                    <div id="supplier-items">
                        <div class="form-row" style="display:grid; gap:10px; margin-bottom:12px;">
                            <input type="text" name="items[0][name]" class="form-control" placeholder="Nama produk, contoh: Kripik Tempe" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-warning" onclick="addSupplierItem()" style="margin-top:8px;">+ Tambah Produk</button>
                </div>
                <button type="submit" class="btn btn-success">Tambahkan Supplier</button>
            </form>
            <script>
                function addSupplierItem() {
                    const container = document.getElementById('supplier-items');
                    const index = container.querySelectorAll('.form-row').length;
                    const row = document.createElement('div');
                    row.className = 'form-row';
                    row.style.display = 'grid';
                    row.style.gap = '10px';
                    row.style.marginBottom = '12px';
                    row.innerHTML = `
                        <input type="text" name="items[${index}][name]" class="form-control" placeholder="Nama produk, contoh: Kripik Tempe" required>
                    `;
                    container.appendChild(row);
                }
            </script>
            <div style="margin-top:16px; color:#5d4037;">
                Contoh: <strong>Jaya Rasa</strong> menjual <strong>Kripik Tempe</strong>.
            </div>
        </div>
    </div>
</div>
@endsection
