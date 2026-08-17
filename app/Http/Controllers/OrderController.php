<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use App\Models\Order;
use App\Models\SupplierStock;
use App\Models\OrderItem;
use App\Models\Supplier;
use Midtrans\Config;
use Midtrans\Snap;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public static function parseWeightToGrams($weightValue)
    {
        if ($weightValue === null || $weightValue === '') {
            return 0;
        }

        $raw = trim((string) $weightValue);
        if ($raw === '') {
            return 0;
        }

        $lower = strtolower($raw);

        if (preg_match('/([0-9]+(?:\.[0-9]+)?)\s*(kg|kilogram|kilograms|kilo|kilos)/', $lower, $matches)) {
            return (int) round((float) $matches[1] * 1000);
        }

        if (preg_match('/([0-9]+(?:\.[0-9]+)?)\s*(g|gr|gram|grams)/', $lower, $matches)) {
            return (int) round((float) $matches[1]);
        }

        return (int) round((float) $raw);
    }

    public static function calculateDeliveryCost($deliveryOption, $totalWeight, $deliveryCity = '', $deliveryDistanceKm = 0, $courier = '', $destinationZone = '')
    {
        $optLower = strtolower((string)$deliveryOption);
        if ($optLower === 'expedition' || strpos($optLower, 'expedition') !== false || strpos($optLower, 'j&t') !== false || strpos($optLower, 'jne') !== false || strpos($optLower, 'pos') !== false) {
            $weightInKg = (int) ceil(max(1, (float)$totalWeight) / 1000.0);

            $courierKey = strtolower((string)$courier);
            if (empty($courierKey)) {
                if (strpos($optLower, 'jne') !== false) $courierKey = 'jne';
                else if (strpos($optLower, 'pos') !== false) $courierKey = 'pos';
                else $courierKey = 'jnt';
            }

            $zoneKey = strtolower((string)$destinationZone);
            if (empty($zoneKey)) {
                if (strpos($optLower, 'luar_pulau') !== false || strpos($optLower, 'luar pulau') !== false) {
                    $zoneKey = 'luar_pulau_jawa';
                } else {
                    $zoneKey = 'luar_kota_jawa';
                }
            }

            $rates = [
                'jnt' => [
                    'luar_kota_jawa' => 18000,
                    'luar_pulau_jawa' => 35000,
                ],
                'jne' => [
                    'luar_kota_jawa' => 20000,
                    'luar_pulau_jawa' => 40000,
                ],
                'pos' => [
                    'luar_kota_jawa' => 16000,
                    'luar_pulau_jawa' => 32000,
                ],
            ];

            $rate = $rates[$courierKey][$zoneKey] ?? 18000;
            return $weightInKg * $rate;
        }

        if ($deliveryOption !== 'delivery') {
            return 0;
        }

        $distanceKm = (float) $deliveryDistanceKm;
        if ($distanceKm <= 0) {
            $distanceKm = 1.0;
        }

        return (int) (ceil($distanceKm / 5.0) * 10000);
    }

    public static function isLocalDeliveryArea(?string $city, ?string $address): bool
    {
        $combined = strtolower(trim(($city ?? '') . ' ' . ($address ?? '')));

        if (empty($combined)) {
            return true;
        }

        $outerPatterns = [
            'bandung barat', 'kabupaten bandung', 'kab. bandung', 'kbb',
            'cianjur', 'garut', 'sumedang', 'subang', 'purwakarta', 'tasikmalaya',
            'ciamis', 'majalengka', 'cirebon', 'kuningan', 'indramayu', 'sukabumi',
            'bogor', 'depok', 'bekasi', 'jakarta', 'tangerang', 'serang', 'karawang'
        ];

        foreach ($outerPatterns as $pattern) {
            if (str_contains($combined, $pattern)) {
                return false;
            }
        }

        if (str_contains($combined, 'cimahi') || str_contains($combined, 'bandung')) {
            return true;
        }

        return false;
    }

    public function checkout(Request $request)
    {
        $rawDeliveryOption = $request->delivery_option;
        if ($rawDeliveryOption === 'delivery' && !self::isLocalDeliveryArea($request->delivery_city, $request->delivery_address)) {
            return response()->json([
                'success' => false,
                'message' => 'Kurir Toko hanya melayani pengiriman untuk wilayah Kota Cimahi dan Kota Bandung. Silakan pilih Ekspedisi Pihak Ketiga.'
            ], 400);
        }

        foreach ($request->items ?? [] as $item) {
            $product = \App\Models\SupplierStock::find($item['id'] ?? null);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.'
                ], 400);
            }
            
            $targetWeight = $item['weight'] ?? '';
            $variants = $product->variants ?? [];
            $variant = null;
            $targetGrams = self::parseWeightToGrams($targetWeight);
            foreach ($variants as $v) {
                if (self::parseWeightToGrams($v['weight'] ?? '') === $targetGrams) {
                    $variant = $v;
                    break;
                }
            }
            
            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Varian ukuran untuk produk ' . $product->name . ' tidak ditemukan.'
                ], 400);
            }
            
            $isExpired = false;
            if (!empty($variant['expiry_date'])) {
                $expiry = \Carbon\Carbon::parse($variant['expiry_date']);
                if ($expiry->isPast()) {
                    $isExpired = true;
                }
            }
            if ($isExpired) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk ' . $product->name . ' (' . $variant['weight'] . ') sudah kadaluarsa.'
                ], 400);
            }
            
            if (($variant['available_quantity'] ?? 0) < (int) ($item['quantity'] ?? 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok produk ' . $product->name . ' (' . $variant['weight'] . ') tidak mencukupi.'
                ], 400);
            }
        }
        
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderNumber = 'ORD-' . time();

        $totalWeight = 0;
        foreach ($request->items ?? [] as $item) {
            $itemWeight = self::parseWeightToGrams($item['weight'] ?? 0);
            $totalWeight += $itemWeight * (int) ($item['quantity'] ?? 0);
        }

        $rawDeliveryOption = $request->delivery_option;
        $courier = $request->courier ?? '';
        $destinationZone = $request->destination_zone ?? '';

        $deliveryCost = (int) ($request->delivery_cost ?? self::calculateDeliveryCost(
            $rawDeliveryOption,
            $totalWeight,
            $request->delivery_city,
            $request->delivery_distance_km,
            $courier,
            $destinationZone
        ));

        $deliveryOptionLabel = $rawDeliveryOption;
        if ($rawDeliveryOption === 'expedition' || strpos(strtolower($rawDeliveryOption), 'expedition') !== false) {
            $courierNameMap = [
                'jnt' => 'J&T Express',
                'jne' => 'JNE (Reguler)',
                'pos' => 'POS Indonesia'
            ];
            $zoneNameMap = [
                'luar_kota_jawa' => 'Luar Kota (Pulau Jawa)',
                'luar_pulau_jawa' => 'Luar Pulau Jawa'
            ];
            $cName = $courierNameMap[strtolower($courier)] ?? 'Pihak Ketiga';
            $zName = $zoneNameMap[strtolower($destinationZone)] ?? '';
            $deliveryOptionLabel = 'Ekspedisi ' . $cName . ($zName ? ' (' . $zName . ')' : '');
        } elseif ($rawDeliveryOption === 'delivery') {
            $deliveryOptionLabel = 'Kurir Toko (Lokal)';
        } elseif ($rawDeliveryOption === 'pickup') {
            $deliveryOptionLabel = 'Ambil di Toko';
        }

        $paymentMethod = $request->payment_method ?? 'midtrans';

        $order = Order::create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'order_number' => $orderNumber,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'delivery_option' => $deliveryOptionLabel,
            'delivery_address' => $request->delivery_address,
            'delivery_cost' => $deliveryCost,
            'notes' => $request->notes,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentMethod === 'transfer_bank' ? 'pending_verification' : 'pending',
            'midtrans_order_id' => $orderNumber
        ]);

        if (auth()->check()) {
            $user = auth()->user();
            $updated = false;
            if (empty($user->phone) && !empty($request->customer_phone)) {
                $user->phone = $request->customer_phone;
                $updated = true;
            }
            if (empty($user->address) && !empty($request->delivery_address)) {
                $user->address = $request->delivery_address;
                $updated = true;
            }
            if ($updated) {
                $user->save();
            }
        }
        foreach($request->items as $item){
            $product = \App\Models\SupplierStock::find($item['id'] ?? null);
            $buyPrice = 0;
            if ($product) {
                $targetWeight = $item['weight'] ?? '';
                $variants = $product->variants ?? [];
                $targetGrams = self::parseWeightToGrams($targetWeight);
                foreach ($variants as $v) {
                    if (self::parseWeightToGrams($v['weight'] ?? '') === $targetGrams) {
                        $buyPrice = (float) ($v['buy_price'] ?? 0);
                        break;
                    }
                }
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'qty' => $item['quantity'],
                'price' => $item['price'],
                'buy_price' => $buyPrice,
                'subtotal' => $item['price']*$item['quantity'],
                'weight' => $item['weight'] ?? ''
            ]);
        }

        if ($paymentMethod === 'transfer_bank') {
            return response()->json([
                'success' => true,
                'payment_method' => 'transfer_bank',
                'order_id' => $orderNumber,
                'order_db_id' => $order->id,
                'bank_name' => 'Bank BCA',
                'bank_account' => '123-456-7890',
                'account_holder' => 'Toko Dewi Lestari 2',
                'total_amount' => $order->total_amount,
                'message' => 'Pesanan berhasil dibuat! Silakan transfer ke BCA 123-456-7890 dan konfirmasi pembayaran.'
            ]);
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderNumber,
                'gross_amount' => (int) $request->total_amount
            ],
            'enabled_payments' => [
                'qris',
                'gopay',
                'bca_va',
                'bri_va'
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'phone' => $request->customer_phone
            ]
        ];

        $snapToken = Snap::getSnapToken($params);
        $order->update(['snap_token' => $snapToken]);

        $redirectUrl = 'https://app.sandbox.midtrans.com/snap/v4/redirection/' . $snapToken;

        return response()->json([
            'success' => true,
            'payment_method' => 'midtrans',
            'snap_token' => $snapToken,
            'redirect_url' => $redirectUrl,
            'order_id' => $orderNumber
        ]);
    }

    public function uploadTransferProof(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $proofPath = null;

        if ($request->hasFile('proof_image')) {
            $request->validate([
                'proof_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072'
            ]);
            $file = $request->file('proof_image');
            $filename = time() . '_tf_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/transfer_proofs'), $filename);
            $proofPath = 'uploads/transfer_proofs/' . $filename;
        } elseif ($request->boolean('is_simulation') || $request->input('is_simulation') == '1' || $request->input('is_simulation') === true) {
            $proofPath = 'uploads/transfer_proofs/simulated_bca_' . time() . '.png';
            if (!file_exists(public_path('uploads/transfer_proofs'))) {
                mkdir(public_path('uploads/transfer_proofs'), 0777, true);
            }
            if (!file_exists(public_path($proofPath))) {
                copy(public_path('favicon.ico'), public_path($proofPath));
            }
        }

        if ($proofPath) {
            $order->transfer_proof = $proofPath;
            $order->payment_status = 'pending_verification';
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Bukti transfer berhasil dikirim! Menunggu verifikasi dari kasir.',
                'proof_path' => asset($proofPath)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Silakan unggah foto bukti transfer.'
        ], 400);
    }

    public function verifyBankPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $action = $request->input('action', 'approve');

        if ($action === 'approve') {
            $order->payment_status = 'paid';
            $order->paid_at = Carbon::now();
            $order->payment_verified_at = Carbon::now();
            $order->payment_verified_by = auth()->check() ? auth()->id() : null;

            if (!$order->tracking_ticket_id) {
                $order->tracking_ticket_id = 'TKT-' . strtoupper(substr(md5($order->id . time()), 0, 8));
            }
            if (!$order->tracking_status) {
                $order->tracking_status = 'preparing';
            }

            $isPickup = (empty($order->delivery_address) || str_contains(strtolower($order->delivery_option ?? ''), 'ambil di toko') || str_contains(strtolower($order->delivery_option ?? ''), 'pickup'));
            if ($isPickup) {
                $order->status = 'Ambil di Toko';
            } else {
                $order->status = 'Menunggu Kurir';
            }

            foreach ($order->items as $item) {
                $product = \App\Models\SupplierStock::find($item->product_id);
                if ($product) {
                    $targetWeight = $item->weight ?? '';
                    $targetGrams = self::parseWeightToGrams($targetWeight);
                    $variants = $product->variants ?? [];
                    if (!is_array($variants)) {
                        $variants = json_decode($variants, true) ?: [];
                    }

                    $matchedIndex = -1;
                    foreach ($variants as $idx => $v) {
                        if (self::parseWeightToGrams($v['weight'] ?? '') === $targetGrams) {
                            $matchedIndex = $idx;
                            break;
                        }
                    }

                    if ($matchedIndex >= 0) {
                        $curAvail = (int) ($variants[$matchedIndex]['available_quantity'] ?? 0);
                        $newAvail = max(0, $curAvail - (int) $item->qty);
                        $variants[$matchedIndex]['available_quantity'] = $newAvail;
                        $product->variants = $variants;
                        $product->save();

                        if ($newAvail <= 0) {
                            SupplierStockController::autoProcessBatchQueue($product->item_name, $targetWeight);
                        }
                    }
                }
            }

            $order->save();

            return redirect()->back()->with('success', 'Pembayaran Transfer Bank #' . $order->order_number . ' berhasil diverifikasi & disetujui kasir!');
        } else {
            $order->payment_status = 'failed';
            $order->notes = ($order->notes ? $order->notes . ' | ' : '') . 'Ditolak kasir: ' . $request->input('reason', 'Bukti transfer tidak valid');
            $order->save();

            return redirect()->back()->with('success', 'Pembayaran Transfer Bank #' . $order->order_number . ' telah ditolak.');
        }
    }

    public function checkPendingOrder(Request $request)
    {
        $pendingOrder = null;

        // Check by order_id from frontend (localStorage)
        $lastOrderId = $request->query('order_id');
        if ($lastOrderId) {
            $pendingOrder = Order::where('midtrans_order_id', $lastOrderId)
                ->where('payment_status', '!=', 'paid')
                ->where('created_at', '>', now()->subMinutes(15))
                ->first();
        }

        // Also check by authenticated user
        if (!$pendingOrder && auth()->check()) {
            $pendingOrder = Order::where('user_id', auth()->id())
                ->where('payment_status', '!=', 'paid')
                ->where('created_at', '>', now()->subMinutes(15))
                ->orderBy('id', 'desc')
                ->first();
        }

        if ($pendingOrder && $pendingOrder->snap_token) {
            return response()->json([
                'has_pending' => true,
                'snap_token' => $pendingOrder->snap_token,
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/' . $pendingOrder->snap_token,
                'order_id' => $pendingOrder->midtrans_order_id,
                'order_number' => $pendingOrder->order_number,
                'total_amount' => $pendingOrder->total_amount,
            ]);
        }

        return response()->json([
            'has_pending' => false
        ]);
    }

    public function cancelPendingOrder(Request $request, $orderId)
    {
        $order = Order::where('midtrans_order_id', $orderId)
            ->orWhere('order_number', $orderId)
            ->first();

        if ($order && $order->payment_status !== 'paid') {
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'cancelled'
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan atau sudah dibayar.']);
    }

    public function index()
{
    $this->cleanupOldOrders();
    $orders = Order::orderBy('id', 'desc')->get();

    return view(
        'admin.orders.index',
        compact('orders')
    );
}

private function cleanupOldOrders()
{
    // Hapus pesanan yang belum dibayar selama > 15 menit
    $unpaidExpired = Order::where('payment_status', '!=', 'paid')
        ->where('created_at', '<', now()->subMinutes(15))
        ->get();
        
    foreach($unpaidExpired as $order) {
        foreach($order->items as $item) {
            $item->delete();
        }
        $order->delete();
    }

    // Hapus semua pesanan (lunas maupun tidak) yang sudah > 3 hari
    $oldOrders = Order::where('created_at', '<', now()->subDays(3))->get();
    
    foreach($oldOrders as $order) {
        foreach($order->items as $item) {
            $item->delete();
        }
        $order->delete();
    }
}

public function checkPayment($orderId)
{
    $order = Order::where(
        'midtrans_order_id',
        $orderId
    )->first();

    if (!$order) {
        return response()->json([
            'success' => false
        ]);
    }

    try {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$curlOptions = [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => []
        ];
        
        $statusResp = \Midtrans\Transaction::status($orderId);
        
        if ($statusResp && in_array($statusResp->transaction_status, ['settlement', 'capture'])) {
            if ($order->payment_status !== 'paid') {
                $ticketId = 'TRK-' . rand(100000, 999999);
                
                $order->payment_status = 'paid';
                $order->status = 'Menunggu Kurir';
                $order->paid_at = now();
                $order->tracking_ticket_id = $ticketId;
                $order->tracking_status = 'preparing';
                $order->save();

                 // Kurangi stok
                 foreach($order->items as $item){
                     $product = \App\Models\SupplierStock::find($item->product_id);
                     if($product){
                         $product->decrementStock($item->weight, $item->qty, $order->order_number);
                     }
                 }
            }
        }
    } catch (\Exception $e) {
        \Log::error('Midtrans API Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Midtrans API Error: ' . $e->getMessage()
        ]);
    }

    return response()->json([
        'success' => true,
        'payment_status' => $order->payment_status,
        'status' => $order->status,
        'tracking_ticket_id' => $order->tracking_ticket_id,
        'snap_token' => $order->snap_token
    ]);
}
    public function callback(Request $request)
{
    $order = Order::where(
        'midtrans_order_id',
        $request->order_id
    )->first();

    if (!$order) {
        return response()->json([
            'message' => 'Order tidak ditemukan'
        ]);
    }

    if (in_array($request->transaction_status, ['settlement', 'capture'])) {
        if ($order->payment_status !== 'paid') {
            // Generate tracking ticket ID
            $ticketId = 'TRK-' . rand(100000, 999999);
            
            $order->payment_status = 'paid';
            $order->status = 'Menunggu Kurir';
            $order->paid_at = now();
            $order->tracking_ticket_id = $ticketId;
            $order->tracking_status = 'preparing';
            $order->save();

             // Kurangi stok
             foreach($order->items as $item){
                 $product = \App\Models\SupplierStock::find($item->product_id);
                 if($product){
                     $product->decrementStock($item->weight, $item->qty, $order->order_number);
                 }
             }
        }
    }

    return response()->json([
        'success' => true
    ]); 
}
public function show($id)
{
    return redirect()->route('courier.deliveries.show', $id);
}

