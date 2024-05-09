<?php

namespace Fintech\RestApi\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait ApiResponseTrait
 */
trait ApiResponseTrait
{
    /**
     * return response with http 200 as deleted
     * resource
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function deleted($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_OK, $headers);
    }

    private function format($data, $statusCode = null)
    {
        if (is_string($data)) {
            $data = ['message' => $data];
            if ($statusCode != null) {
                $data['code'] = $statusCode;
            }
        }

        if (is_array($data) && !isset($data['code'])) {
            if ($statusCode != null) {
                $data['code'] = $statusCode;
            }
        }

        return $data;
    }

    /**
     * return response with http 200 as soft deleted
     * resource restored
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function restored($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_OK, $headers);
    }

    /**
     * return response with http 201 resource
     * created on server
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function created($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_CREATED, $headers);
    }

    /**
     * return response with http 200 update
     * request accepted
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function updated($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_OK, $headers);
    }

    /**
     * return response with http 202 export
     * request accepted
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function exported($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_ACCEPTED, $headers);
    }

    /**
     * return response with http 400 if business
     * logic exception
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function failed($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_BAD_REQUEST, $headers);
    }

    /**
     * return response with http 200 for all success status
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function success($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_OK, $headers);
    }

    /**
     * return response with http 401 if request
     * token or ip banned
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function banned($data, array $headers = []): JsonResponse
    {
        if (is_string($data)) {
            $data = ['message' => $data];
        }

        return response()->json($this->format($data), Response::HTTP_UNAUTHORIZED, $headers);
    }

    /**
     * return response with http 403 if access forbidden
     * to that request
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function forbidden($data, array $headers = []): JsonResponse
    {
        if (is_string($data)) {
            $data = ['message' => $data];
        }

        return response()->json($this->format($data), Response::HTTP_FORBIDDEN, $headers);
    }

    /**
     * return response with http 404 not found
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function notfound($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_NOT_FOUND, $headers);
    }

    /**
     * return response with http 423 attempt locked
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function locked($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_LOCKED, $headers);
    }

    /**
     * return response with http 429 too many requests code
     *
     * @param $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function overflow($data, array $headers = []): JsonResponse
    {
        return response()->json($this->format($data), Response::HTTP_TOO_MANY_REQUESTS, $headers);
    }
}
