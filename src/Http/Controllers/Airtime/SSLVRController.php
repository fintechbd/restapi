<?php

namespace Fintech\RestApi\Http\Controllers\Airtime;

use Fintech\Airtime\Vendors\SSLVirtualRechargeApi;
use Illuminate\Routing\Controller;

class SSLVRController extends Controller
{
    public function __construct(readonly private SSLVirtualRechargeApi $SSLVirtualRechargeApi) {}

    public function test()
    {
        dd($this->SSLVirtualRechargeApi->test());
    }
}
