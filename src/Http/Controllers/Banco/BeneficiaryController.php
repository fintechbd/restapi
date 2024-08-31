<?php

namespace Fintech\RestApi\Http\Controllers\Banco;

use Exception;
use Fintech\Banco\Events\BeneficiaryAdded;
use Fintech\Banco\Facades\Banco;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Banco\ImportBeneficiaryRequest;
use Fintech\RestApi\Http\Requests\Banco\IndexBeneficiaryRequest;
use Fintech\RestApi\Http\Requests\Banco\StoreBeneficiaryRequest;
use Fintech\RestApi\Http\Requests\Banco\UpdateBeneficiaryRequest;
use Fintech\RestApi\Http\Resources\Banco\BeneficiaryCollection;
use Fintech\RestApi\Http\Resources\Banco\BeneficiaryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class BeneficiaryController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Beneficiary
 *
 * @lrd:end
 */
class BeneficiaryController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Beneficiary* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexBeneficiaryRequest $request): BeneficiaryCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiaryPaginate = Banco::beneficiary()->list($inputs);

            return new BeneficiaryCollection($beneficiaryPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *Beneficiary* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreBeneficiaryRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiary = Banco::beneficiary()->create($inputs);

            if (!$beneficiary) {
                throw (new StoreOperationException)->setModel(config('fintech.banco.beneficiary_model'));
            }
            event(new BeneficiaryAdded($request->user(), $beneficiary));

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Beneficiary']),
                'id' => $beneficiary->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Beneficiary* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): BeneficiaryResource|JsonResponse
    {
        try {

            $beneficiary = Banco::beneficiary()->find($id);

            if (!$beneficiary) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_model'), $id);
            }

            return new BeneficiaryResource($beneficiary);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *Beneficiary* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateBeneficiaryRequest $request, string|int $id): JsonResponse
    {
        try {

            $beneficiary = Banco::beneficiary()->find($id);

            if (!$beneficiary) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_model'), $id);
            }

            $inputs = $request->validated();

            if (!Banco::beneficiary()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.banco.beneficiary_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Beneficiary']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Beneficiary* resource using id.
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

            $beneficiary = Banco::beneficiary()->find($id);

            if (!$beneficiary) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_model'), $id);
            }

            if (!Banco::beneficiary()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.banco.beneficiary_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Beneficiary']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Beneficiary* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $beneficiary = Banco::beneficiary()->find($id, true);

            if (!$beneficiary) {
                throw (new ModelNotFoundException)->setModel(config('fintech.banco.beneficiary_model'), $id);
            }

            if (!Banco::beneficiary()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.banco.beneficiary_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Beneficiary']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Beneficiary* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexBeneficiaryRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiaryPaginate = Banco::beneficiary()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Beneficiary']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Beneficiary* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return BeneficiaryCollection|JsonResponse
     */
    public function import(ImportBeneficiaryRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $beneficiaryPaginate = Banco::beneficiary()->list($inputs);

            return new BeneficiaryCollection($beneficiaryPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
