<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderNumber = 'ORD-' . time();

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'delivery_option' => $request->delivery_option,
            'delivery_address' => $request->delivery_address,
            'notes' => $request->notes,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
            'payment_status' => 'pending',
            'midtrans_order_id' => $orderNumber
        ]);
        foreach($request->items as $item){

    OrderItem::create([

        'order_id' => $order->id,

        'product_id' => $item['id'],

        'qty' => $item['quantity'],

        'price' => $item['price'],

        'subtotal' => $item['price']*$item['quantity']
    ]);
}
        $params = [
            'transaction_details' => [
                'order_id' => $orderNumber,
                'gross_amount' => (int) $request->total_amount
            ],

            'customer_details' => [
                'first_name' => $request->customer_name,
                'phone' => $request->customer_phone
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken
        ]);
    }
    public function index()
{
    $orders = Order::orderBy('id', 'desc')->get();

    return view(
        'admin.orders.index',
        compact('orders')
    );
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

    return response()->json([
        'success' => true,
        'payment_status' =>
            $order->payment_status,
        'status' =>
            $order->status
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

    if ($request->transaction_status == 'settlement') {

        $order->payment_status = 'paid';
        $order->status = 'completed';
        $order->paid_at = now();
        $order->save();

        // stok akan kita tambahkan berikutnya
    }
    foreach($order->items as $item){

    $product = Product::find(
        $item->product_id
    );

    if($product){

        $product->stock -= $item->qty;

        if($product->stock < 0){
            $product->stock = 0;
        }

        $product->save();
    }
}

    return response()->json([
        'success' => true
    ]); 
}
public function show($id)
{
    $order = Order::with('items.product')
        ->findOrFail($id);

    return view(
        'admin.orders.show',
        compact('order')
    );
}
public function simulatePayment($id)
{
    $order = Order::findOrFail($id);

    $order->payment_status = 'paid';
    $order->status = 'completed';
    $order->paid_at = now();
    $order->save();

    foreach($order->items as $item)
    {
        $product = Product::find(
            $item->product_id
        );

        if($product)
        {
            $product->stock -= $item->qty;

            if($product->stock < 0)
            {
                $product->stock = 0;
            }

            $product->save();
        }
    }

    return redirect(
        '/admin/orders'
    );
}
public function report()
{
    $orders = Order::where(
        'payment_status',
        'paid'
    )
    ->orderBy('id', 'desc')
    ->get();

    $totalSales = $orders->sum(
        'total_amount'
    );

    $totalOrders = $orders->count();

    return view(
        'admin.reports.index',
        compact(
            'orders',
            'totalSales',
            'totalOrders'
        )
    );
}

}