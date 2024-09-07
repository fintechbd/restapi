<?php

namespace Fintech\RestApi\Http\Controllers\Reload\Charts;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WithdrawPartnerController extends Controller
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
