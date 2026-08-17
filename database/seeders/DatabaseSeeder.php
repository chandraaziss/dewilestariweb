<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@dewilestari.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123'),
                'plain_password' => 'admin123',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 10, Jakarta',
                'role' => 'admin',
            ]
        );

        // Customer user
        $cust = \App\Models\User::updateOrCreate(
            ['email' => 'siti@gmail.com'],
            [
                'name' => 'Siti Aminah',
                'password' => bcrypt('pelanggan123'),
                'plain_password' => 'pelanggan123',
                'phone' => '081987654321',
                'address' => 'Jl. Kenanga No. 45, Bandung',
                'role' => 'cust',
            ]
        );

        // Address
        \App\Models\UserAddress::updateOrCreate(
            ['user_id' => $cust->id, 'label' => 'Rumah Utama'],
            [
                'receiver_name' => 'Siti Aminah',
                'receiver_phone' => '081987654321',
                'address' => 'Jl. Kenanga No. 45, RT 02/RW 05',
                'city' => 'Bandung',
                'is_primary' => true,
            ]
        );

        // Supplier
        $supplier = \App\Models\Supplier::updateOrCreate(
            ['name' => 'PT Jaya Rasa Nusantara'],
            [
                'slug' => 'jaya-rasa',
                'phone' => '6281234567893',
                'email' => 'sales@jayarasa.co.id',
                'address' => 'Jl. Raya Industri No. 88, Cikarang',
                'items' => [
                    ['name' => 'Kripik Tempe Premium', 'unit' => 'pcs'],
                    ['name' => 'Kripik Pisang Cokelat', 'unit' => 'pcs'],
                ],
            ]
        );

        // Supplier Stock
        \App\Models\SupplierStock::updateOrCreate(
            ['item_name' => 'Kripik Tempe Premium'],
            [
                'supplier_id' => $supplier->id,
                'description' => 'Kripik tempe renyah gurih asli racikan khas Toko Dewi Lestari 2.',
                'image_path' => 'storage/products/kripik_tempe.jpg',
                'is_active' => true,
                'entry_date' => '2026-08-01',
                'variants' => [
                    [
                        'weight' => '250g',
                        'initial_quantity' => 50,
                        'available_quantity' => 45,
                        'expiry_date' => '2026-12-31',
                        'price' => 15000,
                        'hpp' => 10000,
                    ],
                    [
                        'weight' => '500g',
                        'initial_quantity' => 30,
                        'available_quantity' => 28,
                        'expiry_date' => '2026-12-31',
                        'price' => 28000,
                        'hpp' => 19000,
                    ],
                ],
            ]
        );
    }
}
