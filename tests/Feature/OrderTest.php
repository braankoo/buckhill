<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;

class OrderTest extends Base
{
    public function test_index(): void
    {
        $response = $this->get(route('order.index'));

        $response->assertStatus(401);
    }

    public function test_index_admin_user(): void
    {
        $this->httpRequestWithToken(
            $this->getAdminUser()
        )->get(route('order.index'))
            ->assertStatus(401);
    }

    public function test_index_regular_user(): void
    {
        $this->httpRequestWithToken(
            $this->getRegularUser()
        )->get(route('order.index'))
            ->assertStatus(200);
    }

    public function test_show_single()
    {
        $user = $this->getRegularUser();
        $order = Order::factory()->complete()->create(['user_id' => $user->id]);

        $this->httpRequestWithToken($user)
            ->get(route('order.show', ['order' => $order->uuid]))
            ->assertStatus(200);
    }

    public function test_put_admin_user()
    {
        $user = $this->getAdminUser();
        $order = Order::factory()->complete()->create(['user_id' => $user->id]);
        $this->httpRequestWithToken(
            $user
        )->put(
            route(
                'order.update',
                [
                    'order' => $order->uuid,
                ]
            ),
            ['order' => $order->uuid]
        )->assertStatus(401);
    }

    public function test_put_regular_user()
    {
        $user = $this->getRegularUser();
        $payment = Payment::factory()->create();
        $oderStatus = OrderStatus::factory()->create();
        $order = Order::factory()->complete()->create(['user_id' => $user->id]);
        $product = Product::factory()->complete()->create();

        $response = $this->httpRequestWithToken(
            $user
        )->put(
            route(
                'order.update',
                [
                    'order' => $order->uuid,
                ]
            ),
            [
                'order_status_uuid' => $oderStatus->uuid,
                'payment_uuid' => $payment->uuid,
                'amount' => '123123123',
                'products' => json_encode(
                    ['product' => $product->uuid, 'quantity' => rand(1, 20)]
                ),
                'address' => json_encode([
                    'billing' => '123',
                    'shipping' => '123',
                ]),
            ]
        );

        $response->assertStatus(200);
        self::assertEquals(Order::where('id', '=', $order->id)->first()->amount, '123123123');
    }

    public function test_delete()
    {
        $user = $this->getRegularUser();
        $order = Order::factory()->complete()->create(['user_id' => $user->id]);
        $response = $this->httpRequestWithToken(
            $user
        )->delete(
            route(
                'order.destroy',
                [
                    'order' => $order->uuid,
                ]
            )
        );
        $response->assertStatus(200);
        self::assertEquals(Order::where('id', '=', $order->id)->count(), 0);
    }

    public function test_download()
    {
        $user = $this->getRegularUser();
        $order = Order::factory()->complete()->create(['user_id' => $user->id]);
        $response = $this->httpRequestWithToken(
            $user
        )->get(
            route(
                'orders.download',
                [
                    'order' => $order->uuid,
                ]
            )
        );
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
