<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\ImportCurrencyRateRequest;
use Fintech\RestApi\Http\Requests\Business\IndexCurrencyRateRequest;
use Fintech\RestApi\Http\Requests\Business\StoreCurrencyRateRequest;
use Fintech\RestApi\Http\Requests\Business\UpdateCurrencyRateRequest;
use Fintech\RestApi\Http\Resources\Business\CurrencyRateCollection;
use Fintech\RestApi\Http\Resources\Business\CurrencyRateResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class CurrencyRateController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to CurrencyRate
 *
 * @lrd:end
 */
class CurrencyRateController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *CurrencyRate* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexCurrencyRateRequest $request): CurrencyRateCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $currencyRatePaginate = Business::currencyRate()->list($inputs);

            return new CurrencyRateCollection($currencyRatePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *CurrencyRate* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreCurrencyRateRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $currencyRate = Business::currencyRate()->create($inputs);

            if (! $currencyRate) {
                throw (new StoreOperationException)->setModel(config('fintech.business.currency_rate_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Currency Rate']),
                'id' => $currencyRate->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *CurrencyRate* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): CurrencyRateResource|JsonResponse
    {
        try {

            $currencyRate = Business::currencyRate()->find($id);

            if (! $currencyRate) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.currency_rate_model'), $id);
            }

            return new CurrencyRateResource($currencyRate);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *CurrencyRate* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateCurrencyRateRequest $request, string|int $id): JsonResponse
    {
        try {

            $currencyRate = Business::currencyRate()->find($id);

            if (! $currencyRate) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.currency_rate_model'), $id);
            }

            $inputs = $request->validated();

            if (! Business::currencyRate()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.currency_rate_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Currency Rate']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *CurrencyRate* resource using id.
     *
     * @lrd:end
     *
     * @return JsonResponse
     *
     * @throws ModelNotFoundException
     * @throws DeleteOperationException
     */
    public function destroy(string|int $id)
    {
        try {

            $currencyRate = Business::currencyRate()->find($id);

            if (! $currencyRate) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.currency_rate_model'), $id);
            }

            if (! Business::currencyRate()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.business.currency_rate_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Currency Rate']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *CurrencyRate* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $currencyRate = Business::currencyRate()->find($id, true);

            if (! $currencyRate) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.currency_rate_model'), $id);
            }

            if (! Business::currencyRate()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.business.currency_rate_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Currency Rate']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *CurrencyRate* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexCurrencyRateRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $currencyRatePaginate = Business::currencyRate()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Currency Rate']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *CurrencyRate* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return CurrencyRateCollection|JsonResponse
     */
    public function import(ImportCurrencyRateRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $currencyRatePaginate = Business::currencyRate()->list($inputs);

            return new CurrencyRateCollection($currencyRatePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
