<?php

namespace Fintech\RestApi\Http\Controllers\Promo;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Promo\Facades\Promo;
use Fintech\RestApi\Http\Requests\Promo\ImportPromotionRequest;
use Fintech\RestApi\Http\Requests\Promo\IndexPromotionRequest;
use Fintech\RestApi\Http\Requests\Promo\StorePromotionRequest;
use Fintech\RestApi\Http\Requests\Promo\UpdatePromotionRequest;
use Fintech\RestApi\Http\Resources\Promo\PromotionCollection;
use Fintech\RestApi\Http\Resources\Promo\PromotionResource;
use Fintech\RestApi\Http\Resources\Promo\PromotionTypeResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class PromotionController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Promotion
 *
 * @lrd:end
 */
class PromotionController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Promotion* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexPromotionRequest $request): PromotionCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $promotionPaginate = Promo::promotion()->list($inputs);

            return new PromotionCollection($promotionPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     *  Create a new *Promotion* resource in storage.
     *
     * @lrd:end
     */
    public function store(StorePromotionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $promotion = Promo::promotion()->create($inputs);

            if (! $promotion) {
                throw (new StoreOperationException)->setModel(config('fintech.promo.promotion_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Promotion']),
                'id' => $promotion->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Promotion* resource found by id.
     *
     * @lrd:end
     */
    public function show(string|int $id): PromotionResource|JsonResponse
    {
        try {

            $promotion = Promo::promotion()->find($id);

            if (! $promotion) {
                throw (new ModelNotFoundException)->setModel(config('fintech.promo.promotion_model'), $id);
            }

            return new PromotionResource($promotion);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *Promotion* resource using id.
     *
     * @lrd:end
     */
    public function update(UpdatePromotionRequest $request, string|int $id): JsonResponse
    {
        try {

            $promotion = Promo::promotion()->find($id);

            if (! $promotion) {
                throw (new ModelNotFoundException)->setModel(config('fintech.promo.promotion_model'), $id);
            }

            $inputs = $request->validated();

            if (! Promo::promotion()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.promo.promotion_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Promotion']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Promotion* resource using id.
     *
     * @lrd:end
     */
    public function destroy(string|int $id): JsonResponse
    {
        try {

            $promotion = Promo::promotion()->find($id);

            if (! $promotion) {
                throw (new ModelNotFoundException)->setModel(config('fintech.promo.promotion_model'), $id);
            }

            if (! Promo::promotion()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.promo.promotion_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Promotion']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Promotion* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     */
    public function restore(string|int $id): JsonResponse
    {
        try {

            $promotion = Promo::promotion()->find($id, true);

            if (! $promotion) {
                throw (new ModelNotFoundException)->setModel(config('fintech.promo.promotion_model'), $id);
            }

            if (! Promo::promotion()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.promo.promotion_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Promotion']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *Promotion* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexPromotionRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $promotionPaginate = Promo::promotion()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Promotion']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *Promotion* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportPromotionRequest $request): PromotionCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $promotionPaginate = Promo::promotion()->list($inputs);

            return new PromotionCollection($promotionPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    public function types(): PromotionTypeResource|JsonResponse
    {
        try {
            $promotionTypes = config('fintech.promo.promotion_types');

            return new PromotionTypeResource($promotionTypes);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
