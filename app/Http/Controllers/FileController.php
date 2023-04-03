<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\StoreRequest;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use InvalidArgumentException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['jwt', 'jwt.auth', 'role:user'])->only(['store']);
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
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $file = $this->getUploadedFile($request->file('file'));
        } catch (\InvalidArgumentException $e) {
            return Response::api(Response::HTTP_UNPROCESSABLE_ENTITY, 1, 'Wrong type');
        }

        $uuid = Str::uuid();
        $path = $file->storeAs('pet-shop', $uuid . '.' . $file->getClientOriginalExtension());
        $file = File::create(
            [
                'uuid' => $uuid,
                'name' => $file->getFilename(),
                'path' => $path,
                'size' => $file->getSize(),
                'type' => $file->getType(),
            ]
        );

        return Response::api(Response::HTTP_OK, 1, $file);
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

    /**
     * @param array<UploadedFile>|UploadedFile|null $file
     * @return UploadedFile
     */
    private function getUploadedFile(array|UploadedFile|null $file): UploadedFile
    {
        if ( ! $file instanceof UploadedFile) {
            throw new InvalidArgumentException();
        }

        return $file;
    }
}
