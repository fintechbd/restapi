<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Transaction\ImportChartTypeRequest;
use Fintech\RestApi\Http\Requests\Transaction\IndexChartTypeRequest;
use Fintech\RestApi\Http\Requests\Transaction\StoreChartTypeRequest;
use Fintech\RestApi\Http\Requests\Transaction\UpdateChartTypeRequest;
use Fintech\RestApi\Http\Resources\Transaction\ChartTypeCollection;
use Fintech\RestApi\Http\Resources\Transaction\ChartTypeResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ChartTypeController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ChartType
 *
 * @lrd:end
 */
class ChartTypeController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ChartType* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexChartTypeRequest $request): ChartTypeCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartTypePaginate = Transaction::chartType()->list($inputs);

            return new ChartTypeCollection($chartTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ChartType* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreChartTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartType = Transaction::chartType()->create($inputs);

            if (!$chartType) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.chart_type_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Chart Type']),
                'id' => $chartType->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ChartType* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ChartTypeResource|JsonResponse
    {
        try {

            $chartType = Transaction::chartType()->find($id);

            if (!$chartType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            return new ChartTypeResource($chartType);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ChartType* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateChartTypeRequest $request, string|int $id): JsonResponse
    {
        try {

            $chartType = Transaction::chartType()->find($id);

            if (!$chartType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            $inputs = $request->validated();

            if (!Transaction::chartType()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'Chart Type']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ChartType* resource using id.
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

            $chartType = Transaction::chartType()->find($id);

            if (!$chartType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            if (!Transaction::chartType()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Chart Type']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ChartType* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $chartType = Transaction::chartType()->find($id, true);

            if (!$chartType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            if (!Transaction::chartType()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            return $this->restored(__('restapi::messages.resource.restored', ['model' => 'Chart Type']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexChartTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartTypePaginate = Transaction::chartType()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'Chart Type']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ChartTypeCollection|JsonResponse
     */
    public function import(ImportChartTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartTypePaginate = Transaction::chartType()->list($inputs);

            return new ChartTypeCollection($chartTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
