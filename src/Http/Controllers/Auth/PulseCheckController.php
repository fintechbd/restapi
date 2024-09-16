<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\RestApi\Http\Resources\Auth\PulseCheckResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class PulseCheckController
 *
 * @lrd:start
 * This class handle server pulse checker
 *
 * @lrd:end
 */
class PulseCheckController extends Controller
{
    /**
     * @LRDparam ip string|nullable
     * @LRDparam datetime string|nullable
     *
     * @lrd:start
     * This api endpoint will check server status and client user agent integrity
     *
     * @lrd:end
     */
    public function __invoke(Request $request): JsonResponse|PulseCheckResource
    {
        try {

            $info = (object)Auth::geoip()->find($request->filled('ip') ? $request->input('ip') : $request->ip());

            return new PulseCheckResource($info);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
