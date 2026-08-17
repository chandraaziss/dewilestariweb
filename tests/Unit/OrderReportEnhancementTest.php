<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use PHPUnit\Framework\TestCase;

class OrderReportEnhancementTest extends TestCase
{
    public function test_supplier_report_groups_sales_and_calculates_shares()
    {
        $controller = new OrderController();

        $orders = collect([
            (object) [
                'items' => collect([
                    (object) [
                        'product_id' => 1,
                        'qty' => 2,
                        'price' => 100000,
                        'buy_price' => 70000,
                        'subtotal' => 200000,
                        'product' => (object) [
                            'name' => 'Dodol Isi Coklat',
                            'supplier_name' => 'Supplier Dodol',
                        ],
                    ],
                ]),
            ],
            (object) [
                'items' => collect([
                    (object) [
                        'product_id' => 2,
                        'qty' => 1,
                        'subtotal' => 150000,
                        'product' => (object) [
                            'name' => 'Kripik Singkong',
                            'supplier_name' => 'Supplier Kripik',
                        ],
                    ],
                ]),
            ],
        ]);

        $report = $controller->buildSalesReportData($orders, 'supplier');

        $this->assertArrayHasKey('supplier_breakdown', $report);
        $this->assertCount(2, $report['supplier_breakdown']);

        $dodol = $report['supplier_breakdown']->firstWhere('supplier_name', 'Supplier Dodol');
        $this->assertNotNull($dodol);
        $this->assertSame(200000.0, $dodol['total_revenue']);
        $this->assertSame(60000.0, $dodol['shop_share']);
        $this->assertSame(140000.0, $dodol['supplier_share']);
    }
}
