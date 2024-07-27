<?php

namespace Fintech\RestApi\Http\Controllers\Banco;

use Exception;
use Fintech\Banco\Facades\Banco;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Banco\ImportBeneficiaryAccountTypeRequest;
use Fintech\RestApi\Http\Requests\Banco\IndexBeneficiaryAccountTypeRequest;
use Fintech\RestApi\Http\Requests\Banco\StoreBeneficiaryAccountTypeRequest;
use Fintech\RestApi\Http\Requests\Banco\UpdateBeneficiaryAccountTypeRequest;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Banco\BeneficiaryAccountTypeCollection;
use Fintech\RestApi\Http\Resources\Banco\BeneficiaryAccountTypeResource;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class BeneficiaryAccountTypeController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to BeneficiaryAccountType
 *
 * @lrd:end
 */
class BeneficiaryAccountTypeController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *BeneficiaryAccountType* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexBeneficiaryAccountTypeRequest $request): BeneficiaryAccountTypeCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiaryAccountTypePaginate = Banco::beneficiaryAccountType()->list($inputs);

            return new BeneficiaryAccountTypeCollection($beneficiaryAccountTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *BeneficiaryAccountType* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreBeneficiaryAccountTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiaryAccountType = Banco::beneficiaryAccountType()->create($inputs);

            if (! $beneficiaryAccountType) {
                throw (new StoreOperationException)->setModel(config('fintech.banco.beneficiary_account_type_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Beneficiary Account Type']),
                'id' => $beneficiaryAccountType->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *BeneficiaryAccountType* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): BeneficiaryAccountTypeResource|JsonResponse
    {
        try {

            $beneficiaryAccountType = Banco::beneficiaryAccountType()->find($id);

            if (! $beneficiaryAccountType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_account_type_model'), $id);
            }

            return new BeneficiaryAccountTypeResource($beneficiaryAccountType);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *BeneficiaryAccountType* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateBeneficiaryAccountTypeRequest $request, string|int $id): JsonResponse
    {
        try {

            $beneficiaryAccountType = Banco::beneficiaryAccountType()->find($id);

            if (! $beneficiaryAccountType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_account_type_model'), $id);
            }

            $inputs = $request->validated();

            if (! Banco::beneficiaryAccountType()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.banco.beneficiary_account_type_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Beneficiary Account Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *BeneficiaryAccountType* resource using id.
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

            $beneficiaryAccountType = Banco::beneficiaryAccountType()->find($id);

            if (! $beneficiaryAccountType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_account_type_model'), $id);
            }

            if (! Banco::beneficiaryAccountType()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.banco.beneficiary_account_type_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Beneficiary Account Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *BeneficiaryAccountType* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $beneficiaryAccountType = Banco::beneficiaryAccountType()->find($id, true);

            if (! $beneficiaryAccountType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_account_type_model'), $id);
            }

            if (! Banco::beneficiaryAccountType()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.banco.beneficiary_account_type_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Beneficiary Account Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *BeneficiaryAccountType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexBeneficiaryAccountTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiaryAccountTypePaginate = Banco::beneficiaryAccountType()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Beneficiary Account Type']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *BeneficiaryAccountType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return BeneficiaryAccountTypeCollection|JsonResponse
     */
    public function import(ImportBeneficiaryAccountTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiaryAccountTypePaginate = Banco::beneficiaryAccountType()->list($inputs);

            return new BeneficiaryAccountTypeCollection($beneficiaryAccountTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
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

            $entries = Banco::beneficiaryAccountType()->list($filters)->map(function ($entry) use ($label, $attribute) {
                return [
                    'attribute' => $entry->{$attribute} ?? 'id',
                    'label' => $entry->{$label} ?? 'name',
                ];
            })->toArray();

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception->getMessage());
        }
    }
}
