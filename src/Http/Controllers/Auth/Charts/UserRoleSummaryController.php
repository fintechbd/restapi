<?php

namespace Fintech\RestApi\Http\Controllers\Auth\Charts;

use Fintech\Auth\Facades\Auth;
use Fintech\RestApi\Http\Resources\Auth\Charts\UserRoleSummaryCollection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserRoleSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $roles = Auth::role()->list([
            'id_not_in' => [1, 2],
            'count_user' => true,
            'paginate' => false,
        ]);

        return new UserRoleSummaryCollection($roles);
    }
}
