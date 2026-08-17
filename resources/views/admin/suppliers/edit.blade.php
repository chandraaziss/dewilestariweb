@extends('admin.layout')

@section('content')
<div class="admin-card">
    <div class="admin-header">
        <h1 class="admin-title">✏️ Edit Supplier</h1>
        <a href="/admin/suppliers/manage" class="btn btn-warning">← Kembali</a>
    </div>

    <form action="/admin/suppliers/{{ $supplier->slug }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama Supplier</label>
            <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
        </div>

        <div class="form-group">
            <label>Telepon Supplier</label>
            <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}">
        </div>

        <div class="form-group">
            <label>Produk yang dijual</label>
            <div id="supplier-items">
                @foreach($supplier->items ?? [] as $index => $item)
                    <div class="form-row" style="display:grid; gap:10px; margin-bottom:12px;">
                        <input type="text" name="items[{{ $index }}][name]" class="form-control" value="{{ $item['name'] ?? '' }}" required>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-warning" onclick="addSupplierItem()" style="margin-top:8px;">+ Tambah Produk</button>
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
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
                <input type="text" name="items[${index}][name]" class="form-control" placeholder="Nama produk" required>
            `;
            container.appendChild(row);
        }
    </script>
</div>
@endsection
