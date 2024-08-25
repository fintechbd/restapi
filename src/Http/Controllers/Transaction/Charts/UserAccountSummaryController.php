<?php

namespace Fintech\RestApi\Http\Controllers\Transaction\Charts;

use App\Http\Controllers\Controller;
use Fintech\RestApi\Http\Resources\Transaction\Charts\UserAccountSummaryCollection;
use Illuminate\Http\Request;

class UserAccountSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $users = \Fintech\Auth\Facades\Auth::user()->list([
            'role_id_not_in' => [1,2],
            'paginate' => false,
            'limit' => 20,
            ]);

        return new UserAccountSummaryCollection($users);
    }
}
