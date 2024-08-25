<?php

namespace Fintech\RestApi\Http\Controllers\Reload\Charts;

use App\Http\Controllers\Controller;
use Fintech\RestApi\Http\Resources\Reload\Charts\DepositPartnerCollection;
use Illuminate\Http\Request;

class DepositPartnerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $collection = collect([
            ['Mode' => 'Bank Account', 'Bank Name' => 'RBC', 'Bank A/C No' => '15787477', 'Limits (CAD)' => '1000', 'Fees/Charges' => '2.50'],
            ['Mode' => 'Bank Account', 'Bank Name' => 'CIBC', 'Bank A/C No' => '9807979991', 'Limits (CAD)' => '1500', 'Fees/Charges' => '2.00'],
            ['Mode' => 'Card', 'Bank Name' => 'Credit Card', 'Bank A/C No' => '........895', 'Limits (CAD)' => '500', 'Fees/Charges' => '1.20%'],
            ['Mode' => 'Paypal', 'Bank Name' => 'Paypal', 'Bank A/C No' => '1223341334', 'Limits (CAD)' => '2000', 'Fees/Charges' => '0.8%'],
            ['Mode' => 'Paypal', 'Bank Name' => 'Paypal', 'Bank A/C No' => '1223341334', 'Limits (CAD)' => '2000', 'Fees/Charges' => '0.8%'],
        ]);

        return new DepositPartnerCollection($collection);
    }
}
