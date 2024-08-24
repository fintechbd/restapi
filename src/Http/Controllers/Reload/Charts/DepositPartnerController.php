<?php

namespace Fintech\RestApi\Http\Controllers\Reload\Charts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepositPartnerController extends Controller
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
