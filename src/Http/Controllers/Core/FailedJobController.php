<?php

namespace Fintech\RestApi\Http\Controllers\Core;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Facades\Core;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\RestApi\Http\Requests\Core\IndexFailedJobRequest;
use Fintech\RestApi\Http\Resources\Core\FailedJobCollection;
use Fintech\RestApi\Http\Resources\Core\FailedJobResource;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

/**
 * Class FailedJobController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to FailedJob
 *
 * @lrd:end
 */
class FailedJobController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *FailedJob* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexFailedJobRequest $request): FailedJobCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $failedJobPaginate = Core::failedJob()->list($inputs);

            return new FailedJobCollection($failedJobPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *FailedJob* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): FailedJobResource|JsonResponse
    {
        try {

            $failedJob = Core::failedJob()->find($id);

            if (! $failedJob) {
                throw (new ModelNotFoundException())->setModel(config('fintech.core.failed_job_model'), $id);
            }

            return new FailedJobResource($failedJob);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *FailedJob* resource using id.
     *
     * @lrd:end
     *
     * @return JsonResponse
     *
     * @throws ModelNotFoundException
     */
    public function destroy(string|int $id)
    {
        try {

            $failedJob = Core::failedJob()->find($id);

            if (! $failedJob) {
                throw (new ModelNotFoundException())->setModel(config('fintech.core.failed_job_model'), $id);
            }

            if (! Core::failedJob()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.core.failed_job_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Failed Job']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *FailedJob* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function retry(string|int $id)
    {
        try {

            $failedJob = Core::failedJob()->find($id);

            if (! $failedJob) {
                throw (new ModelNotFoundException())->setModel(config('fintech.core.failed_job_model'), $id);
            }

            if (Artisan::call('queue:retry', ['id' => [$id]])) {

            }

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *FailedJob* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function prune(Request $request)
    {
        try {

            if (Artisan::call('queue:flush') == Command::SUCCESS) {
                return $this->success(__('restapi::messages.resource.restored', ['model' => 'Failed Job']));
            }

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
