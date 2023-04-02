<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
//        $this->reportable(function (Throwable $e) {
//            //
//        });
    }

    /**
     * @param Request $request
     * @param Throwable $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, $e): Response
    {
        if ($e instanceof ValidationException) {
            return $this->renderValidationError($e);
        }
        if ($e instanceof ModelNotFoundException) {
            return $this->renderModelNotFoundError($e);
        }

        return parent::render($request, $e);
    }

    private function renderValidationError(ValidationException $e): JsonResponse
    {
        return \Illuminate\Http\Response::api(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            0,
            [],
            'Failed Validation',
            $e->validator->errors(),
        );
    }

    private function renderModelNotFoundError(ModelNotFoundException $e): JsonResponse
    {
        $model = explode('\\', $e->getModel());
        $model = $model[count($model) - 1];

        return \Illuminate\Http\Response::api(
            Response::HTTP_NOT_FOUND,
            0,
            [],
            $model . ' not found',
            [],
        );
    }
}
