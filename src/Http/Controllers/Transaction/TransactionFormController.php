<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Requests\Transaction\ImportTransactionFormRequest;
use Fintech\RestApi\Http\Requests\Transaction\IndexTransactionFormRequest;
use Fintech\RestApi\Http\Requests\Transaction\StoreTransactionFormRequest;
use Fintech\RestApi\Http\Requests\Transaction\UpdateTransactionFormRequest;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Fintech\RestApi\Http\Resources\Transaction\TransactionFormCollection;
use Fintech\RestApi\Http\Resources\Transaction\TransactionFormResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class TransactionFormController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to TransactionForm
 *
 * @lrd:end
 */
class TransactionFormController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *TransactionForm* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexTransactionFormRequest $request): TransactionFormCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $transactionFormPaginate = Transaction::transactionForm()->list($inputs);

            return new TransactionFormCollection($transactionFormPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *TransactionForm* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreTransactionFormRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $transactionForm = Transaction::transactionForm()->create($inputs);

            if (! $transactionForm) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.transaction_form_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Transaction Form']),
                'id' => $transactionForm->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *TransactionForm* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): TransactionFormResource|JsonResponse
    {
        try {

            $transactionForm = Transaction::transactionForm()->find($id);

            if (! $transactionForm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            return new TransactionFormResource($transactionForm);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *TransactionForm* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateTransactionFormRequest $request, string|int $id): JsonResponse
    {
        try {

            $transactionForm = Transaction::transactionForm()->find($id);

            if (! $transactionForm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            $inputs = $request->validated();

            if (! Transaction::transactionForm()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Transaction Form']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *TransactionForm* resource using id.
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

            $transactionForm = Transaction::transactionForm()->find($id);

            if (! $transactionForm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            if (! Transaction::transactionForm()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Transaction Form']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *TransactionForm* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $transactionForm = Transaction::transactionForm()->find($id, true);

            if (! $transactionForm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            if (! Transaction::transactionForm()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Transaction Form']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *TransactionForm* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexTransactionFormRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $transactionFormPaginate = Transaction::transactionForm()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Transaction Form']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *TransactionForm* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return TransactionFormCollection|JsonResponse
     */
    public function import(ImportTransactionFormRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $transactionFormPaginate = Transaction::transactionForm()->list($inputs);

            return new TransactionFormCollection($transactionFormPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $label = 'name';

            $attribute = 'id';

            if (! empty($filters['label'])) {
                $label = $filters['label'];
                unset($filters['label']);
            }

            if (! empty($filters['attribute'])) {
                $attribute = $filters['attribute'];
                unset($filters['attribute']);
            }

            $entries = Transaction::transactionForm()->list($filters)->map(function ($entry) use ($label, $attribute) {
                return [
                    'attribute' => $entry->{$attribute} ?? 'id',
                    'label' => $entry->{$label} ?? 'name',
                ];
            })->toArray();

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception);
        }
    }
}
