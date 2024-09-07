<?php

namespace Fintech\RestApi\Http\Controllers\Airtime;

use Illuminate\Routing\Controller;
use Fintech\RestApi\Http\Requests\Airtime\PhoneNumberDetectRequest;
use Fintech\RestApi\Http\Resources\Airtime\PhoneNumberDetectResource;

class PhoneNumberDetectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(PhoneNumberDetectRequest $request): PhoneNumberDetectResource
    {
        $response = [
            'service_slug' => 'grameen_phone_bd',
            'connection_type' => 'prepaid',
            'valid' => true,
        ];

        return new PhoneNumberDetectResource($response);
    }
}
