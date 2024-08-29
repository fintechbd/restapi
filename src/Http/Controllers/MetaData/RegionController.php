<?php

namespace Fintech\RestApi\Http\Controllers\MetaData;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\MetaData\Facades\MetaData;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Requests\MetaData\ImportRegionRequest;
use Fintech\RestApi\Http\Requests\MetaData\IndexRegionRequest;
use Fintech\RestApi\Http\Requests\MetaData\StoreRegionRequest;
use Fintech\RestApi\Http\Requests\MetaData\UpdateRegionRequest;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Fintech\RestApi\Http\Resources\MetaData\RegionCollection;
use Fintech\RestApi\Http\Resources\MetaData\RegionResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class RegionController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to region
 *
 * @lrd:end
 */
class RegionController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the region resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexRegionRequest $request): RegionCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $regionPaginate = MetaData::region()->list($inputs);

            return new RegionCollection($regionPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new region resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreRegionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $region = MetaData::region()->create($inputs);

            if (! $region) {
                throw (new StoreOperationException)->setModel(config('fintech.metadata.region_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Region']),
                'id' => $region->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @LRDparam trashed boolean|nullable
     *
     * @lrd:start
     * Return a specified region resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): RegionResource|JsonResponse
    {
        try {

            $region = MetaData::region()->find($id);

            if (! $region) {
                throw (new ModelNotFoundException)->setModel(config('fintech.metadata.region_model'), $id);
            }

            return new RegionResource($region);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified region resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateRegionRequest $request, string|int $id): JsonResponse
    {
        try {

            $region = MetaData::region()->find($id);

            if (! $region) {
                throw (new ModelNotFoundException)->setModel(config('fintech.metadata.region_model'), $id);
            }

            $inputs = $request->validated();

            if (! MetaData::region()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.metadata.region_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Region']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified region resource using id.
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

            $region = MetaData::region()->find($id);

            if (! $region) {
                throw (new ModelNotFoundException)->setModel(config('fintech.metadata.region_model'), $id);
            }

            if (! MetaData::region()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.metadata.region_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Region']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified region resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $region = MetaData::region()->find($id, true);

            if (! $region) {
                throw (new ModelNotFoundException)->setModel(config('fintech.metadata.region_model'), $id);
            }

            if (! MetaData::region()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.metadata.region_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Region']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the region resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexRegionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $regionPaginate = MetaData::region()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Region']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the region resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return RegionCollection|JsonResponse
     */
    public function import(ImportRegionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $regionPaginate = MetaData::region()->list($inputs);

            return new RegionCollection($regionPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $filters['enabled'] = true;

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

            $entries = MetaData::region()->list($filters)->map(function ($entry) use ($label, $attribute) {
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
