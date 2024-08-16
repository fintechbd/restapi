<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Auth\ImportFavouriteRequest;
use Fintech\RestApi\Http\Requests\Auth\IndexFavouriteRequest;
use Fintech\RestApi\Http\Requests\Auth\StoreFavouriteRequest;
use Fintech\RestApi\Http\Requests\Auth\UpdateFavouriteRequest;
use Fintech\RestApi\Http\Resources\Auth\FavouriteCollection;
use Fintech\RestApi\Http\Resources\Auth\FavouriteResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class FavouriteController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Favourite
 *
 * @lrd:end
 */
class FavouriteController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Favourite* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexFavouriteRequest $request): FavouriteCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $favouritePaginate = Auth::favourite()->list($inputs);

            return new FavouriteCollection($favouritePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *Favourite* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreFavouriteRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $favourite = Auth::favourite()->create($inputs);

            if (! $favourite) {
                throw (new StoreOperationException)->setModel(config('fintech.auth.favourite_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Favourite']),
                'id' => $favourite->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Favourite* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): FavouriteResource|JsonResponse
    {
        try {

            $favourite = Auth::favourite()->find($id);

            if (! $favourite) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.favourite_model'), $id);
            }

            return new FavouriteResource($favourite);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *Favourite* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateFavouriteRequest $request, string|int $id): JsonResponse
    {
        try {

            $favourite = Auth::favourite()->find($id);

            if (! $favourite) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.favourite_model'), $id);
            }

            $inputs = $request->validated();

            if (! Auth::favourite()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.auth.favourite_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Favourite']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Favourite* resource using id.
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

            $favourite = Auth::favourite()->find($id);

            if (! $favourite) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.favourite_model'), $id);
            }

            if (! Auth::favourite()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.auth.favourite_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Favourite']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Favourite* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $favourite = Auth::favourite()->find($id, true);

            if (! $favourite) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.favourite_model'), $id);
            }

            if (! Auth::favourite()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.auth.favourite_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Favourite']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Favourite* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexFavouriteRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $favouritePaginate = Auth::favourite()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Favourite']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Favourite* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return FavouriteCollection|JsonResponse
     */
    public function import(ImportFavouriteRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $favouritePaginate = Auth::favourite()->list($inputs);

            return new FavouriteCollection($favouritePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
