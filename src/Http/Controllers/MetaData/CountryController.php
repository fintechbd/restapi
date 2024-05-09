<?php

namespace Fintech\RestApi\Http\Controllers\MetaData;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\MetaData\Facades\MetaData;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Requests\MetaData\ImportCountryRequest;
use Fintech\RestApi\Http\Requests\MetaData\IndexCountryRequest;
use Fintech\RestApi\Http\Requests\MetaData\StoreCountryRequest;
use Fintech\RestApi\Http\Requests\MetaData\UpdateCountryRequest;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Fintech\RestApi\Http\Resources\MetaData\CountryCollection;
use Fintech\RestApi\Http\Resources\MetaData\CountryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class CountryController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to country
 *
 * @lrd:end
 */
class CountryController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the country resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexCountryRequest $request): CountryCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $countryPaginate = MetaData::country()->list($inputs);

            return new CountryCollection($countryPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new country resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreCountryRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $country = MetaData::country()->create($inputs);

            if (!$country) {
                throw (new StoreOperationException())->setModel(config('fintech.metadata.country_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Country']),
                'id' => $country->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @LRDparam trashed boolean|nullable
     *
     * @lrd:start
     * Return a specified country resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): CountryResource|JsonResponse
    {
        try {

            $country = MetaData::country()->find($id);

            if (!$country) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.country_model'), $id);
            }

            return new CountryResource($country);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified country resource using id.
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

            $country = MetaData::country()->find($id);

            if (!$country) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.country_model'), $id);
            }

            if (!MetaData::country()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.metadata.country_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Country']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified country resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $country = MetaData::country()->find($id, true);

            if (!$country) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.country_model'), $id);
            }

            if (!MetaData::country()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.metadata.country_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Country']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the country resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexCountryRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $countryPaginate = MetaData::country()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Country']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the country resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return CountryCollection|JsonResponse
     */
    public function import(ImportCountryRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $countryPaginate = MetaData::country()->list($inputs);

            return new CountryCollection($countryPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $label = 'name';

            $attribute = 'id';

            if (!empty($filters['label'])) {
                $label = $filters['label'];
                unset($filters['label']);
            }

            if (!empty($filters['attribute'])) {
                $attribute = $filters['attribute'];
                unset($filters['attribute']);
            }

            $entries = MetaData::country()->list($filters)->map(function ($entry) use ($label, $attribute) {
                return [
                    'attribute' => $entry->{$attribute} ?? 'id',
                    'label' => $entry->{$label} ?? 'name',
                ];
            })->toArray();

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified country as serving or not.
     *
     * @lrd:end
     *
     * N.B after toggle update actions follow
     *
     * @throws ModelNotFoundException
     *
     * @see \Fintech\MetaData\Observers\CountryObserver
     */
    public function toggleServingCountry(string|int $id): JsonResponse
    {
        try {

            $country = MetaData::country()->find($id);

            if (!$country) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.country_model'), $id);
            }

            $countryData = $country->country_data;

            $countryData['is_serving'] = !($countryData['is_serving'] ?? false);

            //N.B after toggle update actions check Country Observer
            if (!MetaData::country()->update($id, ['country_data' => $countryData])) {
                throw (new UpdateOperationException())->setModel(config('fintech.metadata.country_model'), $id);
            }

            return response()->updated(__('metadata::messages.country.status_changed', ['field' => 'Serving Country']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified country resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateCountryRequest $request, string|int $id): JsonResponse
    {
        try {

            $country = MetaData::country()->find($id);

            if (!$country) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.country_model'), $id);
            }

            $inputs = $request->validated();

            if (!MetaData::country()->update($id, $inputs)) {

                throw (new UpdateOperationException())->setModel(config('fintech.metadata.country_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Country']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
