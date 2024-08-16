<?php

namespace Fintech\RestApi\Http\Controllers\Bell;

use Exception;
use Fintech\Bell\Facades\Bell;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Bell\ImportTriggerVariableRequest;
use Fintech\RestApi\Http\Requests\Bell\IndexTriggerVariableRequest;
use Fintech\RestApi\Http\Requests\Bell\StoreTriggerVariableRequest;
use Fintech\RestApi\Http\Requests\Bell\UpdateTriggerVariableRequest;
use Fintech\RestApi\Http\Resources\Bell\TriggerVariableCollection;
use Fintech\RestApi\Http\Resources\Bell\TriggerVariableResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class TriggerVariableController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to TriggerVariable
 *
 * @lrd:end
 */
class TriggerVariableController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *TriggerVariable* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexTriggerVariableRequest $request): TriggerVariableCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $triggerVariablePaginate = Bell::triggerVariable()->list($inputs);

            return new TriggerVariableCollection($triggerVariablePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *TriggerVariable* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreTriggerVariableRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $triggerVariable = Bell::triggerVariable()->create($inputs);

            if (! $triggerVariable) {
                throw (new StoreOperationException)->setModel(config('fintech.bell.trigger_variable_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Trigger Variable']),
                'id' => $triggerVariable->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *TriggerVariable* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): TriggerVariableResource|JsonResponse
    {
        try {

            $triggerVariable = Bell::triggerVariable()->find($id);

            if (! $triggerVariable) {
                throw (new ModelNotFoundException)->setModel(config('fintech.bell.trigger_variable_model'), $id);
            }

            return new TriggerVariableResource($triggerVariable);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *TriggerVariable* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateTriggerVariableRequest $request, string|int $id): JsonResponse
    {
        try {

            $triggerVariable = Bell::triggerVariable()->find($id);

            if (! $triggerVariable) {
                throw (new ModelNotFoundException)->setModel(config('fintech.bell.trigger_variable_model'), $id);
            }

            $inputs = $request->validated();

            if (! Bell::triggerVariable()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.bell.trigger_variable_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Trigger Variable']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *TriggerVariable* resource using id.
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

            $triggerVariable = Bell::triggerVariable()->find($id);

            if (! $triggerVariable) {
                throw (new ModelNotFoundException)->setModel(config('fintech.bell.trigger_variable_model'), $id);
            }

            if (! Bell::triggerVariable()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.bell.trigger_variable_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Trigger Variable']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *TriggerVariable* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $triggerVariable = Bell::triggerVariable()->find($id, true);

            if (! $triggerVariable) {
                throw (new ModelNotFoundException)->setModel(config('fintech.bell.trigger_variable_model'), $id);
            }

            if (! Bell::triggerVariable()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.bell.trigger_variable_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Trigger Variable']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *TriggerVariable* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexTriggerVariableRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $triggerVariablePaginate = Bell::triggerVariable()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Trigger Variable']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *TriggerVariable* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return TriggerVariableCollection|JsonResponse
     */
    public function import(ImportTriggerVariableRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $triggerVariablePaginate = Bell::triggerVariable()->list($inputs);

            return new TriggerVariableCollection($triggerVariablePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
