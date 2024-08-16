<?php

namespace Fintech\RestApi\Http\Controllers\Transaction\Charts;

use Exception;
use Fintech\RestApi\Http\Requests\Transaction\Charts\UserAccountUsageRequest;
use Fintech\RestApi\Http\Resources\Transaction\Charts\UserAccountUsageResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserAccountUsagePieChartController extends Controller
{
    public function __invoke(UserAccountUsageRequest $request): UserAccountUsageResource|JsonResponse
    {
        try {
            $filters = [
                'user_id' => $request->input('user_id'),
                'created_at_start_date' => now()->subDays($request->input('duration'))->format('Y-m-d'),
                'created_at_end_date' => now()->format('Y-m-d'),
                'sum_converted_amount' => true,
                'order_type' => $request->input('type', 'transfer'),
                'paginate' => false,
                'sort' => 'orders.currency',
                'dir' => 'asc',
            ];

            $orderSum = Transaction::order()->list($filters);

            return new UserAccountUsageResource($orderSum);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
