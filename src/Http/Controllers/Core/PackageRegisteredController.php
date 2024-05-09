<?php

namespace Fintech\RestApi\Http\Controllers\Core;

use Fintech\RestApi\Http\Resources\Core\PackageCollection;
use Fintech\RestApi\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PackageRegisteredController extends Controller
{
    use ApiResponseTrait;

    public function __invoke(Request $request): PackageCollection
    {
        $packages = collect(config('fintech.core.packages'))
            ->map(function ($package, $code) {
                return [
                    'name' => $package,
                    'code' => $code,
                    'enabled' => config("fintech.{$code}.enabled", false),
                ];
            })
            ->values();

        return new PackageCollection($packages);
    }
}
