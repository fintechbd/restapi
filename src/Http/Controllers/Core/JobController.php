<?php

namespace Fintech\RestApi\Http\Controllers\Core;

use Core;
use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\RestApi\Http\Requests\Core\IndexJobRequest;
use Fintech\RestApi\Http\Resources\Core\JobCollection;
use Fintech\RestApi\Http\Resources\Core\JobResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class JobController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Job
 *
 * @lrd:end
 */
class JobController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Job* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexJobRequest $request): JobCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $jobPaginate = Core::job()->list($inputs);

            return new JobCollection($jobPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Job* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): JobResource|JsonResponse
    {
        try {

            $job = Core::job()->find($id);

            if (! $job) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.job_model'), $id);
            }

            return new JobResource($job);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Job* resource using id.
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

            $job = Core::job()->read($id);

            if (! $job) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.job_model'), $id);
            }

            if (! Core::job()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.core.job_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Job']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
