@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 120px; padding-bottom: 60px; min-height: 85vh; font-family: 'Arial', sans-serif; max-width: 700px; margin: 0 auto;">

    <a href="/customer/dashboard" style="display: inline-flex; align-items: center; gap: 6px; color: #2e7d32; text-decoration: none; font-weight: bold; font-size: 14px; margin-bottom: 24px;">
        ← Kembali ke Dashboard
    </a>

    <div style="background: white; padding: 35px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
        <div style="text-align: center; margin-bottom: 30px;">
            <span style="font-size: 48px; display: block; margin-bottom: 12px;">⭐</span>
            <h2 style="margin: 0 0 8px 0; color: #1f2937; font-size: 22px; font-weight: bold;">Beri Rating & Ulasan</h2>
            <p style="margin: 0; color: #6b7280; font-size: 14px;">Pesanan <strong style="color: #2e7d32;">{{ $order->order_number }}</strong></p>
        </div>

        <form action="/customer/orders/{{ $order->id }}/rate" method="POST">
            @csrf

            @foreach($order->items as $item)
                @php
                    $productName = $item->product->name ?? $item->product->item_name ?? 'Produk';
                    $existingRating = $existingRatings[$item->id] ?? 0;
                    $existingReview = $existingReviews[$item->id] ?? '';
                @endphp

                <div style="background: #f9fafb; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #f3f4f6; transition: all 0.2s;" onmouseover="this.style.borderColor='#2e7d32'; this.style.boxShadow='0 2px 8px rgba(46,125,50,0.08)'" onmouseout="this.style.borderColor='#f3f4f6'; this.style.boxShadow='none'">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <h4 style="margin: 0; color: #1f2937; font-size: 15px; font-weight: 600;">{{ $productName }}</h4>
                            <span style="font-size: 13px; color: #6b7280;">{{ $item->qty }} pcs × Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Star Rating -->
                    <div style="margin-bottom: 14px;">
                        <label style="font-size: 13px; color: #4b5563; font-weight: 600; display: block; margin-bottom: 8px;">Rating:</label>
                        <div class="star-rating-group" data-item-id="{{ $item->id }}" style="display: flex; gap: 6px;">
                            @for($s = 1; $s <= 5; $s++)
                                <span class="star-btn" data-value="{{ $s }}" onclick="setRating({{ $item->id }}, {{ $s }})"
                                    style="font-size: 30px; cursor: pointer; transition: all 0.15s; {{ $existingRating >= $s ? '' : 'filter: grayscale(1); opacity: 0.4;' }}"
                                    onmouseover="hoverRating({{ $item->id }}, {{ $s }})"
                                    onmouseout="unhoverRating({{ $item->id }})">⭐</span>
                            @endfor
                        </div>
                        <input type="hidden" name="ratings[{{ $item->id }}]" id="rating-input-{{ $item->id }}" value="{{ $existingRating }}">
                    </div>

                    <!-- Review Text -->
                    <div>
                        <label style="font-size: 13px; color: #4b5563; font-weight: 600; display: block; margin-bottom: 6px;">Ulasan (opsional):</label>
                        <textarea name="reviews[{{ $item->id }}]" rows="3" placeholder="Tulis ulasan Anda tentang produk ini..."
                            style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; resize: vertical; outline: none; font-family: inherit; box-sizing: border-box; transition: border 0.2s;"
                            onfocus="this.style.borderColor='#2e7d32'" onblur="this.style.borderColor='#e5e7eb'">{{ $existingReview }}</textarea>
                    </div>
                </div>
            @endforeach

            <button type="submit" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #2e7d32, #4caf50); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);"
                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(46, 125, 50, 0.3)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(46, 125, 50, 0.2)'">
                💾 Simpan Rating
            </button>
        </form>
    </div>
</div>

<script>
    const ratingState = {};

    function setRating(itemId, value) {
        ratingState[itemId] = value;
        document.getElementById('rating-input-' + itemId).value = value;
        renderStars(itemId, value);
    }

    function hoverRating(itemId, value) {
        renderStars(itemId, value, true);
    }

    function unhoverRating(itemId) {
        const currentVal = ratingState[itemId] || parseInt(document.getElementById('rating-input-' + itemId).value) || 0;
        renderStars(itemId, currentVal);
    }

    function renderStars(itemId, activeCount, isHover) {
        const group = document.querySelector('.star-rating-group[data-item-id="' + itemId + '"]');
        if (!group) return;
        const stars = group.querySelectorAll('.star-btn');
        stars.forEach((star, index) => {
            if (index < activeCount) {
                star.style.filter = 'none';
                star.style.opacity = '1';
                star.style.transform = isHover ? 'scale(1.15)' : 'scale(1)';
            } else {
                star.style.filter = 'grayscale(1)';
                star.style.opacity = '0.4';
                star.style.transform = 'scale(1)';
            }
        });
    }

    // Initialize existing ratings
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.star-rating-group').forEach(group => {
            const itemId = group.dataset.itemId;
            const input = document.getElementById('rating-input-' + itemId);
            if (input && parseInt(input.value) > 0) {
                ratingState[itemId] = parseInt(input.value);
            }
        });
    });
</script>

<style>
    .star-btn:hover {
        transform: scale(1.2) !important;
    }
</style>
@endsection
