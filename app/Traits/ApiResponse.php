<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     */
    public function successResponse(string $message, mixed $data = [], int $code = Response::HTTP_OK): JsonResponse
    {
        if ($data instanceof JsonResource) {
            $data = $data->toArray(request());
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    public function errorResponse(string $message, int $code): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

    /**
     * Return an error message JSON response with custom header.
     */
    public function errorMessage(string $message, int $code): JsonResponse
    {
        return response()->json($message, $code)->header('Content-Type', 'application/json');
    }

    /**
     * Return a 201 Created response.
     */
    public function createdResponse($message, $data): JsonResponse
    {
        return $this->successResponse($message, $data, Response::HTTP_CREATED);
    }

    /**
     * Return a 204 No Content response.
     */
    public function noContentResponse(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Return a 404 Not Found response.
     */
    public function notFoundResponse($message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Return a 403 Forbidden response.
     */
    public function forbiddenResponse($message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Return a 401 Unauthorized response.
     */
    public function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Return a 422 Validation Error response.
     */
    public function validationErrorResponse(string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}