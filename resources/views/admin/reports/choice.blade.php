@extends('layouts.app')

@section('content')
<style>
    /* Modern Dashboard Styling */
    .admin-title {
        font-weight: 800;
        font-size: 2.2rem;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    .choice-container { 
        max-width: 1000px; 
        margin: 40px auto; 
        padding: 0 24px; 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .report-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .report-subtitle { 
        color: #6b7280; 
        font-size: 1.1rem;
        margin: 5px 0;
    }
    .choice-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
        gap: 30px; 
        margin-top: 20px; 
    }
    .choice-card { 
        background: #ffffff; 
        border-radius: 20px; 
        padding: 32px; 
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.01); 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(229, 231, 235, 0.5);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .choice-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: linear-gradient(90deg, #10b981, #3b82f6);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .choice-card:hover { 
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    }
    .choice-card:hover::before {
        opacity: 1;
    }
    .choice-card h3 { 
        margin-top: 0; 
        color: #111827; 
        font-size: 1.4rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .icon-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f3f4f6;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .choice-card.store .icon-wrapper { background: #dcfce7; color: #166534; }
    .choice-card.supplier .icon-wrapper { background: #fef9c3; color: #854d0e; }
    
    .choice-card p { 
        color: #4b5563; 
        line-height: 1.6;
        margin: 20px 0 30px;
        flex-grow: 1;
        font-size: 1.05rem;
    }
    .btn-custom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .btn-store {
        background: #10b981;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
    }
    .btn-store:hover {
        background: #059669;
        color: white;
        text-decoration: none;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
    }
    .btn-supplier {
        background: #f59e0b;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);
    }
    .btn-supplier:hover {
        background: #d97706;
        color: white;
        text-decoration: none;
        box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
    }
</style>

<div class="choice-container">
    <div class="report-header">
        <h1 class="admin-title">Pilih Jenis Laporan</h1>
        <p class="report-subtitle">Dapatkan wawasan lengkap mengenai performa bisnis Anda.</p>
        <div style="display: inline-block; margin-top: 15px; padding: 8px 20px; background: #f3f4f6; border-radius: 20px; color: #374151; font-weight: 600; font-size: 0.95rem; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);">
            📅 {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    <div class="choice-grid">
        <div class="choice-card store">
            <h3><span class="icon-wrapper">🏪</span> Laporan Toko</h3>
            <p>Tinjau ringkasan penjualan toko, daftar produk yang paling laku, dan total omzet keseluruhan secara rinci dan akurat.</p>
            <a href="/admin/reports/store" class="btn-custom btn-store">Buka Laporan Toko →</a>
        </div>

        <div class="choice-card supplier">
            <h3><span class="icon-wrapper">🏭</span> Laporan Supplier</h3>
            <p>Pantau laporan penjualan per supplier beserta otomatisasi pembagian hasil dengan persentase bagi hasil transparan.</p>
            <a href="/admin/reports/suppliers" class="btn-custom btn-supplier">Buka Laporan Supplier →</a>
        </div>
    </div>
</div>
@endsection
