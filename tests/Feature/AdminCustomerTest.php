<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminCustomerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create the users table dynamically for SQLite in-memory testing
        if (!\Schema::hasTable('users')) {
            \Schema::create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // Create the orders table dynamically for SQLite in-memory testing
        if (!\Schema::hasTable('orders')) {
            \Schema::create('orders', function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('order_number');
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->string('delivery_option');
                $table->text('delivery_address')->nullable();
                $table->integer('delivery_cost')->default(0);
                $table->text('notes')->nullable();
                $table->decimal('total_amount', 12, 2);
                $table->string('status')->default('pending');
                $table->string('payment_status')->default('pending');
                $table->string('midtrans_order_id')->nullable();
                $table->timestamps();
            });
        }
    }

    protected function tearDown(): void
    {
        \Schema::dropIfExists('orders');
        \Schema::dropIfExists('users');
        parent::tearDown();
    }

    public function test_guest_cannot_access_admin_customers()
    {
        $response = $this->get('/admin/customers');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_customers_list()
    {
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get('/admin/customers');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('john@example.com');
    }

    public function test_admin_can_edit_customer()
    {
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get("/admin/customers/{$customer->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('John Doe');
    }

    public function test_admin_can_update_customer()
    {
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->put("/admin/customers/{$customer->id}", [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => 'newpassword123',
            ]);

        $response->assertRedirect('/admin/customers');
        $response->assertSessionHas('success');

        $customer->refresh();
        $this->assertEquals('Jane Doe', $customer->name);
        $this->assertEquals('jane@example.com', $customer->email);
        $this->assertTrue(\Hash::check('newpassword123', $customer->password));
    }

    public function test_admin_can_delete_customer()
    {
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->delete("/admin/customers/{$customer->id}");

        $response->assertRedirect('/admin/customers');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $customer->id,
        ]);
    }
}
