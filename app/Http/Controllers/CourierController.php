<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class CourierController extends Controller
{
    public function dashboard()
    {
        $courierFilter = function($query) {
            $query->whereNotNull('delivery_address')
                  ->where('delivery_address', '!=', '')
                  ->where('delivery_option', 'NOT LIKE', '%Ambil di Toko%')
                  ->where('delivery_option', 'NOT LIKE', '%pickup%');
        };

        $pending = Order::whereIn('status', ['Menunggu Kurir', 'pending', 'preparing'])
            ->where('payment_status', 'paid')
            ->where($courierFilter)
            ->count();

        $delivering = Order::whereIn('status', ['Diambil Kurir', 'Dalam Perjalanan', 'shipped', 'almost_arrived'])
            ->where($courierFilter)
            ->count();

        $delivered = Order::where(function ($q) {
                $q->whereIn('status', ['Terkirim', 'completed', 'Selesai'])
                  ->orWhere('tracking_status', 'completed');
            })
            ->where($courierFilter)
            ->count();

        $totalToday = Order::where(function ($q) {
                $q->whereIn('status', ['Terkirim', 'completed', 'Selesai'])
                  ->orWhere('tracking_status', 'completed');
            })
            ->where($courierFilter)
            ->count();

        return view('courier.dashboard', compact('pending', 'delivering', 'delivered', 'totalToday'));
    }

    public function index(Request $request)
    {
        // Tampilkan hanya pesanan yang punya alamat dan bukan Ambil di Toko (Urutkan dari yang pertama pesan)
        $orders = Order::where('payment_status', 'paid')
            ->whereNotNull('delivery_address')
            ->where('delivery_address', '!=', '')
            ->where('delivery_option', 'NOT LIKE', '%Ambil di Toko%')
            ->where('delivery_option', 'NOT LIKE', '%pickup%')
            ->where(function($query) {
                $query->where('status', '!=', 'Terkirim')
                      ->orWhereNull('status');
            })
            ->orderBy('id', 'asc') // FIFO: Siapa yang pertama kali pesan ada di No. 1
            ->get();
            
        return view('courier.deliveries.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        
        return view('courier.deliveries.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $status = $request->input('status');
        
        if ($status == 'Diambil Kurir') {
            $courier = \App\Models\Courier::firstOrCreate(
                ['id' => 1],
                ['name' => 'Kurir Toko', 'username' => 'courier', 'password' => bcrypt('admin123')]
            );
            $order->courier_id = $courier->id;
            $order->status = 'Diambil Kurir';
            $order->tracking_status = 'preparing';
        } elseif ($status == 'Dalam Perjalanan') {
            $order->status = 'Dalam Perjalanan';
            $order->tracking_status = 'shipped';
            $order->almost_arrived_at = Carbon::now();
        } elseif ($status == 'Terkirim') {
            $request->validate([
                'delivery_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            
            if ($request->hasFile('delivery_proof')) {
                $file = $request->file('delivery_proof');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/delivery_proofs'), $filename);
                $order->delivery_proof = 'uploads/delivery_proofs/' . $filename;
            }
            
            $order->status = 'Terkirim';
            $order->tracking_status = 'completed';
            $order->delivered_at = Carbon::now();
        } elseif ($status == 'Pengembalian') {
            $order->status = 'Pengembalian';
            $order->tracking_status = 'returned';
            if (!$order->return_status) {
                $order->return_status = 'pending';
            }
        } elseif ($status == 'Pengiriman Pengganti') {
            $order->status = 'Pengiriman Pengganti';
            $order->tracking_status = 'shipped';
            $order->return_status = 'reshipped';
            $order->almost_arrived_at = Carbon::now();
        } elseif ($status == 'Selesai Retur') {
            $request->validate([
                'delivery_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
            ]);

            if ($request->hasFile('delivery_proof')) {
                $file = $request->file('delivery_proof');
                $filename = time() . '_replacement_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/delivery_proofs'), $filename);
                $order->delivery_proof = 'uploads/delivery_proofs/' . $filename;
            }

            $order->status = 'Terkirim';
            $order->tracking_status = 'completed';
            $order->return_status = 'resolved';
            $order->delivered_at = Carbon::now();
        }
        
        $order->save();
        
        return back()->with('success', 'Status pengiriman berhasil diperbarui!');
    }

    public function history()
    {
        $orders = Order::where(function($q) {
                $q->whereIn('status', ['Terkirim', 'completed', 'Selesai', 'Pengembalian'])
                  ->orWhere('tracking_status', 'returned')
                  ->orWhere('tracking_status', 'completed');
            })
            ->whereNotNull('delivery_address')
            ->where('delivery_address', '!=', '')
            ->where('delivery_option', 'NOT LIKE', '%Ambil di Toko%')
            ->where('delivery_option', 'NOT LIKE', '%pickup%')
            ->orderBy('id', 'desc')
            ->get();
            
        return view('courier.history', compact('orders'));
    }
}
