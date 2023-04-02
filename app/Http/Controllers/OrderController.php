<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\CreateRequest;
use App\Http\Requests\Order\ShipmentLocatorRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Services\Paginator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use OpenApi\Annotations as OA;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function index(Request $request, Paginator $paginator): LengthAwarePaginator
    {
        $data = $paginator->paginateRequest($request, Order::query());
        $data->getCollection()->transform(function ($value) {
            return new OrderResource($value);
        });
        return $data;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="order_status_uuid",
     *                     type="string",
     *                     description="order status uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="payment_uuid",
     *                     type="string",
     *                     description="payment uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     description="array of products",
     *                     @OA\Items(
     *                        type="object",
     *                        @OA\Property(
     *                             property="product",
     *                             type="string"
     *                         ),
     *                        @OA\Property(
     *                             property="quantity",
     *                             type="integer"
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="object",
     *                     description="shipping and billing address",
     *                         @OA\Property(
     *                             property="shipping",
     *                             type="string"
     *                         ),
     *                        @OA\Property(
     *                             property="billing",
     *                             type="string"
     *                         ),
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer",
     *                     description="amount"
     *                 ),
     *                 required={"order_status_uuid","payment_uuid","products","address","amount"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     *
     * @throws Throwable
     */

    public function store(CreateRequest $request)
    {
        $attributes = $request->safe()->all();

        $status = OrderStatus::where('uuid', '=', $attributes['order_status_uuid'])->first();
        $payment = Payment::where('uuid', '=', $attributes['payment_uuid'])->first();

        $order = $request->user()->orders()->create(
            [
                'products' => json_encode($attributes['products'], true),
                'address' => json_encode($attributes['address'], true),
                'amount' => $attributes['amount'],
                'order_status_id' => $status->id,
                'payment_id' => $payment->id
            ]
        );
        return Response::api(HttpResponse::HTTP_OK, '1', new OrderResource($order));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/order/{uuid}",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function show(Order $order)
    {
        return Response::api(HttpResponse::HTTP_OK, '1', new OrderResource($order));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/order/{uuid}",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="order_status_uuid",
     *                     type="string",
     *                     description="order status uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="payment_uuid",
     *                     type="string",
     *                     description="payment uuid"
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     description="array of products",
     *                     @OA\Items(
     *                        type="object",
     *                        @OA\Property(
     *                             property="product",
     *                             type="string"
     *                         ),
     *                        @OA\Property(
     *                             property="quantity",
     *                             type="integer"
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="object",
     *                     description="shipping and billing address",
     *                         @OA\Property(
     *                             property="shipping",
     *                             type="string"
     *                         ),
     *                        @OA\Property(
     *                             property="billing",
     *                             type="string"
     *                         ),
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer",
     *                     description="amount"
     *                 ),
     *                 required={"order_status_uuid","payment_uuid","products","address","amount"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     *
     * @throws Throwable
     */
    public function update(UpdateRequest $request, Order $order): JsonResponse
    {
        $attributes = $request->safe()->all();

        $status = OrderStatus::where('uuid', '=', $attributes['order_status_uuid'])->first();
        $payment = Payment::where('uuid', '=', $attributes['payment_uuid'])->first();

        $order->update(
            [
                'products' => json_encode($attributes['products'], true),
                'address' => json_encode($attributes['address'], true),
                'amount' => $attributes['amount'],
                'order_status_id' => $status->id,
                'payment_id' => $payment->id
            ]
        );
        return Response::api(HttpResponse::HTTP_OK, 1, new OrderResource($order));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/order/{uuid}",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return Response::api(HttpResponse::HTTP_OK, 1, []);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{uuid}/download",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function download(Order $order): Response
    {
        $products = json_decode($order->products, true);

        $productsAndQuantity = array_map(function ($product) {
            return
                [
                    'product' => Product::firstWhere('uuid', '=', $product['product']),
                    'quantity' => $product['quantity']
                ];
        }, $products);

        $data = [
            'user' => $order->user,
            'productsAndQuantity' => $productsAndQuantity,
            'address' => json_decode($order->address),
            'amount' => $order->amount,
            'uuid' => $order->uuid
        ];

        $pdf = Pdf::loadView('pdf.order', $data);

        return $pdf->download("{$order->uuid}.pdf");
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/shipment-locator",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         description="Sort direction (true for descending, false for ascending)",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="orderUuid",
     *         in="query",
     *         description="Order UUID",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="customerUuid",
     *         in="query",
     *         description="Customer UUID",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="dateRange",
     *         in="query",
     *         description="Date range",
     *         required=false,
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="from",
     *                 description="Start date",
     *                 type="string",
     *                 format="date"
     *             ),
     *             @OA\Property(
     *                 property="to",
     *                 description="End date",
     *                 type="string",
     *                 format="date"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */

    public function shipmentLocator(
        ShipmentLocatorRequest $request,
        Paginator $paginator
    ): Collection {
        $query = Order::query();

        $query = $this->filterByUserUUID($request, $query);
        $query = $this->filterByDateRange($request, $query);
        $query = $this->filterByFixedRange($request, $query);

        $data = $paginator->paginateRequest($request, $query);
        return $data->getCollection()->transform(function ($value) {
            return new OrderResource($value);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/dashboard",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function dashboard(Request $request, Paginator $paginator): Collection
    {
        $query = Order::query();

        $query = $this->filterByDateRange($request, $query);
        $query = $this->filterByFixedRange($request, $query);

        $data = $paginator->paginateRequest($request, $query);
        return $data->getCollection()->transform(function ($value) {
            return new OrderResource($value);
        });
    }

    private function filterByUserUUID(
        Request $request,
        Builder $query
    ): Builder {
        if ($request->has('orderUuid')) {
            $query->where('uuid', '=', $request->input('orderUuid'));
        }
        return $query;
    }

    private function filterByDateRange(
        ShipmentLocatorRequest $request,
        Builder $query
    ): Builder {
        if ($request->has('dateRange')) {
            $query->whereBetween(
                'created_at',
                [
                    Carbon::parse($request->input('dateRange')['from']),
                    Carbon::parse($request->input('dateRange')['to'])
                ]
            );
        }
        return $query;
    }

    private function filterByFixedRange(
        ShipmentLocatorRequest $request,
        Builder $query
    ): Builder {
        if ($request->has('fixedRange')) {
            switch ($request->input('fixedRange')) {
                case 'today':
                    $query->where('created_at', '=', Carbon::today());
                    break;
                case 'monthly':
                    $query->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::now()]);
                    break;
                case 'yearly':
                    $query->whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()]);
                    break;
            }
        }
        return $query;
    }
}
