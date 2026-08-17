@extends('admin.layout')

@section('content')
<div class="admin-card">
    <div class="admin-header">
        <h1 class="admin-title">🛵 Panel Kurir - Dashboard</h1>
        <a href="{{ route('courier.deliveries.index') }}" class="btn btn-success">📦 Lihat Tugas Pengiriman</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; border: 1px solid #c3e6cb;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div style="background: #fff8e1; border: 2px solid #ffe082; padding: 20px; border-radius: 12px; text-align: center;">
            <div style="font-size: 36px; font-weight: bold; color: #f57f17;">{{ $pending }}</div>
            <div style="font-weight: bold; color: #f57f17; margin-top: 5px;">⏳ Menunggu Kurir</div>
        </div>
        <div style="background: #e3f2fd; border: 2px solid #90caf9; padding: 20px; border-radius: 12px; text-align: center;">
            <div style="font-size: 36px; font-weight: bold; color: #1976d2;">{{ $delivering }}</div>
            <div style="font-weight: bold; color: #1976d2; margin-top: 5px;">🛵 Sedang Dikirim</div>
        </div>
        <div style="background: #e8f5e9; border: 2px solid #a5d6a7; padding: 20px; border-radius: 12px; text-align: center;">
            <div style="font-size: 36px; font-weight: bold; color: #2e7d32;">{{ $delivered }}</div>
            <div style="font-weight: bold; color: #2e7d32; margin-top: 5px;">✅ Total Selesai</div>
        </div>
        <div style="background: #f3e5f5; border: 2px solid #ce93d8; padding: 20px; border-radius: 12px; text-align: center;">
            <div style="font-size: 36px; font-weight: bold; color: #7b1fa2;">{{ $totalToday }}</div>
            <div style="font-weight: bold; color: #7b1fa2; margin-top: 5px;">📅 Selesai Hari Ini</div>
        </div>
    </div>

    <div style="display: flex; gap: 15px;">
        <a href="{{ route('courier.deliveries.index') }}" class="btn btn-success">📦 Daftar Tugas Aktif</a>
        <a href="{{ route('courier.history') }}" class="btn btn-warning">📜 Riwayat Pengiriman</a>
    </div>
</div>
@endsection
