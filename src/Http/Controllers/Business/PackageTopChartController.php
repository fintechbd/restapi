<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\ImportPackageTopChartRequest;
use Fintech\RestApi\Http\Requests\Business\IndexPackageTopChartRequest;
use Fintech\RestApi\Http\Requests\Business\StorePackageTopChartRequest;
use Fintech\RestApi\Http\Requests\Business\UpdatePackageTopChartRequest;
use Fintech\RestApi\Http\Resources\Business\PackageTopChartCollection;
use Fintech\RestApi\Http\Resources\Business\PackageTopChartResource;
use Fintech\RestApi\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class PackageTopChartController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to PackageTopChart
 *
 * @lrd:end
 */
class PackageTopChartController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *PackageTopChart* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexPackageTopChartRequest $request): PackageTopChartCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $packageTopChartPaginate = Business::packageTopChart()->list($inputs);

            return new PackageTopChartCollection($packageTopChartPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *PackageTopChart* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StorePackageTopChartRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $packageTopChart = Business::packageTopChart()->create($inputs);

            if (! $packageTopChart) {
                throw (new StoreOperationException)->setModel(config('fintech.business.package_top_chart_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Package Top Chart']),
                'id' => $packageTopChart->id,
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *PackageTopChart* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): PackageTopChartResource|JsonResponse
    {
        try {

            $packageTopChart = Business::packageTopChart()->find($id);

            if (! $packageTopChart) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.package_top_chart_model'), $id);
            }

            return new PackageTopChartResource($packageTopChart);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *PackageTopChart* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdatePackageTopChartRequest $request, string|int $id): JsonResponse
    {
        try {

            $packageTopChart = Business::packageTopChart()->find($id);

            if (! $packageTopChart) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.package_top_chart_model'), $id);
            }

            $inputs = $request->validated();

            if (! Business::packageTopChart()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.package_top_chart_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'Package Top Chart']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *PackageTopChart* resource using id.
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

            $packageTopChart = Business::packageTopChart()->find($id);

            if (! $packageTopChart) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.package_top_chart_model'), $id);
            }

            if (! Business::packageTopChart()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.business.package_top_chart_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Package Top Chart']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *PackageTopChart* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $packageTopChart = Business::packageTopChart()->find($id, true);

            if (! $packageTopChart) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.package_top_chart_model'), $id);
            }

            if (! Business::packageTopChart()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.business.package_top_chart_model'), $id);
            }

            return $this->restored(__('restapi::messages.resource.restored', ['model' => 'Package Top Chart']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *PackageTopChart* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexPackageTopChartRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $packageTopChartPaginate = Business::packageTopChart()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'Package Top Chart']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *PackageTopChart* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return PackageTopChartCollection|JsonResponse
     */
    public function import(ImportPackageTopChartRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $packageTopChartPaginate = Business::packageTopChart()->list($inputs);

            return new PackageTopChartCollection($packageTopChartPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
