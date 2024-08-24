<?php

namespace Fintech\RestApi\Http\Controllers\Auth\Charts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserStatusSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return response()->success([
            'data' => [],
            'query' => $request->all(),
        ]);
    }
}
