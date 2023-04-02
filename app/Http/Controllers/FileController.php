<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\StoreRequest;
use App\Models\File;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt')->only('store');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/file/upload",
     *     tags={"File"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="file",
     *                 ),
     *                 required={"file"}
     *             )
     *         )
     *    ),
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
    public function store(StoreRequest $request)
    {
        $file = $request->file('file');

        $uuid = Str::uuid();
        $path = $file->storeAs('pet-shop', $uuid . '.' . $file->getClientOriginalExtension());
        $file = File::create(
            [
                'uuid' => $uuid,
                'name' => $file->getFilename(),
                'path' => $path,
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ]
        );

        return Response::api(Response::HTTP_OK, '1', $file);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/file/{uuid}",
     *     tags={"File"},
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
    public function show(File $file): BinaryFileResponse
    {
        $headers = [
            'Content-Type' => $file->type,
        ];

        return response()->download(storage_path('app/' . $file->path), $file->name, $headers);
    }
}
