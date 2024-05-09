<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\RestApi\Http\Requests\Transaction\ImportManualRefundRequest;
use Fintech\RestApi\Http\Requests\Transaction\IndexManualRefundRequest;
use Fintech\RestApi\Http\Requests\Transaction\StoreManualRefundRequest;
use Fintech\RestApi\Http\Requests\Transaction\UpdateManualRefundRequest;
use Fintech\RestApi\Http\Resources\Transaction\ManualRefundCollection;
use Fintech\RestApi\Http\Resources\Transaction\ManualRefundResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ManualRefundController
 *
 * @lrd:start
 * This class handle create, display, update, delete
 * operation related to ManualRefund
 *
 * @lrd:end
 */
class ManualRefundController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *ManualRefund* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexManualRefundRequest $request): ManualRefundCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefundPaginate = Transaction::manualRefund()->list($inputs);

            return new ManualRefundCollection($manualRefundPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ManualRefund* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreManualRefundRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefund = Transaction::manualRefund()->create($inputs);

            if (! $manualRefund) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.manual_refund_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Manual Refund']),
                'id' => $manualRefund->id,
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ManualRefund* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ManualRefundResource|JsonResponse
    {
        try {

            $manualRefund = Transaction::manualRefund()->find($id);

            if (! $manualRefund) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            return new ManualRefundResource($manualRefund);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ManualRefund* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateManualRefundRequest $request, string|int $id): JsonResponse
    {
        try {

            $manualRefund = Transaction::manualRefund()->find($id);

            if (! $manualRefund) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            $inputs = $request->validated();

            if (! Transaction::manualRefund()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'Manual Refund']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ManualRefund* resource using id.
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

            $manualRefund = Transaction::manualRefund()->find($id);

            if (! $manualRefund) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            if (! Transaction::manualRefund()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Manual Refund']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ManualRefund* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexManualRefundRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefundPaginate = Transaction::manualRefund()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'Manual Refund']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ManualRefund* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ManualRefundCollection|JsonResponse
     */
    public function import(ImportManualRefundRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefundPaginate = Transaction::manualRefund()->list($inputs);

            return new ManualRefundCollection($manualRefundPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
