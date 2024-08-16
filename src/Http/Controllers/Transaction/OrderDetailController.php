<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Transaction\ImportOrderDetailRequest;
use Fintech\RestApi\Http\Requests\Transaction\IndexOrderDetailRequest;
use Fintech\RestApi\Http\Requests\Transaction\StoreOrderDetailRequest;
use Fintech\RestApi\Http\Requests\Transaction\UpdateOrderDetailRequest;
use Fintech\RestApi\Http\Resources\Transaction\OrderDetailCollection;
use Fintech\RestApi\Http\Resources\Transaction\OrderDetailResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class OrderDetailController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to OrderDetail
 *
 * @lrd:end
 */
class OrderDetailController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *OrderDetail* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexOrderDetailRequest $request): OrderDetailCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderDetailPaginate = Transaction::orderDetail()->list($inputs);

            return new OrderDetailCollection($orderDetailPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *OrderDetail* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreOrderDetailRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderDetail = Transaction::orderDetail()->create($inputs);

            if (! $orderDetail) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.order_detail_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Order Detail']),
                'id' => $orderDetail->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *OrderDetail* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): OrderDetailResource|JsonResponse
    {
        try {

            $orderDetail = Transaction::orderDetail()->find($id);

            if (! $orderDetail) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            return new OrderDetailResource($orderDetail);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *OrderDetail* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateOrderDetailRequest $request, string|int $id): JsonResponse
    {
        try {

            $orderDetail = Transaction::orderDetail()->find($id);

            if (! $orderDetail) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            $inputs = $request->validated();

            if (! Transaction::orderDetail()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Order Detail']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *OrderDetail* resource using id.
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

            $orderDetail = Transaction::orderDetail()->find($id);

            if (! $orderDetail) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            if (! Transaction::orderDetail()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Order Detail']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *OrderDetail* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $orderDetail = Transaction::orderDetail()->find($id, true);

            if (! $orderDetail) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            if (! Transaction::orderDetail()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Order Detail']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *OrderDetail* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexOrderDetailRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderDetailPaginate = Transaction::orderDetail()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Order Detail']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *OrderDetail* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return OrderDetailCollection|JsonResponse
     */
    public function import(ImportOrderDetailRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderDetailPaginate = Transaction::orderDetail()->list($inputs);

            return new OrderDetailCollection($orderDetailPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
