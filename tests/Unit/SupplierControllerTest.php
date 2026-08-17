<?php

namespace Tests\Unit;

use App\Http\Controllers\SupplierController;
use PHPUnit\Framework\TestCase;

class SupplierControllerTest extends TestCase
{
    public function test_build_whatsapp_message_contains_supplier_name_and_selected_items(): void
    {
        $controller = new SupplierController();

        $message = $controller->buildWhatsAppMessage('Supplier Dodol', [
            ['name' => 'Dodol Kentang'],
            ['name' => 'Dodol Mocca'],
        ]);

        $this->assertStringContainsString('Saya ingin memesan produk berikut:', $message);
        $this->assertStringContainsString('Dodol Kentang', $message);
        $this->assertStringNotContainsString(': 5', $message);
    }
}
