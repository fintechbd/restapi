<?php

namespace Fintech\RestApi\Http\Controllers\MetaData;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\MetaData\Facades\MetaData;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Requests\MetaData\ImportSubRegionRequest;
use Fintech\RestApi\Http\Requests\MetaData\IndexSubRegionRequest;
use Fintech\RestApi\Http\Requests\MetaData\StoreSubRegionRequest;
use Fintech\RestApi\Http\Requests\MetaData\UpdateSubRegionRequest;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Fintech\RestApi\Http\Resources\MetaData\SubRegionCollection;
use Fintech\RestApi\Http\Resources\MetaData\SubRegionResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class SubRegionController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to subRegion
 *
 * @lrd:end
 */
class SubRegionController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the subRegion resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexSubRegionRequest $request): SubRegionCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $subRegionPaginate = MetaData::subregion()->list($inputs);

            return new SubRegionCollection($subRegionPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new subRegion resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreSubRegionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $subRegion = MetaData::subregion()->create($inputs);

            if (! $subRegion) {
                throw (new StoreOperationException())->setModel(config('fintech.metadata.subregion_model'));
            }

            return $this->created([
                'message' => __('core::messages.resource.created', ['model' => 'Sub-Region']),
                'id' => $subRegion->getKey(),
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @LRDparam trashed boolean|nullable
     *
     * @lrd:start
     * Return a specified subRegion resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): SubRegionResource|JsonResponse
    {
        try {

            $subRegion = MetaData::subregion()->find($id);

            if (! $subRegion) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.subregion_model'), $id);
            }

            return new SubRegionResource($subRegion);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified subRegion resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateSubRegionRequest $request, string|int $id): JsonResponse
    {
        try {

            $subRegion = MetaData::subregion()->find($id);

            if (! $subRegion) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.subregion_model'), $id);
            }

            $inputs = $request->validated();

            if (! MetaData::subregion()->update($id, $inputs)) {

                throw (new UpdateOperationException())->setModel(config('fintech.metadata.subregion_model'), $id);
            }

            return $this->updated(__('core::messages.resource.updated', ['model' => 'Sub-Region']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified subRegion resource using id.
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

            $subRegion = MetaData::subregion()->find($id);

            if (! $subRegion) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.subregion_model'), $id);
            }

            if (! MetaData::subregion()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.metadata.subregion_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Sub-Region']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified subRegion resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $subRegion = MetaData::subregion()->find($id, true);

            if (! $subRegion) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.subregion_model'), $id);
            }

            if (! MetaData::subregion()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.metadata.subregion_model'), $id);
            }

            return $this->restored(__('core::messages.resource.restored', ['model' => 'Sub-Region']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the subRegion resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexSubRegionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $subRegionPaginate = MetaData::subregion()->export($inputs);

            return $this->exported(__('core::messages.resource.exported', ['model' => 'Sub-Region']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the subRegion resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return SubRegionCollection|JsonResponse
     */
    public function import(ImportSubRegionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $subRegionPaginate = MetaData::subregion()->list($inputs);

            return new SubRegionCollection($subRegionPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

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

            $entries = MetaData::subregion()->list($filters)->map(function ($entry) use ($label, $attribute) {
                return [
                    'attribute' => $entry->{$attribute} ?? 'id',
                    'label' => $entry->{$label} ?? 'name',
                ];
            })->toArray();

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return $this->failed($exception->getMessage());
        }
    }
}
