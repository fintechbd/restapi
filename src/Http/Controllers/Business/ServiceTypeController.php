<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\ImportServiceTypeRequest;
use Fintech\RestApi\Http\Requests\Business\IndexServiceTypeRequest;
use Fintech\RestApi\Http\Requests\Business\ServiceTypeListRequest;
use Fintech\RestApi\Http\Requests\Business\StoreServiceTypeRequest;
use Fintech\RestApi\Http\Requests\Business\UpdateServiceTypeRequest;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceTypeCollection;
use Fintech\RestApi\Http\Resources\Business\ServiceTypeListCollection;
use Fintech\RestApi\Http\Resources\Business\ServiceTypeResource;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ServiceTypeController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ServiceType
 *
 * @lrd:end
 */
class ServiceTypeController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ServiceType* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexServiceTypeRequest $request): ServiceTypeCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceTypePaginate = Business::serviceType()->list($inputs);

            return new ServiceTypeCollection($serviceTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *ServiceType* resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreServiceTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceType = Business::serviceType()->create($inputs);

            if (! $serviceType) {
                throw (new StoreOperationException)->setModel(config('fintech.business.service_type_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Service Type']),
                'id' => $serviceType->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *ServiceType* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ServiceTypeResource|JsonResponse
    {
        try {

            $serviceType = Business::serviceType()->find($id);

            if (! $serviceType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            return new ServiceTypeResource($serviceType);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *ServiceType* resource using id.
     *
     * @lrd:end
     */
    public function update(UpdateServiceTypeRequest $request, string|int $id): JsonResponse
    {
        try {

            $serviceType = Business::serviceType()->find($id);

            if (! $serviceType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            $inputs = $request->validated();

            if (! Business::serviceType()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Service Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ServiceType* resource using id.
     *
     * @lrd:end
     */
    public function destroy(string|int $id): JsonResponse
    {
        try {

            $serviceType = Business::serviceType()->find($id);

            if (! $serviceType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            if (! Business::serviceType()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Service Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ServiceType* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     */
    public function restore(string|int $id): JsonResponse
    {
        try {

            $serviceType = Business::serviceType()->find($id, true);

            if (! $serviceType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            if (! Business::serviceType()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Service Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *ServiceType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexServiceTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            //$serviceTypePaginate = Business::serviceType()->export($inputs);
            Business::serviceType()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Service Type']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *ServiceType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportServiceTypeRequest $request): ServiceTypeCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceTypePaginate = Business::serviceType()->list($inputs);

            return new ServiceTypeCollection($serviceTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    public function serviceTypeList(ServiceTypeListRequest $request): ServiceTypeListCollection|JsonResponse
    {
        try {
            $input = $request->validated();

            $input['user_id'] = ($request->filled('user_id'))
                ? $request->input('user_id')
                : auth()->id();

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

            $serviceTypes = Business::serviceType()->available($input);

            return new ServiceTypeListCollection($serviceTypes);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @LRDparam search nullable|string
     * @LRDparam service_type_parent_id nullable|integer
     * @LRDparam service_type_is_parent nullable|string|in:yes,no
     * @LRDparam service_type_name nullable|string
     * @LRDparam service_type_slug nullable|string
     * @LRDparam enabled nullable|boolean
     */
    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $filters['enabled'] = $filters['enabled'] ?? true;

            $label = 'service_type_name';

            $attribute = 'id';

            if (! empty($filters['label'])) {
                $label = $filters['label'];
                unset($filters['label']);
            }

            if (! empty($filters['attribute'])) {
                $attribute = $filters['attribute'];
                unset($filters['attribute']);
            }

            $entries = Business::serviceType()->list($filters)->map(function ($entry) use ($label, $attribute) {
                return [
                    'attribute' => $entry->{$attribute} ?? 'id',
                    'label' => $entry->{$label} ?? 'service_type_name',
                    'parents' => $entry->all_parent_list ?? [],
                ];
            })->toArray();

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception);
        }
    }
}
