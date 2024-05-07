<?php

namespace Fintech\RestApi\Http\Controllers\Core;

use Fintech\Core\Supports\Encryption;
use Fintech\Core\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EncryptedKeyController extends Controller
{
    use ApiResponseTrait;

    public function __invoke(Request $request): JsonResponse
    {
        return $this->success([
            'data' => [
                'status' => config('fintech.core.encrypt_response'),
                'token' => base64_encode(
                    Encryption::key()
                ),
            ],
        ]);
    }
}
