<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\MetaData\Facades\MetaData;
use Fintech\RestApi\Http\Requests\Business\ImportServiceStatRequest;
use Fintech\RestApi\Http\Requests\Business\IndexServiceStatRequest;
use Fintech\RestApi\Http\Requests\Business\StoreServiceStatRequest;
use Fintech\RestApi\Http\Requests\Business\UpdateServiceStatRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceStatCollection;
use Fintech\RestApi\Http\Resources\Business\ServiceStatResource;
use Fintech\RestApi\Http\Resources\MetaData\CountryCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class ServiceStatController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ServiceStat
 *
 * @lrd:end
 */
class ServiceStatController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ServiceStat* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexServiceStatRequest $request): ServiceStatCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceStatPaginate = Business::serviceStat()->list($inputs);

            return new ServiceStatCollection($serviceStatPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ServiceStat* resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreServiceStatRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();
            $serviceStat = Business::serviceStat()->customStore($inputs);

            if (! $serviceStat) {
                throw (new StoreOperationException)->setModel(config('fintech.business.service_stat_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Service Stat']),
                'id' => $serviceStat,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ServiceStat* resource found by id.
     *
     * @lrd:end
     */
    public function show(string|int $id): ServiceStatResource|JsonResponse
    {
        try {

            $serviceStat = Business::serviceStat()->find($id);

            if (! $serviceStat) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_stat_model'), $id);
            }

            return new ServiceStatResource($serviceStat);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ServiceStat* resource using id.
     *
     * @lrd:end
     */
    public function update(UpdateServiceStatRequest $request, string|int $id): JsonResponse
    {
        try {

            $serviceStat = Business::serviceStat()->find($id);

            if (! $serviceStat) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_stat_model'), $id);
            }

            $inputs = $request->validated();

            if (! Business::serviceStat()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.service_stat_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Service Stat']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ServiceStat* resource using id.
     *
     * @lrd:end
     */
    public function destroy(string|int $id): JsonResponse
    {
        try {

            $serviceStat = Business::serviceStat()->find($id);

            if (! $serviceStat) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_stat_model'), $id);
            }

            if (! Business::serviceStat()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.business.service_stat_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Service Stat']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ServiceStat* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     */
    public function restore(string|int $id): JsonResponse
    {
        try {

            $serviceStat = Business::serviceStat()->find($id, true);

            if (! $serviceStat) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_stat_model'), $id);
            }

            if (! Business::serviceStat()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.business.service_stat_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Service Stat']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *ServiceStat* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexServiceStatRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            Business::serviceStat()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Service Stat']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *ServiceStat* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportServiceStatRequest $request): JsonResponse|ServiceStatCollection
    {
        try {
            $inputs = $request->validated();

            $serviceStatPaginate = Business::serviceStat()->list($inputs);

            return new ServiceStatCollection($serviceStatPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    public function serviceStatWiseCountry(Request $request): CountryCollection|JsonResponse
    {
        try {
            $inputs = $request->all();
            $inputs['sort'] = 'destination_country_id';
            $inputs['paginate'] = false;

            $destination_countries = Business::serviceStat()->list($inputs)->toArray();

            $list = array_unique(array_column($destination_countries, $inputs['sort']));
            $countries = MetaData::country()->list(['in_array_country_id' => array_values($list)]);

            return new CountryCollection($countries);
        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
