@extends('admin.layout')

@section('content')

    <div class="admin-card">
        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
            <h1 class="admin-title" style="margin: 0;">
                ⚠️ Daftar Barang Kadaluarsa / Rusak
            </h1>
            <div style="display: flex; gap: 10px;">
                <a href="/admin/supplier-stocks/expired" class="btn btn-danger"
                    style="background: #dc2626; color: white; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                    + Catat Kadaluarsa Baru
                </a>
                <a href="/admin/supplier-stocks" class="btn btn-outline"
                    style="padding: 10px 18px; border-radius: 8px; border: 1px solid #d1d5db; text-decoration: none; font-weight: bold; color: #374151;">
                    ⬅ Kembali ke Stok
                </a>
            </div>
        </div>

        <p style="color: #666; margin-bottom: 20px;">
            Berikut adalah daftar riwayat barang kadaluarsa/rusak yang telah dicatat. Gunakan filter di bawah untuk
            menyaring berdasarkan status <strong>(Kadaluwarsa / Rusak)</strong> dan <strong>Supplier</strong>.
        </p>

        @if(session('success'))
            <div
                style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #16a34a;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div
                style="padding: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #dc2626;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Clean Filter Bar (Status, Supplier & Search) -->
        <div
            style="background: #fff5f5; border: 1px solid #fca5a5; border-radius: 12px; padding: 16px 20px; margin-bottom: 25px; box-shadow: 0 2px 6px rgba(220,38,38,0.04);">
            <form method="GET" action="/admin/supplier-stocks/expired-list" id="expiredFilterForm"
                style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 14px;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px; flex: 1;">
                    <!-- Filter Status (Kadaluwarsa / Rusak) -->
                    <div>
                        <label
                            style="display: block; font-size: 11.5px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">🏷️
                            Status Barang</label>
                        <select name="filter" id="filterCategory"
                            style="padding: 8px 12px; border-radius: 8px; border: 1.5px solid #fca5a5; font-size: 13px; font-weight: 600; color: #1f2937; outline: none; background: white; cursor: pointer; min-width: 170px;"
                            onchange="applyClientFilter()">
                            <option value="all" {{ request('filter') === 'all' || !request('filter') ? 'selected' : '' }}>📋
                                Semua Status</option>
                            <option value="kadaluwarsa" {{ request('filter') === 'kadaluwarsa' ? 'selected' : '' }}>📅
                                Kadaluwarsa</option>
                            <option value="rusak" {{ request('filter') === 'rusak' ? 'selected' : '' }}>⚠️ Rusak / Berjamur
                            </option>
                        </select>
                    </div>

                    <!-- Filter Supplier -->
                    <div>
                        <label
                            style="display: block; font-size: 11.5px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">🏢
                            Supplier</label>
                        <select name="supplier_id" id="filterSupplier"
                            style="padding: 8px 12px; border-radius: 8px; border: 1.5px solid #fca5a5; font-size: 13px; font-weight: 600; color: #1f2937; outline: none; background: white; cursor: pointer; min-width: 180px;"
                            onchange="applyClientFilter()">
                            <option value="">🏢 Semua Supplier</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pencarian Text -->
                    <div>
                        <label
                            style="display: block; font-size: 11.5px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">🔎
                            Cari Produk</label>
                        <input type="text" name="search" id="filterSearch" value="{{ request('search') }}"
                            placeholder="Ketik nama produk..."
                            style="padding: 8px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 13px; outline: none; background: white; min-width: 180px;"
                            oninput="applyClientFilter()">
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="font-size: 12.5px; color: #64748b; font-weight: 600;" id="filterResultBadge">
                        Menampilkan <strong id="visibleCount"
                            style="color: #dc2626; font-size: 14px;">{{ count($logs) }}</strong> dari
                        <strong>{{ count($logs) }}</strong> data
                    </div>
                    <a href="/admin/supplier-stocks/expired-list" onclick="resetFilters(event)"
                        style="background: white; color: #4b5563; border: 1px solid #cbd5e1; padding: 7px 14px; border-radius: 8px; font-weight: bold; text-decoration: none; font-size: 12px; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">
                        🔄 Reset
                    </a>
                </div>
            </form>
        </div>

        <div style="overflow-x: auto;">
            <table class="stock-table"
                style="width: 100%; border-collapse: collapse; margin-top: 10px; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.04); border: 1px solid #e5e7eb;">
                <thead>
                    <tr style="background: #fdf2f2; border-bottom: 2px solid #fca5a5;">
                        <th style="padding: 14px 16px; text-align: center; width: 5%;">No</th>
                        <th style="padding: 14px 16px;">Tanggal Dicatat</th>
                        <th style="padding: 14px 16px;">Nama Produk</th>
                        <th style="padding: 14px 16px;">Supplier</th>
                        <th style="padding: 14px 16px; text-align: center;">Ukuran</th>
                        <th style="padding: 14px 16px; text-align: right;">Qty Kadaluarsa</th>
                        <th style="padding: 14px 16px; text-align: right;">Harga Beli (HPP)</th>
                        <th style="padding: 14px 16px; text-align: right;">Total Kerugian</th>
                        <th style="padding: 14px 16px;">Catatan / Alasan</th>
                        <th style="padding: 14px 16px; text-align: center; width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="expiredTableBody">
                    @php $rowNo = 0;
                        $totalLoss = 0;
                    $totalQty = 0; @endphp
                    @forelse($logs as $log)
                        @php 
                                                $rowNo++;
                            $totalLoss += $log->loss_value;
                            $totalQty += $log->quantity;
                            $supplierId = $log->supplierStock ? $log->supplierStock->supplier_id : '';
                            $supplierName = $log->supplierStock->supplier->name ?? 'Tanpa Supplier';
                            $itemName = $log->supplierStock->item_name ?? 'Produk Dihapus';
                            $rawDesc = strtolower($log->description ?? '');
                            $isKadaluwarsa = (in_array($log->description, ['Manually logged expired', 'Kadaluarsa / Rusak', 'Telah masuk tanggal kadaluwarsa']) || empty($log->description) || str_contains($rawDesc, 'kadaluw') || str_contains($rawDesc, 'expired'));
                            $categoryType = $isKadaluwarsa ? 'kadaluwarsa' : 'rusak';
                            $reasonText = $isKadaluwarsa ? 'kadaluwarsa' : $log->description;
                        @endphp
                        <tr class="expired-row" data-category="{{ $categoryType }}" data-supplier-id="{{ $supplierId }}"
                            data-search="{{ strtolower($itemName . ' ' . $supplierName . ' ' . $log->weight . ' ' . $reasonText) }}"
                            data-qty="{{ $log->quantity }}" data-loss="{{ $log->loss_value }}"
                            style="border-bottom: 1px solid #fee2e2;">
                            <td style="padding: 12px 16px; text-align: center;">{{ $rowNo }}</td>
                            <td style="padding: 12px 16px; font-size: 13px; color: #4b5563;">
                                {{ $log->created_at ? $log->created_at->translatedFormat('d M Y, H:i') : '-' }}
                            </td>
                            <td style="padding: 12px 16px; font-weight: bold; color: #111827;">{{ $itemName }}</td>
                            <td style="padding: 12px 16px; color: #4b5563;">{{ $supplierName }}</td>
                            <td style="padding: 12px 16px; text-align: center; font-weight: bold; color: #dc2626;">
                                {{ $log->weight ?: '-' }}</td>
                            <td style="padding: 12px 16px; text-align: right; font-weight: bold; color: #dc2626;">
                                {{ number_format($log->quantity, 0, ',', '.') }} bungkus</td>
                            <td style="padding: 12px 16px; text-align: right;">Rp
                                {{ number_format($log->buy_price, 0, ',', '.') }}</td>
                            <td style="padding: 12px 16px; text-align: right; font-weight: bold; color: #991b1b;">Rp
                                {{ number_format($log->loss_value, 0, ',', '.') }}</td>
                            <td style="padding: 12px 16px; font-size: 12.5px; color: #64748b;">
                                <span
                                    style="background: {{ $isKadaluwarsa ? '#fef2f2' : '#fff7ed' }}; color: {{ $isKadaluwarsa ? '#991b1b' : '#c2410c' }}; padding: 3px 10px; border-radius: 6px; border: 1px solid {{ $isKadaluwarsa ? '#fca5a5' : '#fed7aa' }}; font-size: 12px; font-weight: 600;">
                                    {{ $reasonText }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="/admin/supplier-stocks/expired/{{ $log->id }}/edit"
                                        style="background: #f59e0b; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold;">
                                        ✏️ Edit
                                    </a>
                                    <form action="/admin/supplier-stocks/expired/{{ $log->id }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan barang kadaluarsa ini? Stok sebesar {{ $log->quantity }} bungkus akan dipulihkan kembali ke stok tersedia.');"
                                        style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            style="background: #dc2626; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: bold;">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 30px; color: #6b7280;">
                                Belum ada riwayat catatan barang kadaluarsa.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="noFilterDataRow" style="display: none;">
                        <td colspan="10"
                            style="text-align: center; padding: 30px; color: #991b1b; font-weight: bold; background: #fff5f5;">
                            🔍 Tidak ada data barang yang sesuai dengan kombinasi filter ini.
                        </td>
                    </tr>
                </tbody>
                @if(count($logs) > 0)
                    <tfoot>
                        <tr style="background: #fef2f2; font-weight: bold; border-top: 2px solid #fca5a5;">
                            <td colspan="5" style="padding: 14px 16px; text-align: left; font-size: 13.5px;">TOTAL AKUMULASI
                                TERFILTER</td>
                            <td id="footerTotalQty"
                                style="padding: 14px 16px; text-align: right; color: #dc2626; font-size: 14px;">
                                {{ number_format($totalQty, 0, ',', '.') }} bungkus</td>
                            <td style="padding: 14px 16px;">-</td>
                            <td id="footerTotalLoss"
                                style="padding: 14px 16px; text-align: right; color: #991b1b; font-size: 15px;">Rp
                                {{ number_format($totalLoss, 0, ',', '.') }}</td>
                            <td colspan="2" style="padding: 14px 16px;">-</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <script>
        function applyClientFilter() {
            const categoryVal = document.getElementById('filterCategory').value;
            const supplierVal = document.getElementById('filterSupplier').value;
            const searchVal = (document.getElementById('filterSearch').value || '').toLowerCase().trim();

            const rows = document.querySelectorAll('.expired-row');
            let visibleCount = 0;
            let totalQtySum = 0;
            let totalLossSum = 0;

            rows.forEach(row => {
                const rowCategory = row.getAttribute('data-category') || '';
                const rowSupplierId = row.getAttribute('data-supplier-id') || '';
                const rowSearch = row.getAttribute('data-search') || '';
                const qty = parseFloat(row.getAttribute('data-qty')) || 0;
                const loss = parseFloat(row.getAttribute('data-loss')) || 0;

                let match = true;

                if (categoryVal !== 'all' && rowCategory !== categoryVal) {
                    match = false;
                }

                if (supplierVal && rowSupplierId !== supplierVal) {
                    match = false;
                }

                if (searchVal && !rowSearch.includes(searchVal)) {
                    match = false;
                }

                if (match) {
                    row.style.display = '';
                    visibleCount++;
                    totalQtySum += qty;
                    totalLossSum += loss;
                } else {
                    row.style.display = 'none';
                }
            });

            const visibleCountEl = document.getElementById('visibleCount');
            if (visibleCountEl) visibleCountEl.textContent = visibleCount;

            const footerQty = document.getElementById('footerTotalQty');
            if (footerQty) footerQty.textContent = totalQtySum.toLocaleString('id-ID') + ' bungkus';

            const footerLoss = document.getElementById('footerTotalLoss');
            if (footerLoss) footerLoss.textContent = 'Rp ' + totalLossSum.toLocaleString('id-ID');

            const noDataRow = document.getElementById('noFilterDataRow');
            if (noDataRow) {
                noDataRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
            }
        }

        function resetFilters(e) {
            e.preventDefault();
            document.getElementById('filterCategory').value = 'all';
            document.getElementById('filterSupplier').value = '';
            document.getElementById('filterSearch').value = '';
            applyClientFilter();
            if (window.location.search) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    </script>

@endsection