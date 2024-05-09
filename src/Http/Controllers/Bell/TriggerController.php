<?php

namespace Fintech\RestApi\Http\Controllers\Bell;

use Exception;
use Fintech\Bell\Facades\Bell;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Bell\ImportTriggerRequest;
use Fintech\RestApi\Http\Requests\Bell\IndexTriggerRequest;
use Fintech\RestApi\Http\Requests\Bell\StoreTriggerRequest;
use Fintech\RestApi\Http\Requests\Bell\UpdateTriggerRequest;
use Fintech\RestApi\Http\Resources\Bell\TriggerCollection;
use Fintech\RestApi\Http\Resources\Bell\TriggerResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class TriggerController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Trigger
 *
 * @lrd:end
 */
class TriggerController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Trigger* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexTriggerRequest $request): TriggerCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $triggerPaginate = Bell::trigger()->list($inputs);

            return new TriggerCollection($triggerPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *Trigger* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreTriggerRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $trigger = Bell::trigger()->create($inputs);

            if (!$trigger) {
                throw (new StoreOperationException)->setModel(config('fintech.bell.trigger_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Trigger']),
                'id' => $trigger->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *Trigger* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): TriggerResource|JsonResponse
    {
        try {

            $trigger = Bell::trigger()->find($id);

            if (!$trigger) {
                throw (new ModelNotFoundException)->setModel(config('fintech.bell.trigger_model'), $id);
            }

            return new TriggerResource($trigger);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *Trigger* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateTriggerRequest $request, string|int $id): JsonResponse
    {
        try {

            $trigger = Bell::trigger()->find($id);

            if (!$trigger) {
                throw (new ModelNotFoundException)->setModel(config('fintech.bell.trigger_model'), $id);
            }

            $inputs = $request->validated();

            if (!Bell::trigger()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.bell.trigger_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'Trigger']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Trigger* resource using id.
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

            $trigger = Bell::trigger()->find($id);

            if (!$trigger) {
                throw (new ModelNotFoundException)->setModel(config('fintech.bell.trigger_model'), $id);
            }

            if (!Bell::trigger()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.bell.trigger_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Trigger']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Trigger* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $trigger = Bell::trigger()->find($id, true);

            if (!$trigger) {
                throw (new ModelNotFoundException)->setModel(config('fintech.bell.trigger_model'), $id);
            }

            if (!Bell::trigger()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.bell.trigger_model'), $id);
            }

            return $this->restored(__('restapi::messages.resource.restored', ['model' => 'Trigger']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Trigger* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexTriggerRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $triggerPaginate = Bell::trigger()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'Trigger']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Trigger* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return TriggerCollection|JsonResponse
     */
    public function import(ImportTriggerRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $triggerPaginate = Bell::trigger()->list($inputs);

            return new TriggerCollection($triggerPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * This api will search for all available triggers in the system
     * then update the trigger table accordingly.
     *
     * @lrd:end
     */
    public function sync(): JsonResponse
    {
        Bell::trigger()->sync();
        try {
            return $this->success(__('bell::messages.trigger.synced'));
        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
