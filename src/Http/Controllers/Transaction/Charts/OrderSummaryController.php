<?php

namespace Fintech\RestApi\Http\Controllers\Transaction\Charts;

use App\Http\Controllers\Controller;
use Fintech\RestApi\Http\Resources\Transaction\Charts\OrderSummaryCollection;
use Illuminate\Http\Request;

class OrderSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $orders = collect([
            ['Transaction Type' => 'Bank Transfer', 'No of Transactions' => '65', 'Total Amount (CAD)' => '35700'],
            ['Transaction Type' => 'Cash Pickup', 'No of Transactions' => '15', 'Total Amount (CAD)' => '12050'],
            ['Transaction Type' => 'Wallet', 'No of Transactions' => '29', 'Total Amount (CAD)' => '2100'],
        ]);

        return new OrderSummaryCollection($orders);
    }
}
