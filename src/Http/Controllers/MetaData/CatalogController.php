<?php

namespace Fintech\RestApi\Http\Controllers\MetaData;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\MetaData\Facades\MetaData;
use Fintech\RestApi\Http\Requests\MetaData\ImportCatalogRequest;
use Fintech\RestApi\Http\Requests\MetaData\IndexCatalogRequest;
use Fintech\RestApi\Http\Requests\MetaData\StoreCatalogRequest;
use Fintech\RestApi\Http\Requests\MetaData\UpdateCatalogRequest;
use Fintech\RestApi\Http\Resources\MetaData\CatalogCollection;
use Fintech\RestApi\Http\Resources\MetaData\CatalogResource;
use Fintech\RestApi\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class CatalogController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Catalog
 *
 * @lrd:end
 */
class CatalogController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *Catalog* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexCatalogRequest $request): CatalogCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $catalogPaginate = MetaData::catalog()->list($inputs);

            return new CatalogCollection($catalogPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *Catalog* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreCatalogRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $catalog = MetaData::catalog()->create($inputs);

            if (! $catalog) {
                throw (new StoreOperationException())->setModel(config('fintech.metadata.catalog_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Catalog']),
                'id' => $catalog->id,
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *Catalog* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): CatalogResource|JsonResponse
    {
        try {

            $catalog = MetaData::catalog()->find($id);

            if (! $catalog) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.catalog_model'), $id);
            }

            return new CatalogResource($catalog);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *Catalog* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateCatalogRequest $request, string|int $id): JsonResponse
    {
        try {

            $catalog = MetaData::catalog()->find($id);

            if (! $catalog) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.catalog_model'), $id);
            }

            $inputs = $request->validated();

            if (! MetaData::catalog()->update($id, $inputs)) {

                throw (new UpdateOperationException())->setModel(config('fintech.metadata.catalog_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'Catalog']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Catalog* resource using id.
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

            $catalog = MetaData::catalog()->find($id);

            if (! $catalog) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.catalog_model'), $id);
            }

            if (! MetaData::catalog()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.metadata.catalog_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Catalog']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Catalog* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $catalog = MetaData::catalog()->find($id, true);

            if (! $catalog) {
                throw (new ModelNotFoundException())->setModel(config('fintech.metadata.catalog_model'), $id);
            }

            if (! MetaData::catalog()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.metadata.catalog_model'), $id);
            }

            return $this->restored(__('restapi::messages.resource.restored', ['model' => 'Catalog']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Catalog* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexCatalogRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $catalogPaginate = MetaData::catalog()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'Catalog']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Catalog* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return \Fintech\RestApi\Http\Resources\MetaData\CatalogCollection|JsonResponse
     */
    public function import(ImportCatalogRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $catalogPaginate = MetaData::catalog()->list($inputs);

            return new CatalogCollection($catalogPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