public function destroy($id)
{
    $order = Order::findOrFail($id);
    
    // delete order items first if not cascade
    foreach($order->items as $item) {
        $item->delete();
    }
    
    $order->delete();
    
    return redirect('/admin/orders')->with('success', 'Pesanan berhasil dihapus');
}

public function simulatePayment($id)
{
    $order = Order::findOrFail($id);

    if ($order->payment_status !== 'paid') {
        $ticketId = 'TRK-' . rand(100000, 999999);
        
        $order->payment_status = 'paid';
        $order->status = 'Menunggu Kurir';
        $order->paid_at = now();
        $order->tracking_ticket_id = $ticketId;
        $order->tracking_status = 'preparing';
        $order->save();

         foreach($order->items as $item) {
             $product = \App\Models\SupplierStock::find($item->product_id);
             if($product) {
                 $product->decrementStock($item->weight, $item->qty, $order->order_number);
             }
         }
    }

    return redirect('/admin/orders')->with('success', 'Pesanan disimulasikan sebagai Paid');
}


public function trackOrder($ticketId)
{
    $order = Order::with('items.product')->where('tracking_ticket_id', $ticketId)->first();

    if (!$order) {
        // If AJAX/JSON request, return JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket pelacakan tidak ditemukan.'
            ]);
        }
        return redirect('/track')->with('error', 'Tiket pelacakan tidak ditemukan.');
    }

    $labels = [
        'pending' => 'Menunggu Pembayaran',
        'preparing' => 'Dikemas',
        'shipped' => 'Dikirim',
        'almost_arrived' => 'Hampir Sampai',
        'completed' => 'Selesai',
        'returned' => 'Pengembalian'
    ];

    $statusLabel = $labels[$order->tracking_status] ?? 'Unknown';

    // If AJAX/JSON request, return JSON (for the tracking widget on main page)
    if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
        return response()->json([
            'success' => true,
            'tracking_status' => $order->tracking_status,
            'status_label' => $statusLabel,
            'customer_name' => $order->customer_name,
            'order_number' => $order->order_number,
            'delivery_option' => $order->delivery_option,
            'estimated_delivery' => $order->estimated_delivery
        ]);
    }

    // Otherwise return a full HTML tracking page
    return view('customer.track-detail', compact('order', 'statusLabel'));
}

