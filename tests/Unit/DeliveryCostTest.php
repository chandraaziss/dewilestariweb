<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use PHPUnit\Framework\TestCase;

class DeliveryCostTest extends TestCase
{
    public function test_delivery_is_free_within_cimahi_radius_when_weight_is_enough(): void
    {
        $cost = OrderController::calculateDeliveryCost('delivery', 5000, 'Cimahi Tengah');

        $this->assertSame(10000, $cost);
    }

    public function test_delivery_cost_is_charged_per_5km_outside_cimahi(): void
    {
        $cost = OrderController::calculateDeliveryCost('delivery', 5000, 'Bandung', 18);

        $this->assertSame(40000, $cost);
    }

    public function test_weight_values_in_kg_are_converted_to_grams(): void
    {
        $this->assertSame(5000, OrderController::parseWeightToGrams('5 kg'));
        $this->assertSame(2500, OrderController::parseWeightToGrams('2.5 kg'));
    }

    public function test_weight_values_in_gram_are_converted_to_grams(): void
    {
        $this->assertSame(250, OrderController::parseWeightToGrams('250 gram'));
        $this->assertSame(500, OrderController::parseWeightToGrams('500g'));
    }

    public function test_expedition_delivery_cost_calculated_by_weight_and_zone(): void
    {
        // 1 kg J&T Luar Kota (Pulau Jawa) = 18.000
        $cost1 = OrderController::calculateDeliveryCost('expedition', 500, 'Surabaya', 0, 'jnt', 'luar_kota_jawa');
        $this->assertSame(18000, $cost1);

        // 2 kg JNE Luar Pulau Jawa = 2 * 40.000 = 80.000
        $cost2 = OrderController::calculateDeliveryCost('expedition', 1500, 'Medan', 0, 'jne', 'luar_pulau_jawa');
        $this->assertSame(80000, $cost2);

        // 1 kg POS Luar Kota (Pulau Jawa) = 16.000
        $cost3 = OrderController::calculateDeliveryCost('expedition', 250, 'Semarang', 0, 'pos', 'luar_kota_jawa');
        $this->assertSame(16000, $cost3);
    }

    public function test_is_local_delivery_area_returns_true_only_for_cimahi_and_bandung(): void
    {
        $this->assertTrue(OrderController::isLocalDeliveryArea('Kota Cimahi', 'Cigugur Tengah'));
        $this->assertTrue(OrderController::isLocalDeliveryArea('Cimahi Tengah', 'Jl. Raya Cimindi No. 59'));
        $this->assertTrue(OrderController::isLocalDeliveryArea('Kota Bandung', 'Jl. Asia Afrika No. 10'));

        $this->assertFalse(OrderController::isLocalDeliveryArea('Cianjur', 'Jl. Raya Cianjur'));
        $this->assertFalse(OrderController::isLocalDeliveryArea('Padalarang', 'Kabupaten Bandung Barat'));
        $this->assertFalse(OrderController::isLocalDeliveryArea('Soreang', 'Kabupaten Bandung'));
        $this->assertFalse(OrderController::isLocalDeliveryArea('Garut', 'Jl. Otista Garut'));
        $this->assertFalse(OrderController::isLocalDeliveryArea('Jakarta Selatan', 'Jl. Sudirman'));
    }
}
