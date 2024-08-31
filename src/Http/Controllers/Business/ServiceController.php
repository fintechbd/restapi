<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\ImportServiceRequest;
use Fintech\RestApi\Http\Requests\Business\IndexServiceRequest;
use Fintech\RestApi\Http\Requests\Business\ServiceRateRequest;
use Fintech\RestApi\Http\Requests\Business\StoreServiceRequest;
use Fintech\RestApi\Http\Requests\Business\UpdateServiceRequest;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceCollection;
use Fintech\RestApi\Http\Resources\Business\ServiceCostResource;
use Fintech\RestApi\Http\Resources\Business\ServiceResource;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ServiceController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Service
 *
 * @lrd:end
 */
class ServiceController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Service* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexServiceRequest $request): ServiceCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $servicePaginate = Business::service()->list($inputs);

            return new ServiceCollection($servicePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *Service* resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $service = Business::service()->create($inputs);

            if (! $service) {
                throw (new StoreOperationException)->setModel(config('fintech.business.service_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Service']),
                'id' => $service->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Service* resource found by id.
     *
     * @lrd:end
     */
    public function show(string|int $id): ServiceResource|JsonResponse
    {
        try {

            $service = Business::service()->find($id);

            if (! $service) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_model'), $id);
            }

            return new ServiceResource($service);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *Service* resource using id.
     *
     * @lrd:end
     */
    public function update(UpdateServiceRequest $request, string|int $id): JsonResponse
    {
        try {

            $service = Business::service()->find($id);

            if (! $service) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_model'), $id);
            }

            $inputs = $request->validated();

            if (! Business::service()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.service_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Service']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Service* resource using id.
     *
     * @lrd:end
     */
    public function destroy(string|int $id): JsonResponse
    {
        try {

            $service = Business::service()->find($id);

            if (! $service) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_model'), $id);
            }

            if (! Business::service()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.business.service_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Service']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Service* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     */
    public function restore(string|int $id): JsonResponse
    {
        try {

            $service = Business::service()->find($id, true);

            if (! $service) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_model'), $id);
            }

            if (! Business::service()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.business.service_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Service']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *Service* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexServiceRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            //$servicePaginate = Business::service()->export($inputs);
            Business::service()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Service']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *Service* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportServiceRequest $request): ServiceCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $servicePaginate = Business::service()->list($inputs);

            return new ServiceCollection($servicePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    public function cost(ServiceRateRequest $request): JsonResponse|ServiceCostResource
    {
        $inputs = $request->validated();

        try {

            if (! $request->filled('user_id')) {
                $inputs['user_id'] = auth()->id();
            }

            if ($user = Auth::user()->find($inputs['user_id'])) {
                $inputs['role_id'] = $user->roles->first()?->getKey() ?? null;
            }

            $exchangeRate = Business::serviceStat()->cost($inputs);

            return new ServiceCostResource($exchangeRate);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @LRDparam country_id required|integer|min:1
     * @LRDparam state_id required|integer|min:1
     */
    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $filters['enabled'] = $filters['enabled'] ?? true;

            $label = 'name';

            $attribute = 'id';

            if (! empty($filters['label'])) {
                $label = $filters['label'];
                unset($filters['label']);
            }

            if (! empty($filters['attribute'])) {
                $attribute = $filters['attribute'];
                unset($filters['attribute']);
            }

            $entries = Business::service()->list($filters)->map(function ($entry) use ($label, $attribute) {
                return [
                    'attribute' => $entry->{$attribute} ?? 'id',
                    'label' => $entry->{$label} ?? 'name',
                ];
            })->toArray();

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception);
        }
    }
}
