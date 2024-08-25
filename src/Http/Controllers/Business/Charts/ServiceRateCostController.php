<?php

namespace Fintech\RestApi\Http\Controllers\Business\Charts;

use App\Http\Controllers\Controller;
use Fintech\RestApi\Http\Resources\Business\Charts\ServiceRateCostCollection;
use Illuminate\Http\Request;

class ServiceRateCostController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): ServiceRateCostCollection
    {
        $rates = collect([
            ['Transaction Type' => 'Bank Transfer', 'Country/Currency' => 'BDT', 'Ex Rate' => '85.4', 'Fees/Charges' => '2.50'],
            ['Transaction Type' => 'Cash Pickup', 'Country/Currency' => 'BDT', 'Ex Rate' => '85.7', 'Fees/Charges' => '2.40'],
            ['Transaction Type' => 'Wallet', 'Country/Currency' => 'BDT', 'Ex Rate' => '86.00', 'Fees/Charges' => '2.60'],
            ['Transaction Type' => 'Bill Payment', 'Country/Currency' => 'BDT', 'Ex Rate' => '86.4', 'Fees/Charges' => '2.70'],
        ]);

        return new ServiceRateCostCollection($rates);
    }
}
