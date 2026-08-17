@extends('layouts.app')

@section('content')
<div class="admin-container">
    <h1 class="admin-title">⭐ Rating & Ulasan Pelanggan</h1>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 24px; border-radius: 14px; text-align: center; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);">
            <div style="font-size: 32px; font-weight: bold;">{{ $totalRatings }}</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 4px;">Total Rating</div>
        </div>
        <div style="background: linear-gradient(135deg, #2e7d32, #4caf50); color: white; padding: 24px; border-radius: 14px; text-align: center; box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);">
            <div style="font-size: 32px; font-weight: bold;">{{ $avgRating ? number_format($avgRating, 1) : '-' }}</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 4px;">Rata-rata Rating</div>
        </div>
        <div style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; padding: 24px; border-radius: 14px; text-align: center; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);">
            <div style="font-size: 32px; font-weight: bold;">{{ $productStats->count() }}</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 4px;">Produk Dinilai</div>
        </div>
    </div>

    @if($ratings->isEmpty())
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px dashed #d1d5db;">
            <span style="font-size: 40px; display: block; margin-bottom: 16px;">📝</span>
            <h4 style="color: #4b5563; font-weight: 700;">Belum Ada Rating</h4>
            <p style="color: #6b7280; font-size: 14px;">Belum ada pelanggan yang memberikan rating untuk produk Anda.</p>
        </div>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>No. Pesanan</th>
                    <th style="text-align: center;">Rating</th>
                    <th>Ulasan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ratings as $r)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #1f2937;">{{ $r->user->name ?? 'Anonim' }}</div>
                            <div style="font-size: 12px; color: #9ca3af;">{{ $r->user->email ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #374151;">{{ $r->product->name ?? $r->product->item_name ?? 'Produk #' . $r->product_id }}</div>
                            @if(isset($productStats[$r->product_id]))
                                <div style="font-size: 12px; color: #6b7280;">
                                    Avg: {{ number_format($productStats[$r->product_id]->avg_rating, 1) }}⭐ ({{ $productStats[$r->product_id]->total }} ulasan)
                                </div>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #2e7d32;">{{ $r->order->order_number ?? '-' }}</span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 2px;">
                                @for($s = 1; $s <= 5; $s++)
                                    <span style="font-size: 16px; {{ $s <= $r->rating ? '' : 'filter: grayscale(1); opacity: 0.3;' }}">⭐</span>
                                @endfor
                            </div>
                            <div style="font-size: 12px; font-weight: bold; color: #374151; margin-top: 2px;">{{ $r->rating }}/5</div>
                        </td>
                        <td>
                            @if($r->review)
                                <div style="max-width: 250px; font-size: 13px; color: #4b5563; font-style: italic; line-height: 1.5;">
                                    "{{ Str::limit($r->review, 100) }}"
                                </div>
                            @else
                                <span style="color: #9ca3af; font-size: 13px;">Tanpa ulasan</span>
                            @endif
                        </td>
                        <td style="font-size: 13px; color: #6b7280;">
                            {{ $r->created_at->translatedFormat('d M Y') }}<br>
                            <span style="font-size: 12px; color: #9ca3af;">{{ $r->created_at->translatedFormat('H:i') }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
