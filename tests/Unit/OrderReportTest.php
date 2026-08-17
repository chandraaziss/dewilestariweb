<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class OrderReportTest extends TestCase
{
    public function test_build_sales_report_data_groups_products_by_quantity_and_revenue(): void
    {
        $orders = new Collection([
            (object) [
                'items' => new Collection([
                    (object) [
                        'product_id' => 1,
                        'product' => (object) ['name' => 'Kripik Tempe Original'],
                        'qty' => 2,
                        'price' => 15000,
                        'subtotal' => 30000,
                    ],
                ]),
            ],
            (object) [
                'items' => new Collection([
                    (object) [
                        'product_id' => 1,
                        'product' => (object) ['name' => 'Kripik Tempe Original'],
                        'qty' => 1,
                        'price' => 15000,
                        'subtotal' => 15000,
                    ],
                    (object) [
                        'product_id' => 2,
                        'product' => (object) ['name' => 'Kripik Tempe Balado'],
                        'qty' => 3,
                        'price' => 16000,
                        'subtotal' => 48000,
                    ],
                ]),
            ],
        ]);

        $report = (new OrderController())->buildSalesReportData($orders);

        $this->assertSame(2, $report['products']->count());
        $this->assertSame(3, $report['products']->firstWhere('product_name', 'Kripik Tempe Original')['quantity_sold']);
        $this->assertSame(45000, $report['products']->firstWhere('product_name', 'Kripik Tempe Original')['revenue']);
        $this->assertSame('Kripik Tempe Original', $report['best_seller']['product_name']);
        $this->assertSame(3, $report['best_seller']['quantity_sold']);
    }
}
