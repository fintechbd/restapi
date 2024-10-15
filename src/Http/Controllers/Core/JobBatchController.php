<?php

namespace Fintech\RestApi\Http\Controllers\Core;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Facades\Core;
use Fintech\RestApi\Http\Requests\Core\ImportJobBatchRequest;
use Fintech\RestApi\Http\Requests\Core\IndexJobBatchRequest;
use Fintech\RestApi\Http\Requests\Core\StoreJobBatchRequest;
use Fintech\RestApi\Http\Requests\Core\UpdateJobBatchRequest;
use Fintech\RestApi\Http\Resources\Core\JobBatchCollection;
use Fintech\RestApi\Http\Resources\Core\JobBatchResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class JobBatchController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to JobBatch
 *
 * @lrd:end
 */
class JobBatchController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *JobBatch* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexJobBatchRequest $request): JobBatchCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $jobBatchPaginate = Core::jobBatch()->list($inputs);

            return new JobBatchCollection($jobBatchPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *JobBatch* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreJobBatchRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $jobBatch = Core::jobBatch()->create($inputs);

            if (! $jobBatch) {
                throw (new StoreOperationException)->setModel(config('fintech.core.job_batch_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Job Batch']),
                'id' => $jobBatch->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *JobBatch* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): JobBatchResource|JsonResponse
    {
        try {

            $jobBatch = Core::jobBatch()->find($id);

            if (! $jobBatch) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.job_batch_model'), $id);
            }

            return new JobBatchResource($jobBatch);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *JobBatch* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateJobBatchRequest $request, string|int $id): JsonResponse
    {
        try {

            $jobBatch = Core::jobBatch()->find($id);

            if (! $jobBatch) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.job_batch_model'), $id);
            }

            $inputs = $request->validated();

            if (! Core::jobBatch()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.core.job_batch_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Job Batch']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *JobBatch* resource using id.
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

            $jobBatch = Core::jobBatch()->find($id);

            if (! $jobBatch) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.job_batch_model'), $id);
            }

            if (! Core::jobBatch()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.core.job_batch_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Job Batch']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *JobBatch* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $jobBatch = Core::jobBatch()->find($id, true);

            if (! $jobBatch) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.job_batch_model'), $id);
            }

            if (! Core::jobBatch()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.core.job_batch_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Job Batch']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *JobBatch* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexJobBatchRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $jobBatchPaginate = Core::jobBatch()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Job Batch']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *JobBatch* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return JobBatchCollection|JsonResponse
     */
    public function import(ImportJobBatchRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $jobBatchPaginate = Core::jobBatch()->list($inputs);

            return new JobBatchCollection($jobBatchPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
