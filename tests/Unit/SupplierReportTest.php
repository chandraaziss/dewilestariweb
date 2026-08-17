<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use App\Models\Supplier;
use PHPUnit\Framework\TestCase;

class SupplierReportTest extends TestCase
{
    public function test_resolve_supplier_for_product_uses_supplier_items_from_database(): void
    {
        $controller = new OrderController();

        $supplier = new Supplier([
            'name' => 'Supplier Tempe',
            'items' => ['Tempe Murni', 'Tempe Original'],
        ]);

        $product = (object) [
            'name' => 'Tempe Original',
            'description' => 'Produk premium',
            'category' => 'makanan',
        ];

        $resolved = $controller->resolveSupplierForProduct($product, collect([$supplier]));

        $this->assertSame('Supplier Tempe', $resolved);
    }
}
