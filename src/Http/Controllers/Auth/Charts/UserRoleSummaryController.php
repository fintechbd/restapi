<?php

namespace Fintech\RestApi\Http\Controllers\Auth\Charts;

use App\Http\Controllers\Controller;
use Fintech\RestApi\Http\Resources\Auth\Charts\UserRoleSummaryCollection;
use Illuminate\Http\Request;

class UserRoleSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $roles = \Fintech\Auth\Facades\Auth::role()->list([
            'count_user' => true,
            'paginate' => false,
        ]);

        return new UserRoleSummaryCollection($roles);
    }
}