public function updateTrackingStatus(Request $request, $id)
{
    $request->validate([
        'delivery_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $order = Order::findOrFail($id);
    $newStatus = $request->input('tracking_status');
    $order->tracking_status = $newStatus;

    // Update timestamps based on status
    if ($newStatus === 'shipped' && !$order->shipped_at) {
        $order->shipped_at = now();
    } elseif ($newStatus === 'completed') {
        if (!$order->delivered_at) {
            $order->delivered_at = now();
        }
        $order->status = 'completed';
    }

    if ($request->has('return_status')) {
        $order->return_status = $request->input('return_status');
    }

    if ($request->filled('estimated_delivery_date')) {
        $order->estimated_delivery_date = $request->input('estimated_delivery_date');
    }

    if ($request->hasFile('delivery_proof')) {
        // Hapus file lama jika ada
        if ($order->delivery_proof && file_exists(public_path($order->delivery_proof))) {
            @unlink(public_path($order->delivery_proof));
        }

        $filename = time() . '_' . $request->file('delivery_proof')->getClientOriginalName();
        $request->file('delivery_proof')->move(
            public_path('images/delivery_proofs'),
            $filename
        );
        $order->delivery_proof = 'images/delivery_proofs/' . $filename;
    }

    $order->save();

    return redirect()->back()->with('success', 'Status pengiriman berhasil diperbarui!');
}

public function requestReturn(Request $request, $ticketId)
{
    $order = Order::where('tracking_ticket_id', $ticketId)->firstOrFail();

    $request->validate([
        'return_category' => 'required|string',
        'return_reason_details' => 'required|string|min:5',
        'return_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ], [
        'return_category.required' => 'Silakan pilih kategori alasan pengembalian.',
        'return_reason_details.required' => 'Silakan jelaskan detail kendala pengembalian.',
        'return_reason_details.min' => 'Penjelasan detail pengembalian minimal 5 karakter.',
        'return_proof.image' => 'File bukti harus berupa gambar (JPG, PNG, WEBP).',
        'return_proof.max' => 'Ukuran foto bukti tidak boleh melebihi 2MB.',
    ]);

    $fullReason = '[' . $request->input('return_category') . '] ' . trim($request->input('return_reason_details'));
    $order->return_reason = $fullReason;
    $order->return_requested_at = now();
    $order->return_status = 'pending';
    $order->tracking_status = 'returned';

    if ($request->hasFile('return_proof')) {
        if ($order->return_proof && file_exists(public_path($order->return_proof))) {
            @unlink(public_path($order->return_proof));
        }

        $filename = time() . '_retur_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $request->file('return_proof')->getClientOriginalName());
        $request->file('return_proof')->move(
            public_path('images/return_proofs'),
            $filename
        );
        $order->return_proof = 'images/return_proofs/' . $filename;
    }

    $order->save();

    return redirect()->back()->with('success', 'Pengajuan pengembalian pesanan berhasil dikirim! Tim kami akan segera meninjau permohonan Anda.');
}

public function resolveSupplierForProduct($product, $suppliers = null): string
{
    if (empty($product)) {
        return 'Tanpa Supplier';
    }

    if (is_object($product)) {
        if (isset($product->supplier) && !empty($product->supplier->name)) {
            return $product->supplier->name;
        }
        if (isset($product->supplier_id)) {
            $s = \App\Models\Supplier::find($product->supplier_id);
            if ($s) {
                return $s->name;
            }
        }
    }

    $supplierName = null;
    if (is_object($product) && method_exists($product, 'getAttribute')) {
        $supplierName = $product->getAttribute('supplier_name');
    } elseif (is_object($product) && property_exists($product, 'supplier_name')) {
        $supplierName = $product->supplier_name;
    } elseif (is_array($product) && array_key_exists('supplier_name', $product)) {
        $supplierName = $product['supplier_name'];
    }

    if (!empty($supplierName)) {
        return $supplierName;
    }

    $suppliers = $suppliers ?? Supplier::orderBy('name')->get();
    $productName = strtolower(trim(($product->name ?? '') . ' ' . ($product->description ?? '') . ' ' . ($product->category ?? '')));

    foreach ($suppliers as $supplier) {
        $items = is_array($supplier->items) ? $supplier->items : [];
        foreach ($items as $item) {
            $itemName = is_array($item) ? ($item['name'] ?? '') : (string) $item;
            if ($itemName && str_contains($productName, strtolower($itemName))) {
                return $supplier->name;
            }
        }
    }

    if (str_contains($productName, 'dodol')) {
        return 'Supplier Dodol';
    }

    if (str_contains($productName, 'kripik')) {
        return 'Supplier Kripik';
    }

    if (str_contains($productName, 'bolu')) {
        return 'Supplier Bolu';
    }

    return 'Tanpa Supplier';
}

private function getSupplierNameForProduct($product): string
{
    return $this->resolveSupplierForProduct($product);
}

private function applyReportDateFilter($query, Request $request)
{
    $month = $request->input('month');

    if ($month) {
        try {
            $date = Carbon::createFromFormat('Y-m', $month);
            return $query->whereYear('paid_at', $date->year)
                         ->whereMonth('paid_at', $date->month);
        } catch (\Exception $e) {
            // Ignore invalid month format and fallback to current month
        }
    }

    $now = Carbon::now();
    return $query->whereYear('paid_at', $now->year)
                 ->whereMonth('paid_at', $now->month);
}

public static function formatWeightLabel($weight)
{
    if (empty($weight) || $weight === '-') {
        return '';
    }
    $trimmed = trim((string)$weight);
    if (empty($trimmed) || $trimmed === '-') {
        return '';
    }

    // Match numbers like "250", "250g", "250gr", "250 gram", "250gram", "500 gr"
    if (preg_match('/^(\d+(?:\.\d+)?)\s*(g|gr|gram|grams)?$/i', $trimmed, $matches)) {
        $val = (float)$matches[1];
        $valStr = (floor($val) == $val) ? (string)(int)$val : (string)$val;
        return $valStr . ' gram';
    }

    // Match kilograms like "1 kg", "1kg", "1 kilogram"
    if (preg_match('/^(\d+(?:\.\d+)?)\s*(kg|kilogram|kilograms)?$/i', $trimmed, $matches)) {
        $val = (float)$matches[1];
        $valStr = (floor($val) == $val) ? (string)(int)$val : (string)$val;
        return $valStr . ' kg';
    }

    return $trimmed;
}

public function buildSalesReportData($orders, $reportType = 'sales', $supplierFilter = 'all')
{
    $productSales = [];
    $supplierBreakdown = [];

    try {
        $suppliers = Supplier::orderBy('name')->get();
    } catch (\Throwable $e) {
        $suppliers = collect();
    }

    foreach ($orders as $order) {
        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) {
                try {
                    if (\Schema::hasTable('products')) {
                        $product = \DB::table('products')->where('id', $item->product_id)->first();
                    }
                } catch (\Throwable $e) {
                }
            }
            $productName = $product->item_name ?? $product->name ?? 'Produk Tidak Diketahui';
            $supplierName = $this->resolveSupplierForProduct($product, $suppliers);
            $quantitySold = (int) ($item->qty ?? 0);
            $revenue = $item->subtotal ?? 0;

            if ($supplierFilter !== 'all' && $supplierName !== $supplierFilter) {
                continue;
            }

            $weight = $item->weight;
            if (empty($weight)) {
                if ($product && !empty($product->variants)) {
                    $variants = is_array($product->variants) ? $product->variants : (json_decode($product->variants, true) ?: []);
                    foreach ($variants as $v) {
                        if ((float)($v['price'] ?? 0) === (float)$item->price) {
                            $weight = $v['weight'] ?? '';
                            break;
                        }
                    }
                    if (empty($weight) && count($variants) === 1) {
                        $weight = $variants[0]['weight'] ?? '';
                    }
                }
            }

            $weight = self::formatWeightLabel($weight);
            $groupKey = $productName . '|' . ($weight ?: 'default');

            if (!isset($productSales[$groupKey])) {
                $productSales[$groupKey] = [
                    'product_id' => (int) ($item->product_id ?? 0),
                    'product_name' => $productName . ($weight ? ' (' . $weight . ')' : ''),
                    'quantity_sold' => 0,
                    'revenue' => 0,
                    'last_transaction_date' => null,
                    'transactions' => [],
                ];
            }

            $productSales[$groupKey]['quantity_sold'] += $quantitySold;
            $productSales[$groupKey]['revenue'] += $revenue;
            $transactionDate = $order->paid_at ? Carbon::parse($order->paid_at)->format('Y-m-d') : null;
            if ($transactionDate) {
                $productSales[$groupKey]['last_transaction_date'] = $transactionDate;
                $productSales[$groupKey]['transactions'][] = [
                    'date' => $transactionDate,
                    'quantity' => $quantitySold,
                    'revenue' => $revenue,
                ];
            }

            if (!isset($supplierBreakdown[$supplierName])) {
                $supplierBreakdown[$supplierName] = [
                    'supplier_name' => $supplierName,
                    'quantity_sold' => 0,
                    'total_revenue' => 0.0,
                    'shop_share' => 0,
                    'supplier_share' => 0,
                    'products' => [],
                    'expired_products' => [], // Added for expired products
                ];
            }

            $supplierBreakdown[$supplierName]['quantity_sold'] += $quantitySold;
            $supplierBreakdown[$supplierName]['total_revenue'] += $revenue;

            if (!isset($supplierBreakdown[$supplierName]['products'][$groupKey])) {
                $buyPrice = (float)($item->buy_price ?? 0);
                if ($buyPrice <= 0 && $product && !empty($product->variants)) {
                    $variants = is_array($product->variants) ? $product->variants : (json_decode($product->variants, true) ?: []);
                    foreach ($variants as $v) {
                        $vWeightFormatted = self::formatWeightLabel($v['weight'] ?? '');
                        if ($vWeightFormatted === $weight || (float)($v['price'] ?? 0) === (float)$item->price) {
                            $buyPrice = (float)($v['buy_price'] ?? 0);
                            break;
                        }
                    }
                }
                $supplierBreakdown[$supplierName]['products'][$groupKey] = [
                    'product_name' => $productName,
                    'weight' => $weight,
                    'quantity_sold' => 0,
                    'revenue' => 0,
                    'buy_price' => $buyPrice,
                    'last_transaction_date' => null,
                ];
            }

            $supplierBreakdown[$supplierName]['products'][$groupKey]['quantity_sold'] += $quantitySold;
            $supplierBreakdown[$supplierName]['products'][$groupKey]['revenue'] += $revenue;
            $supplierBreakdown[$supplierName]['products'][$groupKey]['last_transaction_date'] = $order->paid_at ? Carbon::parse($order->paid_at)->format('Y-m-d') : null;
        }
    }

    $products = collect(array_values($productSales))
        ->sortByDesc('quantity_sold')
        ->values();

    $bestSeller = $products->first() ?: [
        'product_id' => 0,
        'product_name' => 'Belum ada penjualan',
        'quantity_sold' => 0,
        'revenue' => 0,
    ];

    // Fetch all active supplier stocks to resolve prices & current available stock
    try {
        $allSupplierStocks = \App\Models\SupplierStock::with('supplier')->where('is_active', 1)->get();
    } catch (\Throwable $e) {
        $allSupplierStocks = collect();
    }

    $supplierSummary = collect(array_values($supplierBreakdown))
        ->map(function ($supplier) use ($allSupplierStocks) {
            $totalModalSupplier = 0;
            $totalNilaiJual = 0;
            $totalKeuntunganToko = 0;
            $totalBayarSupplier = 0;

            $supplierProducts = collect($supplier['products'])
                ->sortByDesc('quantity_sold')
                ->values()
                ->all();

            $matchedStocks = $allSupplierStocks->filter(function($st) use ($supplier) {
                return strcasecmp($st->supplier->name ?? '', $supplier['supplier_name']) === 0;
            });

            foreach ($supplierProducts as &$p) {
                $weight = self::formatWeightLabel($p['weight'] ?? '');
                $p['weight'] = $weight;
                $pName = $p['product_name'] ?? '';

                $hargaTitip = (float)($p['buy_price'] ?? 0);
                $hargaJual = $p['quantity_sold'] > 0 ? (float)($p['revenue'] / $p['quantity_sold']) : 0;
                $sisaStok = 0;

                foreach ($matchedStocks as $st) {
                    if (strcasecmp($st->item_name, $pName) === 0 || strcasecmp($st->name, $pName) === 0) {
                        $variants = $st->variants;
                        if (!is_array($variants)) {
                            $variants = json_decode($variants, true) ?: [];
                        }
                        foreach ($variants as $v) {
                            $vWeight = self::formatWeightLabel($v['weight'] ?? '');
                            if ($vWeight === $weight || empty($weight) || count($variants) === 1) {
                                if (empty($p['weight'])) {
                                    $p['weight'] = $vWeight;
                                }
                                if ($hargaTitip <= 0 && isset($v['buy_price'])) {
                                    $hargaTitip = (float)$v['buy_price'];
                                }
                                if ($hargaJual <= 0 && isset($v['price'])) {
                                    $hargaJual = (float)$v['price'];
                                }
                                $sisaStok = (int)($v['available_quantity'] ?? 0);
                                break;
                            }
                        }
                    }
                }

                $p['weight'] = self::formatWeightLabel($p['weight'] ?? '');

                $terjual = (int)$p['quantity_sold'];
                $bayarSupplier = $terjual * $hargaTitip;
                $nilaiJual = $terjual * $hargaJual;
                $keuntungan = $nilaiJual - $bayarSupplier;

                $p['harga_titip'] = $hargaTitip;
                $p['harga_jual'] = $hargaJual;
                $p['sisa_stok'] = $sisaStok;
                $p['keuntungan'] = $keuntungan;
                $p['bayar_supplier'] = $bayarSupplier;
                $p['total_nilai_jual'] = $nilaiJual;

                $totalModalSupplier += $bayarSupplier;
                $totalNilaiJual += $nilaiJual;
                $totalKeuntunganToko += $keuntungan;
                $totalBayarSupplier += $bayarSupplier;
            }
            unset($p);

            $supplier['products'] = $supplierProducts;
            $supplier['total_modal_supplier'] = $totalModalSupplier;
            $supplier['total_nilai_jual'] = $totalNilaiJual;
            $supplier['total_keuntungan_toko'] = $totalKeuntunganToko;
            $supplier['total_bayar_supplier'] = $totalBayarSupplier;
            $supplier['shop_share'] = (float)$totalKeuntunganToko;
            $supplier['supplier_share'] = (float)$totalBayarSupplier;
            $supplier['total_buy_cost'] = $totalModalSupplier;
            $supplier['total_laba'] = $totalKeuntunganToko;

            return $supplier;
        })
        ->sortByDesc('total_revenue')
        ->values()
        ->all();

    // Fetch active stocks and filter for expired variants in PHP
    $expiredStocks = [];
    try {
        $allStocks = \App\Models\SupplierStock::with('supplier')->where('is_active', 1)->get();
    } catch (\Throwable $e) {
        $allStocks = collect();
    }
    foreach ($allStocks as $stock) {
        $variants = $stock->variants;
        if (!is_array($variants)) {
            $variants = json_decode($variants, true) ?: [];
        }
        foreach ($variants as $v) {
            $aq = (int) ($v['available_quantity'] ?? 0);
            if ($aq > 0 && !empty($v['expiry_date'])) {
                $expiry = \Carbon\Carbon::parse($v['expiry_date']);
                if ($expiry->isPast()) {
                    $expStock = new \stdClass();
                    $expStock->id = $stock->id;
                    $expStock->supplier = $stock->supplier;
                    $expStock->item_name = $stock->item_name . ' (' . ($v['weight'] ?? '') . ')';
                    $expStock->available_quantity = $aq;
                    $expStock->expiry_date = $expiry;
                    $expiredStocks[] = $expStock;
                }
            }
        }
    }

    // Fetch manually logged expired items in the filtered month
    try {
        $month = app()->bound('request') ? request()->input('month') : null;
        $expiredLogsQuery = \App\Models\StockLog::with('supplierStock.supplier')
            ->where('type', 'expired');

        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
                $expiredLogsQuery->whereYear('created_at', $date->year)
                                 ->whereMonth('created_at', $date->month);
            } catch (\Exception $e) {}
        } else {
            $now = Carbon::now();
            $expiredLogsQuery->whereYear('created_at', $now->year)
                             ->whereMonth('created_at', $now->month);
        }
        $expiredLogs = $expiredLogsQuery->get();
    } catch (\Throwable $e) {
        $expiredLogs = collect();
    }

    // Map expired logs to suppliers
    $supplierManuallyExpired = [];
    foreach ($expiredLogs as $log) {
        if (!$log->supplierStock) continue;
        
        $supplierName = $log->supplierStock->supplier->name ?? 'Tanpa Supplier';
        
        // Find buy price by matching weight
        $buyPrice = 0;
        $variants = $log->supplierStock->variants;
        if (!is_array($variants)) {
            $variants = json_decode($variants, true) ?: [];
        }
        foreach ($variants as $v) {
            if (($v['weight'] ?? '') === $log->weight) {
                $buyPrice = (float)($v['buy_price'] ?? 0);
                break;
            }
        }
        
        $lossValue = $log->quantity * $buyPrice;
        
        if (!isset($supplierManuallyExpired[$supplierName])) {
            $supplierManuallyExpired[$supplierName] = [
                'products' => [],
                'total_qty' => 0,
                'total_loss' => 0,
            ];
        }
        
        $supplierManuallyExpired[$supplierName]['products'][] = [
            'item_name' => $log->supplierStock->item_name,
            'weight' => $log->weight,
            'quantity' => $log->quantity,
            'buy_price' => $buyPrice,
            'loss_value' => $lossValue,
            'date_logged' => $log->created_at->format('Y-m-d'),
            'description' => $log->description
        ];
        
        $supplierManuallyExpired[$supplierName]['total_qty'] += $log->quantity;
        $supplierManuallyExpired[$supplierName]['total_loss'] += $lossValue;
    }

    // Fetch manually logged stock-in items in the filtered month
    try {
        $stockInLogsQuery = \App\Models\StockLog::with('supplierStock.supplier')
            ->where('type', 'in');

        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
                $stockInLogsQuery->whereYear('created_at', $date->year)
                                 ->whereMonth('created_at', $date->month);
            } catch (\Exception $e) {}
        } else {
            $now = Carbon::now();
            $stockInLogsQuery->whereYear('created_at', $now->year)
                             ->whereMonth('created_at', $now->month);
        }
        $stockInLogs = $stockInLogsQuery->get();
    } catch (\Throwable $e) {
        $stockInLogs = collect();
    }

    // Map stock in logs to suppliers
    $supplierStockIn = [];
    foreach ($stockInLogs as $log) {
        if (!$log->supplierStock) continue;
        
        $supplierName = $log->supplierStock->supplier->name ?? 'Tanpa Supplier';
        
        // Find buy price by matching weight
        $buyPrice = 0;
        $variants = $log->supplierStock->variants;
        if (!is_array($variants)) {
            $variants = json_decode($variants, true) ?: [];
        }
        foreach ($variants as $v) {
            if (($v['weight'] ?? '') === $log->weight) {
                $buyPrice = (float)($v['buy_price'] ?? 0);
                break;
            }
        }
        
        $totalCost = $log->quantity * $buyPrice;
        
        if (!isset($supplierStockIn[$supplierName])) {
            $supplierStockIn[$supplierName] = [
                'logs' => [],
                'total_qty' => 0,
                'total_cost' => 0,
            ];
        }
        
        $supplierStockIn[$supplierName]['logs'][] = [
            'item_name' => $log->supplierStock->item_name,
            'weight' => $log->weight,
            'quantity' => $log->quantity,
            'buy_price' => $buyPrice,
            'total_cost' => $totalCost,
            'date_received' => $log->created_at->format('Y-m-d'),
            'description' => $log->description
        ];
        
        $supplierStockIn[$supplierName]['total_qty'] += $log->quantity;
        $supplierStockIn[$supplierName]['total_cost'] += $totalCost;
    }

    // Ensure suppliers with expired stocks exist in the summary
    $existingSuppliers = array_column($supplierSummary, 'supplier_name');
    foreach ($expiredStocks as $stock) {
        $stockSupplierName = $stock->supplier->name ?? 'Tanpa Supplier';
        if ($supplierFilter !== 'all' && $stockSupplierName !== $supplierFilter) {
            continue;
        }
        if (!in_array($stockSupplierName, $existingSuppliers)) {
            $supplierSummary[] = [
                'supplier_name' => $stockSupplierName,
                'quantity_sold' => 0,
                'total_revenue' => 0,
                'shop_share' => 0,
                'supplier_share' => 0,
                'products' => [],
                'expired_products' => []
            ];
            $existingSuppliers[] = $stockSupplierName;
        }
    }

    // Ensure suppliers with manual expired logs exist in the summary
    foreach (array_keys($supplierManuallyExpired) as $expiredSupplierName) {
        if ($supplierFilter !== 'all' && $expiredSupplierName !== $supplierFilter) {
            continue;
        }
        if (!in_array($expiredSupplierName, $existingSuppliers)) {
            $supplierSummary[] = [
                'supplier_name' => $expiredSupplierName,
                'quantity_sold' => 0,
                'total_revenue' => 0,
                'shop_share' => 0,
                'supplier_share' => 0,
                'products' => [],
                'expired_products' => []
            ];
            $existingSuppliers[] = $expiredSupplierName;
        }
    }

    // Ensure suppliers with stock-in logs exist in the summary
    foreach (array_keys($supplierStockIn) as $inSupplierName) {
        if ($supplierFilter !== 'all' && $inSupplierName !== $supplierFilter) {
            continue;
        }
        if (!in_array($inSupplierName, $existingSuppliers)) {
            $supplierSummary[] = [
                'supplier_name' => $inSupplierName,
                'quantity_sold' => 0,
                'total_revenue' => 0,
                'shop_share' => 0,
                'supplier_share' => 0,
                'products' => [],
                'expired_products' => []
            ];
            $existingSuppliers[] = $inSupplierName;
        }
    }

    foreach ($supplierSummary as &$supplier) {
        if (!isset($supplier['expired_products'])) {
            $supplier['expired_products'] = [];
        }
        foreach ($expiredStocks as $stock) {
            $stockSupplierName = $stock->supplier->name ?? 'Tanpa Supplier';
            if ($stockSupplierName === $supplier['supplier_name']) {
                $supplier['expired_products'][] = [
                    'item_name' => $stock->item_name,
                    'quantity' => $stock->available_quantity,
                    'expiry_date' => $stock->expiry_date->format('Y-m-d')
                ];
            }
        }

        // Attach manually expired logs
        $sName = $supplier['supplier_name'];
        if (isset($supplierManuallyExpired[$sName])) {
            $supplier['manually_expired_products'] = $supplierManuallyExpired[$sName]['products'];
            $supplier['total_expired_qty'] = $supplierManuallyExpired[$sName]['total_qty'];
            $supplier['total_expired_loss'] = $supplierManuallyExpired[$sName]['total_loss'];
        } else {
            $supplier['manually_expired_products'] = [];
            $supplier['total_expired_qty'] = 0;
            $supplier['total_expired_loss'] = 0;
        }

        // Attach stock-in logs
        if (isset($supplierStockIn[$sName])) {
            $supplier['stock_in_products'] = $supplierStockIn[$sName]['logs'];
            $supplier['total_stock_in_qty'] = $supplierStockIn[$sName]['total_qty'];
            $supplier['total_stock_in_cost'] = $supplierStockIn[$sName]['total_cost'];
        } else {
            $supplier['stock_in_products'] = [];
            $supplier['total_stock_in_qty'] = 0;
            $supplier['total_stock_in_cost'] = 0;
        }

        // === BUILD UNIFIED PRODUCT SUMMARY ===
        $productSummary = [];

        // 1) From stock-in logs
        foreach ($supplier['stock_in_products'] as $item) {
            $key = $item['item_name'] . '|' . ($item['weight'] ?? '-');
            if (!isset($productSummary[$key])) {
                $productSummary[$key] = [
                    'item_name' => $item['item_name'],
                    'weight' => $item['weight'] ?? '-',
                    'qty_masuk' => 0,
                    'qty_terjual' => 0,
                    'qty_retur' => 0,
                    'sisa_stok' => 0,
                    'buy_price' => $item['buy_price'],
                    'sell_price' => 0,
                    'total_bayar_supplier' => 0,
                    'total_omzet' => 0,
                ];
            }
            $productSummary[$key]['qty_masuk'] += $item['quantity'];
            $productSummary[$key]['total_bayar_supplier'] += $item['total_cost'];
            $productSummary[$key]['buy_price'] = $item['buy_price'];
        }

        // 2) From sold products
        foreach ($supplier['products'] as $item) {
            $key = $item['product_name'] . '|' . ($item['weight'] ?? '-');
            if (!isset($productSummary[$key])) {
                $productSummary[$key] = [
                    'item_name' => $item['product_name'],
                    'weight' => $item['weight'] ?? '-',
                    'qty_masuk' => 0,
                    'qty_terjual' => 0,
                    'qty_retur' => 0,
                    'sisa_stok' => 0,
                    'buy_price' => 0,
                    'sell_price' => 0,
                    'total_bayar_supplier' => 0,
                    'total_omzet' => 0,
                ];
            }
            $productSummary[$key]['qty_terjual'] += $item['quantity_sold'];
            $productSummary[$key]['total_omzet'] += $item['revenue'];
            if ($item['quantity_sold'] > 0) {
                $productSummary[$key]['sell_price'] = round($item['revenue'] / $item['quantity_sold']);
            }
        }

        // 3) From manually expired products
        foreach ($supplier['manually_expired_products'] as $item) {
            $key = $item['item_name'] . '|' . ($item['weight'] ?? '-');
            if (!isset($productSummary[$key])) {
                $productSummary[$key] = [
                    'item_name' => $item['item_name'],
                    'weight' => $item['weight'] ?? '-',
                    'qty_masuk' => 0,
                    'qty_terjual' => 0,
                    'qty_retur' => 0,
                    'sisa_stok' => 0,
                    'buy_price' => $item['buy_price'],
                    'sell_price' => 0,
                    'total_bayar_supplier' => 0,
                    'total_omzet' => 0,
                ];
            }
            $productSummary[$key]['qty_retur'] += $item['quantity'];
        }

        // 4) Get remaining stock from SupplierStock variants for this supplier
        try {
            $supplierStocks = \App\Models\SupplierStock::with('supplier')
                ->whereHas('supplier', function($q) use ($sName) {
                    $q->where('name', $sName);
                })
                ->where('is_active', 1)
                ->get();

            foreach ($supplierStocks as $stock) {
                $variants = $stock->variants;
                if (!is_array($variants)) {
                    $variants = json_decode($variants, true) ?: [];
                }
                foreach ($variants as $v) {
                    $vWeight = $v['weight'] ?? '-';
                    $key = $stock->item_name . '|' . $vWeight;
                    if (isset($productSummary[$key])) {
                        $productSummary[$key]['sisa_stok'] = (int)($v['available_quantity'] ?? 0);
                        // Backfill buy_price and sell_price if missing
                        if ($productSummary[$key]['buy_price'] <= 0) {
                            $productSummary[$key]['buy_price'] = (float)($v['buy_price'] ?? 0);
                        }
                        if ($productSummary[$key]['sell_price'] <= 0) {
                            $productSummary[$key]['sell_price'] = (float)($v['price'] ?? 0);
                        }
                    } else {
                        // Product exists in stock but has no activity this period
                        $aq = (int)($v['available_quantity'] ?? 0);
                        if ($aq > 0) {
                            $productSummary[$key] = [
                                'item_name' => $stock->item_name,
                                'weight' => $vWeight,
                                'qty_masuk' => 0,
                                'qty_terjual' => 0,
                                'qty_retur' => 0,
                                'sisa_stok' => $aq,
                                'buy_price' => (float)($v['buy_price'] ?? 0),
                                'sell_price' => (float)($v['price'] ?? 0),
                                'total_bayar_supplier' => 0,
                                'total_omzet' => 0,
                            ];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore if SupplierStock query fails
        }

        // Calculate keuntungan per product
        foreach ($productSummary as &$p) {
            $p['keuntungan'] = $p['total_omzet'] - ($p['qty_terjual'] * $p['buy_price']);
        }
        unset($p);

        $supplier['product_summary'] = array_values($productSummary);
        $supplier['product_count'] = count($productSummary);

        // Financial summary for this supplier
        $supplier['total_laba'] = $supplier['total_revenue'] - $supplier['total_stock_in_cost'] - $supplier['total_expired_loss'];
    }

    return [
        'products' => $products,
        'best_seller' => $bestSeller,
        'supplier_breakdown' => collect($supplierSummary),
    ];
}

public function report(Request $request)
{
    return redirect('/admin/reports/store');
}

public function storeReport(Request $request)
{
    $query = Order::with('items.product')
        ->where('payment_status', 'paid');

    $orders = $this->applyReportDateFilter($query, $request)
        ->orderBy('id', 'desc')
        ->get();

    $month = $request->input('month', Carbon::now()->format('Y-m'));
    $periodLabel = null;

    try {
        $periodLabel = Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y');
    } catch (\Exception $e) {
        $periodLabel = 'Semua Periode';
    }

    $totalSales = $orders->sum('total_amount');
    $totalOrders = $orders->count();
    $totalItemsSold = $orders->sum(function ($order) {
        return $order->items->sum('qty');
    });

    $soldProducts = $orders->flatMap(function ($order) {
        return $order->items;
    })->groupBy(function ($item) {
        return $item->product_id ?? 'unknown';
    })->map(function ($items) {
        $product = $items->first()->product;
        if (!$product) {
            try {
                if (\Schema::hasTable('products')) {
                    $product = \DB::table('products')->where('id', $items->first()->product_id)->first();
                }
            } catch (\Throwable $e) {
            }
        }

        return [
            'product_name' => $product->item_name ?? $product->name ?? 'Produk Tidak Diketahui',
            'quantity_sold' => $items->sum('qty'),
            'revenue' => $items->sum('subtotal'),
        ];
    })->sortByDesc('quantity_sold')->values();

    // Fetch expired logs for the selected month
    $expiredLogsQuery = \App\Models\StockLog::with('supplierStock')
        ->where('type', 'expired');

    if ($month) {
        try {
            $date = Carbon::createFromFormat('Y-m', $month);
            $expiredLogsQuery->whereYear('created_at', $date->year)
                             ->whereMonth('created_at', $date->month);
        } catch (\Exception $e) {}
    } else {
        $now = Carbon::now();
        $expiredLogsQuery->whereYear('created_at', $now->year)
                         ->whereMonth('created_at', $now->month);
    }
    $expiredLogs = $expiredLogsQuery->orderBy('id', 'desc')->get();

    $totalExpiredLoss = 0;
    foreach ($expiredLogs as $log) {
        $buyPrice = 0;
        if ($log->supplierStock) {
            $variants = is_array($log->supplierStock->variants) ? $log->supplierStock->variants : (json_decode($log->supplierStock->variants, true) ?: []);
            $logWeightGrams = self::parseWeightToGrams($log->weight);
            foreach ($variants as $v) {
                if (self::parseWeightToGrams($v['weight'] ?? '') === $logWeightGrams || count($variants) === 1) {
                    $buyPrice = (float) ($v['buy_price'] ?? $v['price'] ?? 0);
                    if ($buyPrice <= 0) {
                        $buyPrice = (float) ($v['price'] ?? 0);
                    }
                    break;
                }
            }
        }
        $log->buy_price = $buyPrice;
        $log->total_loss = $buyPrice * $log->quantity;
        $totalExpiredLoss += $log->total_loss;
    }

    $netSales = max(0, $totalSales - $totalExpiredLoss);

    return view(
        'admin.reports.store',
        compact(
            'orders',
            'totalSales',
            'totalOrders',
            'totalItemsSold',
            'month',
            'periodLabel',
            'soldProducts',
            'expiredLogs',
            'totalExpiredLoss',
            'netSales'
        )
    );
}

public function supplierReport(Request $request)
{
    $query = Order::with('items.product')
        ->where('payment_status', 'paid');

    $orders = $this->applyReportDateFilter($query, $request)
        ->orderBy('id', 'desc')
        ->get();

    $month = $request->input('month', Carbon::now()->format('Y-m'));
    $periodLabel = null;
    try {
        $periodLabel = Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y');
    } catch (\Exception $e) {
        $periodLabel = 'Semua Periode';
    }

    $supplierFilter = $request->input('supplier_filter', 'all');
    $suppliers = Supplier::orderBy('name')->get();

    $totalSales = $orders->sum('total_amount');
    $totalOrders = $orders->count();
    $salesReport = $this->buildSalesReportData($orders, 'supplier', $supplierFilter);
    $totalItemsSold = $salesReport['products']->sum('quantity_sold');
    $totalStockInCost = collect($salesReport['supplier_breakdown'])->sum('total_stock_in_cost');
    $totalExpiredLoss = collect($salesReport['supplier_breakdown'])->sum('total_expired_loss');

    // Prepare supplier performance chart data
    $supplierChartLabels = [];
    $supplierChartValues = [];
    foreach ($salesReport['supplier_breakdown'] as $sb) {
        $supplierChartLabels[] = $sb['supplier_name'];
        $supplierChartValues[] = $sb['total_revenue'];
    }

    return view(
        'admin.reports.supplier',
        compact(
            'orders',
            'totalSales',
            'totalOrders',
            'salesReport',
            'totalItemsSold',
            'month',
            'periodLabel',
            'supplierFilter',
            'suppliers',
            'totalStockInCost',
            'totalExpiredLoss',
            'supplierChartLabels',
            'supplierChartValues'
        )
    );
}

public function downloadStoreReportPdf(Request $request)
{
    $query = Order::with('items.product')
        ->where('payment_status', 'paid');

    $orders = $this->applyReportDateFilter($query, $request)
        ->orderBy('id', 'desc')
        ->get();

    $month = $request->input('month', Carbon::now()->format('Y-m'));
    $periodLabel = null;

    try {
        $periodLabel = Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y');
    } catch (\Exception $e) {
        $periodLabel = 'Semua Periode';
    }

    $totalItemsSold = $orders->sum(function ($order) {
        return $order->items->sum('qty');
    });

    $soldProducts = $orders->flatMap(function ($order) {
        return $order->items;
    })->groupBy(function ($item) {
        return $item->product_id ?? 'unknown';
    })->map(function ($items) {
        $product = $items->first()->product;
        if (!$product) {
            try {
                if (\Schema::hasTable('products')) {
                    $product = \DB::table('products')->where('id', $items->first()->product_id)->first();
                }
            } catch (\Throwable $e) {
            }
        }

        return [
            'product_name' => $product->item_name ?? $product->name ?? 'Produk Tidak Diketahui',
            'quantity_sold' => $items->sum('qty'),
            'revenue' => $items->sum('subtotal'),
        ];
    })->sortByDesc('quantity_sold')->values();

    $pdf = Pdf::loadView('admin.reports.pdf.store', [
        'orders' => $orders,
        'totalSales' => $orders->sum('total_amount'),
        'totalOrders' => $orders->count(),
        'totalItemsSold' => $totalItemsSold,
        'month' => $month,
        'periodLabel' => $periodLabel,
        'soldProducts' => $soldProducts,
    ]);

    return $pdf->download('laporan-penjualan-toko-' . now()->format('YmdHis') . '.pdf');
}

public function downloadSupplierReportPdf(Request $request)
{
    $query = Order::with('items.product')
        ->where('payment_status', 'paid');

    $orders = $this->applyReportDateFilter($query, $request)
        ->orderBy('id', 'desc')
        ->get();

    $month = $request->input('month', Carbon::now()->format('Y-m'));
    $periodLabel = null;
    try {
        $periodLabel = Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y');
    } catch (\Exception $e) {
        $periodLabel = 'Semua Periode';
    }

    $supplierFilter = $request->input('supplier_filter', 'all');
    $salesReport = $this->buildSalesReportData($orders, 'supplier', $supplierFilter);

    $pdf = Pdf::loadView('admin.reports.pdf.supplier', [
        'orders' => $orders,
        'totalSales' => $orders->sum('total_amount'),
        'totalOrders' => $orders->count(),
        'salesReport' => $salesReport,
        'totalItemsSold' => $salesReport['products']->sum('quantity_sold'),
        'month' => $month,
        'periodLabel' => $periodLabel,
        'supplierFilter' => $supplierFilter,
        'totalStockInCost' => collect($salesReport['supplier_breakdown'])->sum('total_stock_in_cost'),
        'totalExpiredLoss' => collect($salesReport['supplier_breakdown'])->sum('total_expired_loss'),
    ]);

    return $pdf->download('laporan-supplier-' . now()->format('YmdHis') . '.pdf');
}

public function exportReport(Request $request)
{
    $query = Order::with('items.product')
        ->where('payment_status', 'paid');

    $orders = $this->applyReportDateFilter($query, $request)
        ->orderBy('id', 'desc')
        ->get();

    $reportType = $request->input('report_type', 'sales');
    $supplierFilter = $request->input('supplier_filter', 'all');
    $salesReport = $this->buildSalesReportData($orders, $reportType, $supplierFilter);

    if ($reportType === 'supplier' || $reportType === 'supplier_products') {
        $rows = [[
            'Nama Supplier',
            'Jumlah Terjual (pcs)',
            'Total Penjualan Ritel (Rp)',
            'Total Pembelian Stok (PO) (Rp)',
            'Total Kerugian Expired (Rp)'
        ]];

        foreach ($salesReport['supplier_breakdown'] as $supplier) {
            $rows[] = [
                $supplier['supplier_name'],
                $supplier['quantity_sold'],
                $supplier['total_revenue'],
                $supplier['total_stock_in_cost'] ?? 0,
                $supplier['total_expired_loss'] ?? 0
            ];
        }
    } else {
        $rows = [[
            'Nama Produk',
            'Jumlah Terjual',
            'Total Pendapatan'
        ]];

        foreach ($salesReport['products'] as $product) {
            $rows[] = [
                $product['product_name'],
                $product['quantity_sold'],
                $product['revenue'],
            ];
        }
    }

    $filename = 'laporan-' . $reportType . '-' . now()->format('Ymd-His') . '.csv';
    $handle = fopen('php://temp', 'r+');

    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }

    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);

    return response($csv, 200, [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}

}