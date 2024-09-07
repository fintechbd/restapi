<?php

namespace Fintech\RestApi\Http\Controllers\Transaction\Charts;

use Fintech\Auth\Facades\Auth;
use Fintech\RestApi\Http\Resources\Transaction\Charts\UserAccountSummaryCollection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserAccountSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $users = Auth::user()->list([
            'role_id_not_in' => [1, 2],
            'paginate' => false,
            'limit' => 20,
        ]);

        return new UserAccountSummaryCollection($users);
    }
}
