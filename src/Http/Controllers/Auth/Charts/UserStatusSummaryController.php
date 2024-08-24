<?php

namespace Fintech\RestApi\Http\Controllers\Auth\Charts;

use App\Http\Controllers\Controller;
use Fintech\RestApi\Http\Resources\Auth\Charts\UserStatusSummaryCollection;
use Illuminate\Http\Request;

class UserStatusSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->mergeIfMissing([
            'role_id_not_in' => [1, 2],
            'count_user_status' => true,
            'paginate' => false,
            'sort' => 'count',
            'dir' => 'desc',
        ]);
        $users = \Fintech\Auth\Facades\Auth::user()->list($request->all());

        return new UserStatusSummaryCollection($users);
    }
}
