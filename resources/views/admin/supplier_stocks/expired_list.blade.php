@extends('admin.layout')

@section('content')

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h1 class="admin-title" style="margin: 0;">
            ⚠️ Daftar Barang Kadaluarsa / Rusak
        </h1>
        <div style="display: flex; gap: 10px;">
            <a href="/admin/supplier-stocks/expired" class="btn btn-danger" style="background: #dc2626; color: white; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                + Input Kadaluarsa Baru
            </a>
            <a href="/admin/supplier-stocks" class="btn btn-outline" style="padding: 10px 18px; border-radius: 8px; border: 1px solid #d1d5db; text-decoration: none; font-weight: bold; color: #374151;">
                ⬅ Kembali ke Stok
            </a>
        </div>
    </div>

    <p style="color: #666; margin-bottom: 25px;">
        Berikut adalah daftar riwayat barang kadaluarsa/rusak yang telah dicatat. Anda dapat mengubah jumlah atau menghapus catatan (stok akan dikembalikan ke manajemen stok secara otomatis saat dihapus).
    </p>

    @if(session('success'))
        <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #16a34a;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border-left: 4px solid #dc2626;">
            {{ session('error') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table class="stock-table" style="width: 100%; border-collapse: collapse; margin-top: 10px; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.04); border: 1px solid #e5e7eb;">
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
            <tbody>
                @php $rowNo = 0; $totalLoss = 0; $totalQty = 0; @endphp
                @forelse($logs as $log)
                    @php 
                        $rowNo++;
                        $totalLoss += $log->loss_value;
                        $totalQty += $log->quantity;
                        $supplierName = $log->supplierStock->supplier->name ?? 'Tanpa Supplier';
                        $itemName = $log->supplierStock->item_name ?? 'Produk Dihapus';
                    @endphp
                    <tr style="border-bottom: 1px solid #fee2e2;">
                        <td style="padding: 12px 16px; text-align: center;">{{ $rowNo }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #4b5563;">
                            {{ $log->created_at ? $log->created_at->translatedFormat('d M Y, H:i') : '-' }}
                        </td>
                        <td style="padding: 12px 16px; font-weight: bold; color: #111827;">{{ $itemName }}</td>
                        <td style="padding: 12px 16px; color: #4b5563;">{{ $supplierName }}</td>
                        <td style="padding: 12px 16px; text-align: center; font-weight: bold; color: #dc2626;">{{ $log->weight ?: '-' }}</td>
                        <td style="padding: 12px 16px; text-align: right; font-weight: bold; color: #dc2626;">{{ number_format($log->quantity, 0, ',', '.') }} pcs</td>
                        <td style="padding: 12px 16px; text-align: right;">Rp {{ number_format($log->buy_price, 0, ',', '.') }}</td>
                        <td style="padding: 12px 16px; text-align: right; font-weight: bold; color: #991b1b;">Rp {{ number_format($log->loss_value, 0, ',', '.') }}</td>
                        <td style="padding: 12px 16px; font-size: 12.5px; color: #64748b;">{{ $log->description ?: '-' }}</td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="/admin/supplier-stocks/expired/{{ $log->id }}/edit" style="background: #f59e0b; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: bold;">
                                    ✏️ Edit
                                </a>
                                <form action="/admin/supplier-stocks/expired/{{ $log->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan barang kadaluarsa ini? Stok sebesar {{ $log->quantity }} pcs akan dipulihkan kembali ke stok tersedia.');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #dc2626; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: bold;">
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
            </tbody>
            @if(count($logs) > 0)
                <tfoot>
                    <tr style="background: #fef2f2; font-weight: bold; border-top: 2px solid #fca5a5;">
                        <td colspan="5" style="padding: 14px 16px; text-align: left; font-size: 13.5px;">TOTAL AKUMULASI KADALUARSA</td>
                        <td style="padding: 14px 16px; text-align: right; color: #dc2626; font-size: 14px;">{{ number_format($totalQty, 0, ',', '.') }} pcs</td>
                        <td style="padding: 14px 16px;">-</td>
                        <td style="padding: 14px 16px; text-align: right; color: #991b1b; font-size: 15px;">Rp {{ number_format($totalLoss, 0, ',', '.') }}</td>
                        <td colspan="2" style="padding: 14px 16px;">-</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
