<?php

namespace Fintech\RestApi\Http\Controllers\Airtime;

use Exception;
use Fintech\Airtime\Facades\Airtime;
use Fintech\Airtime\Jobs\BangladeshTopUp\SslWirelessPackageSyncJob;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Airtime\ImportBangladeshTopUpRequest;
use Fintech\RestApi\Http\Requests\Airtime\IndexBangladeshTopUpRequest;
use Fintech\RestApi\Http\Requests\Airtime\StoreBangladeshTopUpRequest;
use Fintech\RestApi\Http\Requests\Airtime\UpdateBangladeshTopUpRequest;
use Fintech\RestApi\Http\Resources\Airtime\BangladeshTopUpCollection;
use Fintech\RestApi\Http\Resources\Airtime\BangladeshTopUpResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class BangladeshTopUpController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to BangladeshTopUp
 *
 * @lrd:end
 */
class BangladeshTopUpController extends Controller
{
    public function __construct()
    {
        $this->middleware('imposter', ['only' => ['store']]);
    }

    /**
     * @lrd:start
     * Return a listing of the *BangladeshTopUp* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexBangladeshTopUpRequest $request): BangladeshTopUpCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();
            //$inputs['transaction_form_id'] = Transaction::transactionForm()->findWhere(['code' => 'bangladesh_top_up'])->getKey();
            $inputs['transaction_form_code'] = 'bangladesh_top_up';
            //$inputs['service_id'] = Business::serviceType()->list(['service_type_slug'=>'bangladesh_top_up']);
            //$inputs['service_type_slug'] = 'bangladesh_top_up';
            $bangladeshTopUpPaginate = Airtime::bangladeshTopUp()->list($inputs);

            return new BangladeshTopUpCollection($bangladeshTopUpPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *BangladeshTopUp* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreBangladeshTopUpRequest $request): JsonResponse
    {
        $inputs = $request->validated();

        $inputs['user_id'] = ($request->filled('user_id')) ? $request->input('user_id') : $request->user('sanctum')->getKey();

        try {

            $bangladeshTopUp = Airtime::bangladeshTopUp()->create($inputs);

            return response()->created([
                'message' => __('core::messages.transaction.request_created', ['service' => 'Bangladesh TopUp']),
                'id' => $bangladeshTopUp->getKey(),
            ]);

        } catch (Exception $exception) {
            Transaction::orderQueue()->removeFromQueueUserWise($inputs['user_id']);

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *BangladeshTopUp* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateBangladeshTopUpRequest $request, string|int $id): JsonResponse
    {
        try {

            $bangladeshTopUp = Airtime::bangladeshTopUp()->find($id);

            if (! $bangladeshTopUp) {
                throw (new ModelNotFoundException)->setModel(config('fintech.airtime.bangladesh_top_up_model'), $id);
            }

            $inputs = $request->validated();

            if (! Airtime::bangladeshTopUp()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.airtime.bangladesh_top_up_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Bangladesh Top Up']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *BangladeshTopUp* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): BangladeshTopUpResource|JsonResponse
    {
        try {

            $bangladeshTopUp = Airtime::bangladeshTopUp()->find($id);

            if (! $bangladeshTopUp) {
                throw (new ModelNotFoundException)->setModel(config('fintech.airtime.bangladesh_top_up_model'), $id);
            }

            return new BangladeshTopUpResource($bangladeshTopUp);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *BangladeshTopUp* resource using id.
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

            $bangladeshTopUp = Airtime::bangladeshTopUp()->find($id);

            if (! $bangladeshTopUp) {
                throw (new ModelNotFoundException)->setModel(config('fintech.airtime.bangladesh_top_up_model'), $id);
            }

            if (! Airtime::bangladeshTopUp()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.airtime.bangladesh_top_up_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Bangladesh Top Up']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *BangladeshTopUp* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $bangladeshTopUp = Airtime::bangladeshTopUp()->find($id, true);

            if (! $bangladeshTopUp) {
                throw (new ModelNotFoundException)->setModel(config('fintech.airtime.bangladesh_top_up_model'), $id);
            }

            if (! Airtime::bangladeshTopUp()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.airtime.bangladesh_top_up_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Bangladesh Top Up']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *BangladeshTopUp* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexBangladeshTopUpRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bangladeshTopUpPaginate = Airtime::bangladeshTopUp()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Bangladesh Top Up']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *BangladeshTopUp* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return BangladeshTopUpCollection|JsonResponse
     */
    public function import(ImportBangladeshTopUpRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $bangladeshTopUpPaginate = Airtime::bangladeshTopUp()->list($inputs);

            return new BangladeshTopUpCollection($bangladeshTopUpPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @LRDparam pin required|string|min:6
     *
     * @lrd:start
     * Synchronize bangladesh service package allowed and blocked amount
     * packages. initially only for `sslwireless`
     *
     * @lrd:end
     */
    public function sync(Request $request): JsonResponse
    {
        try {

            if ($serviceVendor = Business::serviceVendor()->findWhere(['service_vendor_slug' => 'sslwireless', 'enabled' => true])) {
                SslWirelessPackageSyncJob::dispatch();
            }

            return response()->success(__('airtime::messages.synchronize-queued'));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
