@extends('admin.layout')

@section('content')
<div class="admin-card">
    <div class="admin-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
            <h1 class="admin-title" style="margin: 0; font-size: 22px; color: #166534; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                📦 Daftar Pengiriman Kurir
            </h1>
            <small style="color: #64748b; font-size: 13px; font-weight: 600; display: block; margin-top: 4px;">
                ℹ️ Urutan pesanan disusun berdasarkan siapa yang pesan terlebih dahulu (FIFO). No. 1 adalah prioritas pengiriman utama.
            </small>
        </div>
        <a href="{{ route('courier.history') }}" class="btn btn-warning" style="padding: 9px 18px; font-weight: bold; border-radius: 8px;">📜 Lihat Riwayat</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border: 1px solid #c3e6cb;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @php
        $kurirAOrders = [];
        $kurirBOrders = [];
        $otherOrders = [];

        foreach($orders as $ord) {
            $addr = strtolower($ord->delivery_address ?? '');
            if (str_contains($addr, 'cimahi')) {
                $kurirAOrders[] = $ord;
            } elseif (str_contains($addr, 'bandung')) {
                $kurirBOrders[] = $ord;
            } else {
                $otherOrders[] = $ord;
            }
        }
    @endphp

    {{-- Filter Tabs Pemisah Kurir --}}
    <div style="display: flex; gap: 10px; margin-bottom: 22px; flex-wrap: wrap;">
        <button type="button" onclick="switchCourierTab('all')" id="tab-btn-all" class="courier-tab-btn" style="padding: 10px 18px; font-weight: bold; border-radius: 10px; border: 1px solid #15803d; background: #15803d; color: white; cursor: pointer; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
            📦 Semua Pengiriman <span style="background: rgba(255,255,255,0.25); padding: 2px 8px; border-radius: 12px; font-size: 12px;">{{ count($orders) }}</span>
        </button>

        <button type="button" onclick="switchCourierTab('kurir-a')" id="tab-btn-kurir-a" class="courier-tab-btn" style="padding: 10px 18px; font-weight: bold; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #1e293b; cursor: pointer; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
            🅰️ Kurir A (Kota Cimahi) <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">{{ count($kurirAOrders) }}</span>
        </button>

        <button type="button" onclick="switchCourierTab('kurir-b')" id="tab-btn-kurir-b" class="courier-tab-btn" style="padding: 10px 18px; font-weight: bold; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #1e293b; cursor: pointer; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
            🅱️ Kurir B (Kota Bandung) <span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">{{ count($kurirBOrders) }}</span>
        </button>

        @if(count($otherOrders) > 0)
            <button type="button" onclick="switchCourierTab('other')" id="tab-btn-other" class="courier-tab-btn" style="padding: 10px 18px; font-weight: bold; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #1e293b; cursor: pointer; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
                📍 Wilayah Lainnya <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;">{{ count($otherOrders) }}</span>
            </button>
        @endif
    </div>

    {{-- Tabel Pengiriman --}}
    <div style="overflow-x: auto;">
        <table class="admin-table" id="courier-deliveries-table">
            <thead>
                <tr>
                    <th style="width: 55px; text-align: center;">No</th>
                    <th>No Order</th>
                    <th>Penugasan Kurir</th>
                    <th>Nama Pelanggan</th>
                    <th>Telepon</th>
                    <th>Alamat Pengiriman</th>
                    <th>Total Pesanan</th>
                    <th>Status Pengiriman</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $globalIdx => $order)
                @php
                    $addrLower = strtolower($order->delivery_address ?? '');
                    if (str_contains($addrLower, 'cimahi')) {
                        $courierCode = 'kurir-a';
                        $courierBadge = '<span style="background: #e0f2fe; color: #0369a1; padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 12px; border: 1px solid #bae6fd; display: inline-flex; align-items: center; gap: 4px;">🅰️ Kurir A (Cimahi)</span>';
                    } elseif (str_contains($addrLower, 'bandung')) {
                        $courierCode = 'kurir-b';
                        $courierBadge = '<span style="background: #fef3c7; color: #b45309; padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 12px; border: 1px solid #fde047; display: inline-flex; align-items: center; gap: 4px;">🅱️ Kurir B (Bandung)</span>';
                    } else {
                        $courierCode = 'other';
                        $courierBadge = '<span style="background: #f1f5f9; color: #475569; padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 12px; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; gap: 4px;">📍 Kurir Umum</span>';
                    }
                @endphp
                <tr class="delivery-row" data-courier="{{ $courierCode }}">
                    <td style="text-align: center; font-weight: bold;">
                        <span class="row-seq-num" style="display: inline-block;">{{ $globalIdx + 1 }}</span>
                    </td>
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>{!! $courierBadge !!}</td>
                    <td style="font-weight: 600;">{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td style="max-width: 250px; text-align: left; font-size: 13px;">{{ $order->delivery_address }}</td>
                    <td style="font-weight: bold; color: #166534;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>
                        @if($order->status == 'Pengembalian' || $order->tracking_status == 'returned' || !empty($order->return_reason))
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 12px; border: 1px solid #fecaca;">🔄 Pengembalian</span>
                        @elseif($order->status == 'Menunggu Kurir')
                            <span style="background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 12px;">⏳ Menunggu Kurir</span>
                        @elseif($order->status == 'Diambil Kurir')
                            <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 12px;">🎒 Diambil Kurir</span>
                        @elseif($order->status == 'Dalam Perjalanan')
                            <span style="background: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 12px;">🛵 Dalam Perjalanan</span>
                        @else
                            <span style="background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 12px;">{{ $order->status }}</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('courier.deliveries.show', $order->id) }}" class="btn btn-success" style="padding: 6px 14px; font-size: 13px; font-weight: bold;">
                            Detail & Process
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #888; padding: 30px;">
                        Belum ada tugas pengiriman saat ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function switchCourierTab(courierType) {
    const btns = document.querySelectorAll('.courier-tab-btn');
    btns.forEach(btn => {
        btn.style.background = '#ffffff';
        btn.style.color = '#1e293b';
        btn.style.borderColor = '#cbd5e1';
    });

    const activeBtn = document.getElementById('tab-btn-' + courierType);
    if (activeBtn) {
        activeBtn.style.background = '#15803d';
        activeBtn.style.color = '#ffffff';
        activeBtn.style.borderColor = '#15803d';
    }

    const rows = document.querySelectorAll('.delivery-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const rowCourier = row.getAttribute('data-courier');
        if (courierType === 'all' || rowCourier === courierType) {
            row.style.display = '';
            visibleCount++;
            const seqElem = row.querySelector('.row-seq-num');
            if (seqElem) {
                if (visibleCount === 1) {
                    seqElem.innerHTML = '<span style="background: #166534; color: white; border-radius: 50%; width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;" title="Prioritas Pengiriman Pertama">1</span>';
                } else {
                    seqElem.textContent = visibleCount;
                }
            }
        } else {
            row.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    switchCourierTab('all');
});
</script>
@endsection
