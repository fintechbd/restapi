<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;

use Exception;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Fintech\Transaction\Facades\Transaction;
use Fintech\RestApi\Http\Resources\Transaction\ComplianceResource;
use Fintech\RestApi\Http\Resources\Transaction\ComplianceCollection;
use Fintech\RestApi\Http\Requests\Transaction\ImportComplianceRequest;
use Fintech\RestApi\Http\Requests\Transaction\StoreComplianceRequest;
use Fintech\RestApi\Http\Requests\Transaction\UpdateComplianceRequest;
use Fintech\RestApi\Http\Requests\Transaction\IndexComplianceRequest;
use Fintech\Transaction\Jobs\Compliance;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

/**
 * Class ComplianceController
 * @package Fintech\RestApi\Http\Controllers\Transaction
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Compliance
 * @lrd:end
 *
 */
class ComplianceController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Compliance* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexComplianceRequest $request
     * @return ComplianceCollection|JsonResponse
     */
    public function index(IndexComplianceRequest $request): ComplianceCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $compliancePaginate = Transaction::compliance()->list($inputs);

            return new ComplianceCollection($compliancePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *Compliance* resource in storage.
     * @lrd:end
     *
     * @param StoreComplianceRequest $request
     * @return JsonResponse
     * @throws StoreOperationException
     */
    public function store(StoreComplianceRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $compliance = Transaction::compliance()->create($inputs);

            if (!$compliance) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.compliance_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Compliance']),
                'id' => $compliance->id
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Compliance* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return ComplianceResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ComplianceResource|JsonResponse
    {
        try {

            $compliance = Transaction::compliance()->find($id);

            if (!$compliance) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.compliance_model'), $id);
            }

            return new ComplianceResource($compliance);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *Compliance* resource using id.
     * @lrd:end
     *
     * @param UpdateComplianceRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateComplianceRequest $request, string|int $id): JsonResponse
    {
        try {

            $compliance = Transaction::compliance()->find($id);

            if (!$compliance) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.compliance_model'), $id);
            }

            $inputs = $request->validated();

            if (!Transaction::compliance()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.compliance_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Compliance']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Compliance* resource using id.
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws DeleteOperationException
     */
    public function destroy(string|int $id)
    {
        try {

            $compliance = Transaction::compliance()->find($id);

            if (!$compliance) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.compliance_model'), $id);
            }

            if (!Transaction::compliance()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.compliance_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Compliance']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Compliance* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $compliance = Transaction::compliance()->find($id, true);

            if (!$compliance) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.compliance_model'), $id);
            }

            if (!Transaction::compliance()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.transaction.compliance_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Compliance']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Compliance* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param IndexComplianceRequest $request
     * @return JsonResponse
     */
    public function export(IndexComplianceRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $compliancePaginate = Transaction::compliance()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Compliance']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Compliance* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param ImportComplianceRequest $request
     * @return ComplianceCollection|JsonResponse
     */
    public function import(ImportComplianceRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $compliancePaginate = Transaction::compliance()->list($inputs);

            return new ComplianceCollection($compliancePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * Return a dropdown list of compliances
     *
     * @param DropDownRequest $request
     * @return DropDownCollection|JsonResponse
     */
    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
//        try {
        $filters = $request->all();

        $polices = collect(get_declared_classes())->filter(function ($class) {
            return Str::contains($class, 'Fintech');
        });


        dd($polices);

//            $entries = Auth::role()->list($filters)->map(function ($entry) use ($label, $attribute) {
//                return [
//                    'attribute' => $entry->{$attribute} ?? 'id',
//                    'label' => $entry->{$label} ?? 'name',
//                ];
//            })->toArray();
//
//            return new DropDownCollection($entries);

//        } catch (Exception $exception) {
//            return response()->failed($exception);
//        }
    }
}
