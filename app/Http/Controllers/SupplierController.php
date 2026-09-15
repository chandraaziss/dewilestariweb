<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('orders')->orderBy('name')->get();
        $orders = \App\Models\SupplierOrder::with('supplier')->latest()->get();
        $returns = \App\Models\SupplierReturn::with(['supplier', 'supplierOrder'])->latest()->get();

        return view('admin.suppliers.index', compact('suppliers', 'orders', 'returns'));
    }

    public function completeOrder($id)
    {
        $order = \App\Models\SupplierOrder::findOrFail($id);
        $order->status = 'completed';
        $order->save();

        return redirect()->back()->with('success', "Status Pesanan #{$order->invoice_number} berhasil diperbarui menjadi 'Telah Diterima'!");
    }

    public function show($slug)
    {
        $supplier = Supplier::with(['orders' => fn($q) => $q->latest()])->where('slug', $slug)->firstOrFail();

        return view('admin.suppliers.show', ['supplier' => $supplier]);
    }

    public function manage()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('admin.suppliers.manage', ['suppliers' => $suppliers]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
        ]);

        Supplier::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'phone' => $data['phone'] ?? null,
            'items' => $data['items'],
        ]);

        return redirect('/admin/suppliers/manage')->with('success', 'Supplier baru berhasil ditambahkan.');
    }

    public function edit($slug)
    {
        $supplier = Supplier::where('slug', $slug)->firstOrFail();

        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $slug)
    {
        $supplier = Supplier::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
        ]);

        $supplier->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'phone' => $data['phone'] ?? null,
            'items' => $data['items'],
        ]);

        return redirect('/admin/suppliers/manage')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy($slug)
    {
        $supplier = Supplier::where('slug', $slug)->firstOrFail();

        $supplier->delete();

        return redirect('/admin/suppliers/manage')->with('success', 'Supplier berhasil dihapus.');
    }

    public function sendOrder(Request $request, $slug)
    {
        $supplier = Supplier::where('slug', $slug)->firstOrFail();

        $selectedItems = [];
        foreach ($request->input('items', []) as $item) {
            $hasName = !empty(trim($item['name'] ?? ''));
            $isSelected = !empty($item['selected']);
            $hasDetails = !empty(trim($item['quantity'] ?? '')) || !empty(trim($item['variant'] ?? '')) || !empty(trim($item['size'] ?? ''));

            if ($hasName && ($isSelected || $hasDetails)) {
                $rawSize = !empty(trim($item['size'] ?? '')) ? trim($item['size']) : (!empty(trim($item['variant'] ?? '')) ? trim($item['variant']) : '');
                $rawQty = !empty(trim($item['quantity'] ?? '')) ? trim($item['quantity']) : '';

                $sizes = array_values(array_filter(array_map('trim', explode(',', $rawSize))));
                $quantities = array_values(array_filter(array_map('trim', explode(',', $rawQty))));

                if (count($sizes) > 1 || count($quantities) > 1) {
                    $maxCount = max(count($sizes), count($quantities));
                    for ($i = 0; $i < $maxCount; $i++) {
                        $sz = $sizes[$i] ?? ($sizes[0] ?? null);
                        $qt = $quantities[$i] ?? ($quantities[0] ?? null);
                        $selectedItems[] = [
                            'name' => trim($item['name']),
                            'variant' => $sz,
                            'size' => $sz,
                            'quantity' => $qt,
                        ];
                    }
                } else {
                    $selectedItems[] = [
                        'name' => trim($item['name']),
                        'variant' => $rawSize ?: null,
                        'size' => $rawSize ?: null,
                        'quantity' => $rawQty ?: null,
                    ];
                }
            }
        }

        // Generate Invoice Number
        $invoiceNumber = 'INV-SUP-' . date('Ymd') . '-' . rand(1000, 9999);

        // Save Supplier Order to Database
        $order = \App\Models\SupplierOrder::create([
            'invoice_number' => $invoiceNumber,
            'supplier_id' => $supplier->id,
            'items' => $selectedItems,
            'notes' => $request->input('notes') ?? null,
            'status' => 'sent',
        ]);

        return redirect('/admin/supplier-orders/' . $order->id . '/invoice')->with('success', 'Invoice pesanan supplier berhasil dibuat!');
    }

    public function showInvoice($id)
    {
        $order = \App\Models\SupplierOrder::with('supplier')->findOrFail($id);
        $supplier = $order->supplier;

        $message = $this->buildWhatsAppMessage($supplier->name ?? 'Supplier', $order->items ?? []);
        $phone = preg_replace('/[^0-9]/', '', $supplier->phone ?? '');

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $waUrl = 'https://wa.me/' . $phone . '?text=' . urlencode("Halo {$supplier->name},\n\nBerikut Invoice Pesanan #{$order->invoice_number}:\n\n" . $message);

        return view('admin.suppliers.invoice', compact('order', 'supplier', 'waUrl', 'message'));
    }

    public function buildWhatsAppMessage($supplierName, array $items): string
    {
        $displayName = str_replace('Supplier ', '', $supplierName);
        $message = "Saya ingin memesan produk berikut:\n\n";

        if (empty($items)) {
            $message .= "Tidak ada item yang dipilih.\n";
            return $message;
        }

        foreach ($items as $index => $item) {
            $no = $index + 1;
            $message .= "{$no}. 📦 Nama Produk: {$item['name']}\n";
            if (!empty($item['size'])) {
                $message .= "   - Ukuran: {$item['size']}\n";
            }
            if (!empty($item['quantity'])) {
                $message .= "   - Jumlah: {$item['quantity']}\n";
            }
            $message .= "\n";
        }

        $message .= "Mohon konfirmasi ketersediaan barang dan total harganya. Terima kasih! 🙏";

        return $message;
    }

    public function storeReturn(Request $request)
    {
        // Support array of return items if submitted via form
        if ($request->has('items') && is_array($request->items)) {
            $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'items' => 'required|array|min:1',
                'items.*.item_name' => 'required|string|max:255',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.reason' => 'required|string',
                'proof_image' => 'nullable|image|max:2048',
                'supplier_order_id' => 'nullable|exists:supplier_orders,id',
            ]);

            $proofImagePath = null;
            if ($request->hasFile('proof_image')) {
                $filename = time() . '_retur_' . $request->proof_image->getClientOriginalName();
                $request->proof_image->move(
                    public_path('images/returns'),
                    $filename
                );
                $proofImagePath = 'images/returns/' . $filename;
            }

            $returnNumber = 'RET-SUP-' . date('Ymd') . '-' . rand(1000, 9999);
            $firstRet = null;

            foreach ($request->items as $item) {
                if (empty($item['item_name']) || empty($item['quantity'])) continue;

                $ret = \App\Models\SupplierReturn::create([
                    'return_number' => $returnNumber,
                    'supplier_id' => $request->supplier_id,
                    'supplier_order_id' => $request->supplier_order_id ?: null,
                    'item_name' => trim($item['item_name']),
                    'weight' => !empty($item['weight']) ? trim($item['weight']) : null,
                    'quantity' => (int) $item['quantity'],
                    'reason' => trim($item['reason']),
                    'proof_image' => $proofImagePath,
                    'status' => 'pending',
                ]);
                if (!$firstRet) $firstRet = $ret;
            }

            if ($firstRet) {
                return redirect('/admin/supplier-returns/' . $firstRet->id . '/ticket')->with('success', 'Pengajuan Retur Produk ke Supplier berhasil dibuat!');
            }
            return redirect('/admin/suppliers')->with('success', 'Pengajuan Retur Produk berhasil dibuat!');
        }

        // Single item fallback
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'item_name' => 'required|string|max:255',
            'weight' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'proof_image' => 'nullable|image|max:2048',
            'supplier_order_id' => 'nullable|exists:supplier_orders,id',
        ]);

        $proofImagePath = null;
        if ($request->hasFile('proof_image')) {
            $filename = time() . '_retur_' . $request->proof_image->getClientOriginalName();
            $request->proof_image->move(
                public_path('images/returns'),
                $filename
            );
            $proofImagePath = 'images/returns/' . $filename;
        }

        $returnNumber = 'RET-SUP-' . date('Ymd') . '-' . rand(1000, 9999);

        $ret = \App\Models\SupplierReturn::create([
            'return_number' => $returnNumber,
            'supplier_id' => $request->supplier_id,
            'supplier_order_id' => $request->supplier_order_id ?: null,
            'item_name' => trim($request->item_name),
            'weight' => $request->weight ? trim($request->weight) : null,
            'quantity' => (int) $request->quantity,
            'reason' => trim($request->reason),
            'proof_image' => $proofImagePath,
            'status' => 'pending',
        ]);

        return redirect('/admin/supplier-returns/' . $ret->id . '/ticket')->with('success', 'Pengajuan Retur Produk ke Supplier berhasil dibuat!');
    }

    public function showReturnTicket($id)
    {
        $return = \App\Models\SupplierReturn::with(['supplier', 'supplierOrder'])->findOrFail($id);
        $supplier = $return->supplier;

        // Fetch all returns for this order or batch
        if (!empty($return->supplier_order_id)) {
            $relatedReturns = \App\Models\SupplierReturn::where('supplier_order_id', $return->supplier_order_id)->get();
        } else {
            $relatedReturns = \App\Models\SupplierReturn::where('return_number', $return->return_number)->get();
        }

        if ($relatedReturns->isEmpty()) {
            $relatedReturns = collect([$return]);
        }

        $phone = preg_replace('/[^0-9]/', '', $supplier->phone ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $message = "Halo {$supplier->name},\n\n";
        $message .= "⚠️ *PENGAJUAN RETUR PRODUK TIDAK SESUAI*\n";
        $message .= "No. Retur: #{$return->return_number}\n\n";
        $message .= "Rincian Barang Retur:\n";

        foreach ($relatedReturns as $idx => $retItem) {
            $no = $idx + 1;
            $message .= "{$no}. 📦 Nama Produk: {$retItem->item_name}\n";
            if ($retItem->weight) {
                $message .= "   ⚖️ Ukuran/Varian: {$retItem->weight}\n";
            }
            $message .= "   🔢 Jumlah Retur: {$retItem->quantity} bungkus\n";
            $message .= "   ⚠️ Alasan Kendala: {$retItem->reason}\n\n";
        }

        $message .= "Mohon konfirmasi proses penggantian barang / refund atas retur produk ini. Terima kasih! 🙏";

        $waUrl = 'https://wa.me/' . $phone . '?text=' . urlencode($message);

        return view('admin.suppliers.return_ticket', compact('return', 'relatedReturns', 'supplier', 'waUrl', 'message'));
    }

    public function updateReturnStatus(Request $request, $id)
    {
        $return = \App\Models\SupplierReturn::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,approved,completed,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $return->status = $request->status;
        if ($request->filled('admin_notes')) {
            $return->admin_notes = $request->admin_notes;
        }
        $return->save();

        return redirect()->back()->with('success', "Status Pengajuan Retur #{$return->return_number} berhasil diperbarui.");
    }

    public function destroyReturn($id)
    {
        $return = \App\Models\SupplierReturn::findOrFail($id);
        if (!empty($return->return_number)) {
            \App\Models\SupplierReturn::where('return_number', $return->return_number)->delete();
        } else {
            $return->delete();
        }

        return redirect('/admin/suppliers')->with('success', 'Data pengajuan retur berhasil dihapus.');
    }
}
