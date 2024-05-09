<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\RestApi\Http\Requests\Transaction\ImportOrderRequest;
use Fintech\RestApi\Http\Requests\Transaction\IndexOrderRequest;
use Fintech\RestApi\Http\Requests\Transaction\StoreOrderRequest;
use Fintech\RestApi\Http\Requests\Transaction\UpdateOrderRequest;
use Fintech\RestApi\Http\Resources\Transaction\OrderCollection;
use Fintech\RestApi\Http\Resources\Transaction\OrderResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class OrderController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Order
 *
 * @lrd:end
 */
class OrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *Order* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexOrderRequest $request): OrderCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderPaginate = Transaction::order()->list($inputs);

            return new OrderCollection($orderPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *Order* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $order = Transaction::order()->create($inputs);

            if (! $order) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.order_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Order']),
                'id' => $order->id,
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *Order* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): OrderResource|JsonResponse
    {
        try {

            $order = Transaction::order()->find($id);

            if (! $order) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_model'), $id);
            }

            return new OrderResource($order);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *Order* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateOrderRequest $request, string|int $id): JsonResponse
    {
        try {

            $order = Transaction::order()->find($id);

            if (! $order) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_model'), $id);
            }

            $inputs = $request->validated();

            if (! Transaction::order()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.order_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'Order']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Order* resource using id.
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

            $order = Transaction::order()->find($id);

            if (! $order) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_model'), $id);
            }

            if (! Transaction::order()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.order_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Order']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Order* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $order = Transaction::order()->find($id, true);

            if (! $order) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_model'), $id);
            }

            if (! Transaction::order()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.transaction.order_model'), $id);
            }

            return $this->restored(__('restapi::messages.resource.restored', ['model' => 'Order']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Order* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexOrderRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderPaginate = Transaction::order()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'Order']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Order* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return OrderCollection|JsonResponse
     */
    public function import(ImportOrderRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderPaginate = Transaction::order()->list($inputs);

            return new OrderCollection($orderPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
