<?php

namespace Fintech\RestApi\Http\Controllers\Core;

use Core;
use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\RestApi\Http\Requests\Core\IndexJobRequest;
use Fintech\RestApi\Http\Resources\Core\JobCollection;
use Fintech\RestApi\Http\Resources\Core\JobResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class JobController
 * @package Fintech\Core\Http\Controllers
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Job
 * @lrd:end
 *
 */
class JobController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *Job* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexJobRequest $request
     * @return JobCollection|JsonResponse
     */
    public function index(IndexJobRequest $request): JobCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $jobPaginate = Core::job()->list($inputs);

            return new JobCollection($jobPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *Job* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return JobResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): JobResource|JsonResponse
    {
        try {

            $job = Core::job()->find($id);

            if (!$job) {
                throw (new ModelNotFoundException())->setModel(config('fintech.core.job_model'), $id);
            }

            return new JobResource($job);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Job* resource using id.
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

            $job = Core::job()->read($id);

            if (!$job) {
                throw (new ModelNotFoundException())->setModel(config('fintech.core.job_model'), $id);
            }

            if (!Core::job()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.core.job_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Job']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

}
