<?php

namespace Tests\Feature;

use App\Models\OrderStatus;
use App\Services\TokenService;

class OrderStatusesTest extends Base
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $response = $this->get(route('order-status.index'));

        $response->assertStatus(200);
    }

    public function test_show_single()
    {
        $orderStatus = OrderStatus::factory()->create();
        $response = $this->get(route('order-status.show', ['order_status' => $orderStatus->uuid]));
        $response->assertStatus(200);
    }

    public function test_put_admin_user()
    {
        $orderStatus = OrderStatus::factory()->create();
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getAdminUser())
        )->put(
            route('order-status.update', ['order_status' => $orderStatus->uuid]),
            ['title' => '123']
        )->assertStatus(401);
    }

    public function test_put_regular_user()
    {
        $orderStatus = OrderStatus::factory()->create();
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getRegularUser(), true)
        )->put(
            route('order-status.update', ['order_status' => $orderStatus->uuid]),
            [
                'title' => '123123123',
            ]
        )->assertStatus(200);
        self::assertEquals(OrderStatus::find($orderStatus)->first()->title, '123123123');
    }
}
