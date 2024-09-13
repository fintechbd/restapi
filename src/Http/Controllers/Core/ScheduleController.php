<?php

namespace Fintech\RestApi\Http\Controllers\Core;
use Exception;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Facades\Core;
use Fintech\RestApi\Http\Resources\Core\ScheduleResource;
use Fintech\RestApi\Http\Resources\Core\ScheduleCollection;
use Fintech\RestApi\Http\Requests\Core\ImportScheduleRequest;
use Fintech\RestApi\Http\Requests\Core\StoreScheduleRequest;
use Fintech\RestApi\Http\Requests\Core\UpdateScheduleRequest;
use Fintech\RestApi\Http\Requests\Core\IndexScheduleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ScheduleController
 * @package Fintech\RestApi\Http\Controllers\Core
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Schedule
 * @lrd:end
 *
 */

class ScheduleController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Schedule* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexScheduleRequest $request
     * @return ScheduleCollection|JsonResponse
     */
    public function index(IndexScheduleRequest $request): ScheduleCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $schedulePaginate = Core::schedule()->list($inputs);

            return new ScheduleCollection($schedulePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *Schedule* resource in storage.
     * @lrd:end
     *
     * @param StoreScheduleRequest $request
     * @return JsonResponse
     * @throws StoreOperationException
     */
    public function store(StoreScheduleRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $schedule = Core::schedule()->create($inputs);

            if (!$schedule) {
                throw (new StoreOperationException)->setModel(config('fintech.core.schedule_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Schedule']),
                'id' => $schedule->id
             ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Schedule* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return ScheduleResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ScheduleResource|JsonResponse
    {
        try {

            $schedule = Core::schedule()->find($id);

            if (!$schedule) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.schedule_model'), $id);
            }

            return new ScheduleResource($schedule);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *Schedule* resource using id.
     * @lrd:end
     *
     * @param UpdateScheduleRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateScheduleRequest $request, string|int $id): JsonResponse
    {
        try {

            $schedule = Core::schedule()->find($id);

            if (!$schedule) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.schedule_model'), $id);
            }

            $inputs = $request->validated();

            if (!Core::schedule()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.core.schedule_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Schedule']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Schedule* resource using id.
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

            $schedule = Core::schedule()->find($id);

            if (!$schedule) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.schedule_model'), $id);
            }

            if (!Core::schedule()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.core.schedule_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Schedule']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Schedule* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $schedule = Core::schedule()->find($id, true);

            if (!$schedule) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.schedule_model'), $id);
            }

            if (!Core::schedule()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.core.schedule_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Schedule']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Schedule* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param IndexScheduleRequest $request
     * @return JsonResponse
     */
    public function export(IndexScheduleRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $schedulePaginate = Core::schedule()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Schedule']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Schedule* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param ImportScheduleRequest $request
     * @return ScheduleCollection|JsonResponse
     */
    public function import(ImportScheduleRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $schedulePaginate = Core::schedule()->list($inputs);

            return new ScheduleCollection($schedulePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
