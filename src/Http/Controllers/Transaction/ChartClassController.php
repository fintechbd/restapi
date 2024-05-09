<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Transaction\ImportChartClassRequest;
use Fintech\RestApi\Http\Requests\Transaction\IndexChartClassRequest;
use Fintech\RestApi\Http\Requests\Transaction\StoreChartClassRequest;
use Fintech\RestApi\Http\Requests\Transaction\UpdateChartClassRequest;
use Fintech\RestApi\Http\Resources\Transaction\ChartClassCollection;
use Fintech\RestApi\Http\Resources\Transaction\ChartClassResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ChartClassController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ChartClass
 *
 * @lrd:end
 */
class ChartClassController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ChartClass* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexChartClassRequest $request): ChartClassCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartClassPaginate = Transaction::chartClass()->list($inputs);

            return new ChartClassCollection($chartClassPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ChartClass* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreChartClassRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartClass = Transaction::chartClass()->create($inputs);

            if (!$chartClass) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.chart_class_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Chart Class']),
                'id' => $chartClass->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ChartClass* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ChartClassResource|JsonResponse
    {
        try {

            $chartClass = Transaction::chartClass()->find($id);

            if (!$chartClass) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            return new ChartClassResource($chartClass);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ChartClass* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateChartClassRequest $request, string|int $id): JsonResponse
    {
        try {

            $chartClass = Transaction::chartClass()->find($id);

            if (!$chartClass) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            $inputs = $request->validated();

            if (!Transaction::chartClass()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Chart Class']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ChartClass* resource using id.
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

            $chartClass = Transaction::chartClass()->find($id);

            if (!$chartClass) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            if (!Transaction::chartClass()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Chart Class']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ChartClass* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $chartClass = Transaction::chartClass()->find($id, true);

            if (!$chartClass) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            if (!Transaction::chartClass()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Chart Class']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartClass* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexChartClassRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartClassPaginate = Transaction::chartClass()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Chart Class']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartClass* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ChartClassCollection|JsonResponse
     */
    public function import(ImportChartClassRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartClassPaginate = Transaction::chartClass()->list($inputs);

            return new ChartClassCollection($chartClassPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
