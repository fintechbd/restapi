<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
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
    public function __invoke(Request $request): JsonResponse
    {
        try {

            $ipinfo = Auth::geoip()->find($request->filled('ip') ? $request->input('ip') : $request->ip());

            return response()->success(['data' => $ipinfo]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    private function validTimezone(Request $request): bool
    {
        return true;
    }

    private function validHeaders(Request $request): bool
    {
        return true;
    }
}
