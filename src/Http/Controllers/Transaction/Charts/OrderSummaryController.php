<?php

namespace Fintech\RestApi\Http\Controllers\Transaction\Charts;

use Fintech\RestApi\Http\Resources\Transaction\Charts\OrderSummaryCollection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $orders = collect([
            ['service_type' => 'Bank Transfer', 'count' => '65', 'total' => '35700'],
            ['service_type' => 'Cash Pickup', 'count' => '15', 'total' => '12050'],
            ['service_type' => 'Wallet', 'count' => '29', 'total' => '2100'],
            ['service_type' => 'Bill Payment', 'count' => '5', 'total' => '21000'],
        ]);

        return new OrderSummaryCollection($orders);
    }
}
