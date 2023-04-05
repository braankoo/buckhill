<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\CreateRequest;
use App\Http\Requests\Payment\UpdateRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

final class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['jwt', 'jwt.auth', 'role:user']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payment",
     *     tags={"Payment"},
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
    public function index(Request $request, Paginator $paginator): Collection
    {
        $data = $paginator->paginateRequest($request, Payment::query());

        return $data->getCollection()->transform(function ($value) {
            return new PaymentResource($value);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payment",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     description="Type of payment",
     *                     enum={"cash_on_delivery", "credit_card", "bank_transfer"}
     *                 ),
     *                 @OA\Property(
     *                     property="details",
     *                     type="object",
     *                     description="Payment details",
     *                 ),
     *                 required={"type", "details"}
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
        $attributes = $request->safe()->all();
        $payment = Payment::create(
            [
                'type' => $attributes['type'],
                'details' => $attributes['details'],
            ]
        );

        return Response::api(
            HttpResponse::HTTP_OK,
            1,
            new PaymentResource($payment)
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payment/{uuid}",
     *     tags={"Payment"},
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
    public function show(Payment $payment): JsonResponse
    {
        return Response::api(HttpResponse::HTTP_OK, 1, new PaymentResource($payment));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/payment/{uuid}",
     *     tags={"Payment"},
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
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     description="Type of payment",
     *                     enum={"cash_on_delivery", "credit_card", "bank_transfer"}
     *                 ),
     *                 @OA\Property(
     *                     property="details",
     *                     type="object",
     *                     description="Payment details",
     *                 ),
     *                 required={"type", "details"}
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
    public function update(
        UpdateRequest $request,
        Payment $payment
    ): JsonResponse {
        $attributes = $request->safe()->all();
        $payment->update([
            'type' => $attributes['type'],
            'details' => $attributes['details'],
        ]);

        return Response::api(
            HttpResponse::HTTP_OK,
            1,
            new PaymentResource($payment)
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/payment/{uuid}",
     *     tags={"Payment"},
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
    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();

        return Response::api(HttpResponse::HTTP_OK, 1, []);
    }
}
