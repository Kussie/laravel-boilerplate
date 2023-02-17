<?php

namespace App\Http\Controllers\Traits;

use Error;
use ErrorException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

trait HasApiResponses
{
    protected function handleError(Throwable|Exception $e): JsonResponse
    {
        return match (true) {
            $e instanceof ValidationException => $this->respondError($e->getMessage()),
            $e instanceof AuthenticationException => $this->respondForbidden('Unauthenticated'),
            $e instanceof AccessDeniedHttpException => $this->respondForbidden(),
            $e instanceof UnauthorizedException => $this->respondUnauthorized(),
            $e instanceof ErrorException,
            $e instanceof ModelNotFoundException,
            $e instanceof NotFoundHttpException => $this->respondNotFound('No such data found'),
            default => $this->respondErrorThrowable($e),
        };
    }

    /**
     * Respond with forbidden.
     */
    protected function respondForbidden(string $errorMessage = 'Forbidden'): JsonResponse
    {
        return $this->respondError($errorMessage, Response::HTTP_FORBIDDEN);
    }

    /**
     * Respond with error.
     *
     * @param null $exception
     */
    protected function respondError(string $errorMessage = 'Failed', int $statusCode = Response::HTTP_BAD_REQUEST, $exception = null, ?int $error_code = null, array $headers = []): JsonResponse
    {
        return $this->apiResponse(['result' => null, 'success' => false, 'message' => $errorMessage, 'exception' => $exception, 'error_code' => $error_code], $statusCode, $headers);
    }

    /**
     * Return generic json response with the given data.
     */
    protected function apiResponse(array $data = [], int $statusCode = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        return response()->json($this->parseGivenData($data, $headers), $statusCode);
    }

    public function parseGivenData(array $data = [], array $headers = []): array
    {
        request()->headers->set('Accept', 'application/json');

        $response = [
            'success' => $data['success'] ?? true,
            'message' => $data['message'] ?? null,
            'result' => $data['result'] ?? null,
        ];

        if (isset($data['errors'])) {
            $response['errors'] = $data['errors'];
        }

        if (
            isset($data['exception'])
            && ($data['exception'] instanceof Error || $data['exception'] instanceof Exception)
        ) {
            if (config('app.env') !== 'production') {
                $response['exception'] = [
                    'message' => $data['exception']->getMessage(),
                    'file' => $data['exception']->getFile(),
                    'line' => $data['exception']->getLine(),
                    'code' => $data['exception']->getCode(),
                    'trace' => $data['exception']->getTrace(),
                ];
            }
        }

        if ($response['success'] === false && isset($data['error_code']) && $data['error_code']) {
            $response['error_code'] = $data['error_code'];
        }

        if ($headers) {
            $response['headers'] = $headers;
        }

        return $response;
    }

    /**
     * Respond with unauthorized.
     */
    protected function respondUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->respondError($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Respond with not found.
     */
    protected function respondNotFound(string $errorMessage = 'Not Found'): JsonResponse
    {
        return $this->respondError($errorMessage, Response::HTTP_NOT_FOUND);
    }

    /**
     * Respond with error.
     */
    protected function respondErrorThrowable(Throwable $ex, string $message = 'Failed', int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->apiResponse(['success' => false, 'message' => $message, 'result' => ['code' => $ex->getCode(), 'message' => $ex->getMessage(), 'line' => $ex->getLine(), 'file' => $ex->getFile()]], $statusCode);
    }

    protected function respondSuccessWithData($data, string $successMessage = '', int $statusCode = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        return $this->apiResponse(['result' => $data, 'success' => true, 'message' => $successMessage], $statusCode, $headers);
    }

    protected function respondWithResource(JsonResource $resource, string $successMessage = '', int $statusCode = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        return $this->apiResponse(['result' => $resource, 'message' => $successMessage, 'success' => true], $statusCode, $headers);
    }

    /**
     * Respond with success.
     */
    protected function respondSuccess(string $successMessage = 'Success', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return $this->apiResponse(['success' => true, 'message' => $successMessage], $statusCode);
    }

    /**
     * Respond with error.
     */
    protected function respondErrorWithData($data, string $errorMessage = '', int $statusCode = Response::HTTP_BAD_REQUEST, array $headers = []): JsonResponse
    {
        return $this->apiResponse(['result' => $data, 'success' => false, 'message' => $errorMessage], $statusCode, $headers);
    }

    /**
     * Respond with not found.
     */
    protected function respondInternalServerError(Exception $ex): JsonResponse
    {
        return $this->respondErrorException($ex, 'Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Respond with error.
     */
    protected function respondErrorException(Exception $ex, string $message = 'Failed', int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->apiResponse(['success' => false, 'message' => $message, 'result' => ['code' => $ex->getCode(), 'message' => $ex->getMessage(), 'line' => $ex->getLine(), 'file' => $ex->getFile()]], $statusCode);
    }

    /**
     * Respond with not found.
     */
    protected function respondForbiddenError($errorMessage): JsonResponse
    {
        return $this->respondError($errorMessage, Response::HTTP_FORBIDDEN);
    }
}
