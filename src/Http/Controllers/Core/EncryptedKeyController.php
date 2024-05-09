<?php

namespace Fintech\RestApi\Http\Controllers\Core;

use Fintech\Core\Supports\Encryption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EncryptedKeyController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->success([
            'data' => [
                'status' => config('fintech.core.encrypt_response'),
                'token' => base64_encode(
                    Encryption::key()
                ),
            ],
        ]);
    }
}
