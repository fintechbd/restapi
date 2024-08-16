<?php

namespace Fintech\RestApi\Http\Controllers\Banco;

use Exception;
use Fintech\Banco\Facades\Banco;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Banco\ImportBeneficiaryTypeRequest;
use Fintech\RestApi\Http\Requests\Banco\IndexBeneficiaryTypeRequest;
use Fintech\RestApi\Http\Requests\Banco\StoreBeneficiaryTypeRequest;
use Fintech\RestApi\Http\Requests\Banco\UpdateBeneficiaryTypeRequest;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Banco\BeneficiaryTypeCollection;
use Fintech\RestApi\Http\Resources\Banco\BeneficiaryTypeResource;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class BeneficiaryTypeController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to BeneficiaryType
 *
 * @lrd:end
 */
class BeneficiaryTypeController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *BeneficiaryType* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexBeneficiaryTypeRequest $request): BeneficiaryTypeCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiaryTypePaginate = Banco::beneficiaryType()->list($inputs);

            return new BeneficiaryTypeCollection($beneficiaryTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *BeneficiaryType* resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreBeneficiaryTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiaryType = Banco::beneficiaryType()->create($inputs);

            if (! $beneficiaryType) {
                throw (new StoreOperationException)->setModel(config('fintech.banco.beneficiary_type_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Beneficiary Type']),
                'id' => $beneficiaryType->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *BeneficiaryType* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): BeneficiaryTypeResource|JsonResponse
    {
        try {

            $beneficiaryType = Banco::beneficiaryType()->find($id);

            if (! $beneficiaryType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_type_model'), $id);
            }

            return new BeneficiaryTypeResource($beneficiaryType);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *BeneficiaryType* resource using id.
     *
     * @lrd:end
     */
    public function update(UpdateBeneficiaryTypeRequest $request, string|int $id): JsonResponse
    {
        try {

            $beneficiaryType = Banco::beneficiaryType()->find($id);

            if (! $beneficiaryType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_type_model'), $id);
            }

            $inputs = $request->validated();

            if (! Banco::beneficiaryType()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.banco.beneficiary_type_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Beneficiary Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *BeneficiaryType* resource using id.
     *
     * @lrd:end
     */
    public function destroy(string|int $id): JsonResponse
    {
        try {

            $beneficiaryType = Banco::beneficiaryType()->find($id);

            if (! $beneficiaryType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_type_model'), $id);
            }

            if (! Banco::beneficiaryType()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.banco.beneficiary_type_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Beneficiary Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *BeneficiaryType* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     */
    public function restore(string|int $id): JsonResponse
    {
        try {

            $beneficiaryType = Banco::beneficiaryType()->find($id, true);

            if (! $beneficiaryType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_type_model'), $id);
            }

            if (! Banco::beneficiaryType()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.banco.beneficiary_type_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Beneficiary Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *BeneficiaryType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexBeneficiaryTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            //$beneficiaryTypePaginate = Banco::beneficiaryType()->export($inputs);
            Banco::beneficiaryType()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Beneficiary Type']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *BeneficiaryType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportBeneficiaryTypeRequest $request): JsonResponse|BeneficiaryTypeCollection
    {
        try {
            $inputs = $request->validated();

            $beneficiaryTypePaginate = Banco::beneficiaryType()->list($inputs);

            return new BeneficiaryTypeCollection($beneficiaryTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $label = 'beneficiary_type_name';

            $attribute = 'id';

            if (! empty($filters['label'])) {
                $label = $filters['label'];
                unset($filters['label']);
            }

            if (! empty($filters['attribute'])) {
                $attribute = $filters['attribute'];
                unset($filters['attribute']);
            }

            $entries = Banco::beneficiaryType()->list($filters)->map(function ($entry) use ($label, $attribute) {
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
