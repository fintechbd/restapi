<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Supports\Utility;
use Fintech\RestApi\Http\Requests\Business\ServiceTypeListRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceTypeListCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class AvailableServiceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ServiceTypeListRequest $request): ServiceTypeListCollection|JsonResponse
    {
        try {
            $input = $request->validated();

            //            $input['user_id'] = ($request->filled('user_id'))
            //                ? $request->input('user_id')
            //                : auth()->id();

            $input['role_id'] = ($request->filled('role_id'))
                ? $request->input('role_id')
                : auth()->user()?->roles?->first()?->getKey() ?? config('fintech.auth.customer_roles', [7])[0];

            if ($request->filled('reload') && $request->boolean('reload')) {
                $input['destination_country_id'] = $input['source_country_id'];
            }

            if ($request->filled('service_type_parent_slug')) {
                $serviceType = Business::serviceType()->findWhere(['service_type_slug' => $input['service_type_parent_slug'], 'get' => ['service_types.id']]);
                $input['service_type_parent_id'] = $serviceType->id;
            } elseif ($request->filled('service_type_parent_id')) {
                $input['service_type_parent_id'] = $request->input('service_type_parent_id');
            } else {
                $input['service_type_parent_id_is_null'] = true;
            }

            return  Cache::remember(
                $this->cacheIdentifier($input),
                (App::environment('production') ? HOUR : 0),
//                HOUR,
                function () use ($input, $request) {
                    $serviceTypes = Business::serviceType()->available($input);
                    return (new ServiceTypeListCollection($serviceTypes))->toResponse($request);
                });

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    private function cacheIdentifier(array $inputs = []): string
    {
        $id = ['services'];
        foreach ($inputs as $key => $value) {
            $id[] = "{$key}-".Utility::stringify(gettype($value), $value);
        }

        return implode('-', $id);
    }
}
