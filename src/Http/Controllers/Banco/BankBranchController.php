<?php

namespace Fintech\RestApi\Http\Controllers\Banco;

use Exception;
use Fintech\Banco\Facades\Banco;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Banco\ImportBankBranchRequest;
use Fintech\RestApi\Http\Requests\Banco\IndexBankBranchRequest;
use Fintech\RestApi\Http\Requests\Banco\StoreBankBranchRequest;
use Fintech\RestApi\Http\Requests\Banco\UpdateBankBranchRequest;
use Fintech\RestApi\Http\Resources\Banco\BankBranchCollection;
use Fintech\RestApi\Http\Resources\Banco\BankBranchResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class BankBranchController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to BankBranch
 *
 * @lrd:end
 */
class BankBranchController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *BankBranch* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexBankBranchRequest $request): BankBranchCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bankBranchPaginate = Banco::bankBranch()->list($inputs);

            return new BankBranchCollection($bankBranchPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *BankBranch* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreBankBranchRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bankBranch = Banco::bankBranch()->create($inputs);

            if (! $bankBranch) {
                throw (new StoreOperationException)->setModel(config('fintech.banco.bank_branch_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Bank Branch']),
                'id' => $bankBranch->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *BankBranch* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): BankBranchResource|JsonResponse
    {
        try {

            $bankBranch = Banco::bankBranch()->find($id);

            if (! $bankBranch) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.bank_branch_model'), $id);
            }

            return new BankBranchResource($bankBranch);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *BankBranch* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateBankBranchRequest $request, string|int $id): JsonResponse
    {
        try {

            $bankBranch = Banco::bankBranch()->find($id);

            if (! $bankBranch) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.bank_branch_model'), $id);
            }

            $inputs = $request->validated();

            if (! Banco::bankBranch()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.banco.bank_branch_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Bank Branch']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *BankBranch* resource using id.
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

            $bankBranch = Banco::bankBranch()->find($id);

            if (! $bankBranch) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.bank_branch_model'), $id);
            }

            if (! Banco::bankBranch()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.banco.bank_branch_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Bank Branch']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *BankBranch* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $bankBranch = Banco::bankBranch()->find($id, true);

            if (! $bankBranch) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.bank_branch_model'), $id);
            }

            if (! Banco::bankBranch()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.banco.bank_branch_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Bank Branch']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *BankBranch* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexBankBranchRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bankBranchPaginate = Banco::bankBranch()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Bank Branch']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *BankBranch* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return BankBranchCollection|JsonResponse
     */
    public function import(ImportBankBranchRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bankBranchPaginate = Banco::bankBranch()->list($inputs);

            return new BankBranchCollection($bankBranchPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
