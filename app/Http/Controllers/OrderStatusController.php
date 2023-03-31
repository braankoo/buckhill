<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStatus\CreateRequest;
use App\Http\Requests\OrderStatus\StoreRequest;
use App\Http\Requests\OrderStatus\UpdateRequest;
use App\Models\OrderStatus;
use App\Services\Paginator;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class OrderStatusController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/order-status",
     *     tags={"Order Statuses"},
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
        return $paginator->paginateRequest($request, OrderStatus::query());
    }

    /**
     * @OA\Post(
     *     path="/api/v1/order-status",
     *     tags={"Order Statuses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="title",
     *                 ),
     *                 required={"title"}
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
    public function store(CreateRequest $request): JsonResponse
    {
        $orderStatus = OrderStatus::create($request->safe()->all());
        return Response::api(HttpResponse::HTTP_OK, 1, $orderStatus);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/order-status/{uuid}",
     *     tags={"Order Statuses"},
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
    public function show(OrderStatus $orderStatus): JsonResponse
    {
        return Response::api(HttpResponse::HTTP_OK, 1, $orderStatus);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/order-status/{uuid}",
     *     tags={"Order Statuses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="User Title",
     *                 ),
     *                 required={"title"}
     *             )
     *         )
     *    ),
     *    @OA\Response(
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
     * )
     */
    public
    function update(
        UpdateRequest $request,
        OrderStatus $orderStatus
    ): JsonResponse {
        $orderStatus->update($request->safe()->all());
        return Response::api(HttpResponse::HTTP_OK, 1, $orderStatus);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/order-status/{uuid}",
     *     tags={"Order Statuses"},
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
    public function destroy(OrderStatus $orderStatus): JsonResponse
    {
        $orderStatus->delete();
        return Response::api(HttpResponse::HTTP_OK, 1, []);
    }
}
