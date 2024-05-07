<?php

namespace Fintech\RestApi\Http\Controllers\Banco;

use Exception;
use Fintech\Banco\Facades\Banco;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\RestApi\Http\Requests\Banco\ImportBankRequest;
use Fintech\RestApi\Http\Requests\Banco\IndexBankRequest;
use Fintech\RestApi\Http\Requests\Banco\StoreBankRequest;
use Fintech\RestApi\Http\Requests\Banco\UpdateBankRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class BankController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Bank
 *
 * @lrd:end
 */
class BankController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *Bank* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexBankRequest $request): BankCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bankPaginate = Banco::bank()->list($inputs);

            return new BankCollection($bankPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *Bank* resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreBankRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bank = Banco::bank()->create($inputs);

            if (! $bank) {
                throw (new StoreOperationException)->setModel(config('fintech.banco.bank_model'));
            }

            return $this->created([
                'message' => __('core::messages.resource.created', ['model' => 'Bank']),
                'id' => $bank->getKey(),
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *Bank* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): BankResource|JsonResponse
    {
        try {

            $bank = Banco::bank()->find($id);

            if (! $bank) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.bank_model'), $id);
            }

            return new BankResource($bank);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *Bank* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateBankRequest $request, string|int $id): JsonResponse
    {
        try {

            $bank = Banco::bank()->find($id);

            if (! $bank) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.bank_model'), $id);
            }

            $inputs = $request->validated();

            if (! Banco::bank()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.banco.bank_model'), $id);
            }

            return $this->updated(__('core::messages.resource.updated', ['model' => 'Bank']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Bank* resource using id.
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

            $bank = Banco::bank()->find($id);

            if (! $bank) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.bank_model'), $id);
            }

            if (! Banco::bank()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.banco.bank_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Bank']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Bank* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $bank = Banco::bank()->find($id, true);

            if (! $bank) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.bank_model'), $id);
            }

            if (! Banco::bank()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.banco.bank_model'), $id);
            }

            return $this->restored(__('core::messages.resource.restored', ['model' => 'Bank']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Bank* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexBankRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bankPaginate = Banco::bank()->export($inputs);

            return $this->exported(__('core::messages.resource.exported', ['model' => 'Bank']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Bank* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return BankCollection|JsonResponse
     */
    public function import(ImportBankRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bankPaginate = Banco::bank()->list($inputs);

            return new BankCollection($bankPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    public function bankCategory(): BankCategoryResource|JsonResponse
    {
        try {
            $bankCategories = config('fintech.banco.bank_categories');

            return new BankCategoryResource($bankCategories);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
