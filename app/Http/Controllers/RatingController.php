<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Rating;

class RatingController extends Controller
{
    /**
     * Show the rating form for an order
     */
    public function create($orderId)
    {
        $user = Auth::user();
        $order = Order::with('items.product')
            ->where('id', $orderId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Check that order is completed
        if ($order->tracking_status !== 'completed') {
            return redirect('/customer/dashboard')->with('error', 'Pesanan belum selesai, tidak bisa memberi rating.');
        }

        // Get existing ratings for this order
        $existingRatings = Rating::where('order_id', $order->id)
            ->where('user_id', $user->id)
            ->pluck('rating', 'order_item_id')
            ->toArray();

        $existingReviews = Rating::where('order_id', $order->id)
            ->where('user_id', $user->id)
            ->pluck('review', 'order_item_id')
            ->toArray();

        return view('customer.rate', compact('order', 'existingRatings', 'existingReviews'));
    }

    /**
     * Store ratings for an order
     */
    public function store(Request $request, $orderId)
    {
        $user = Auth::user();
        $order = Order::with('items')
            ->where('id', $orderId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($order->tracking_status !== 'completed') {
            return redirect('/customer/dashboard')->with('error', 'Pesanan belum selesai.');
        }

        $ratings = $request->input('ratings', []);
        $reviews = $request->input('reviews', []);

        foreach ($order->items as $item) {
            $itemId = $item->id;
            if (isset($ratings[$itemId]) && $ratings[$itemId] >= 1 && $ratings[$itemId] <= 5) {
                Rating::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'order_item_id' => $itemId,
                    ],
                    [
                        'product_id' => $item->product_id,
                        'user_id' => $user->id,
                        'rating' => (int) $ratings[$itemId],
                        'review' => $reviews[$itemId] ?? null,
                    ]
                );
            }
        }

        return redirect('/customer/dashboard')->with('success', 'Terima kasih! Rating Anda berhasil disimpan.');
    }

    /**
     * Admin: view all ratings
     */
    public function adminIndex()
    {
        $ratings = Rating::with(['user', 'product', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();

        $avgRating = Rating::avg('rating');
        $totalRatings = Rating::count();

        // Per-product stats
        $productStats = Rating::selectRaw('product_id, AVG(rating) as avg_rating, COUNT(*) as total')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        return view('admin.ratings.index', compact('ratings', 'avgRating', 'totalRatings', 'productStats'));
    }
}
