<?php

namespace Fintech\RestApi\Http\Controllers\Reload;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Reload\Facades\Reload;
use Fintech\RestApi\Http\Requests\Reload\ImportWalletToAtmRequest;
use Fintech\RestApi\Http\Requests\Reload\IndexWalletToAtmRequest;
use Fintech\RestApi\Http\Requests\Reload\StoreWalletToAtmRequest;
use Fintech\RestApi\Http\Requests\Reload\UpdateWalletToAtmRequest;
use Fintech\RestApi\Http\Resources\Reload\WalletToAtmCollection;
use Fintech\RestApi\Http\Resources\Reload\WalletToAtmResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class WalletToAtmController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to WalletToAtm
 *
 * @lrd:end
 */
class WalletToAtmController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *WalletToAtm* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexWalletToAtmRequest $request): WalletToAtmCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToAtmPaginate = Reload::walletToAtm()->list($inputs);

            return new WalletToAtmCollection($walletToAtmPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *WalletToAtm* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreWalletToAtmRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToAtm = Reload::walletToAtm()->create($inputs);

            if (!$walletToAtm) {
                throw (new StoreOperationException)->setModel(config('fintech.reload.wallet_to_atm_model'));
            }

            return $this->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Wallet To Atm']),
                'id' => $walletToAtm->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *WalletToAtm* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): WalletToAtmResource|JsonResponse
    {
        try {

            $walletToAtm = Reload::walletToAtm()->find($id);

            if (!$walletToAtm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_atm_model'), $id);
            }

            return new WalletToAtmResource($walletToAtm);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *WalletToAtm* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateWalletToAtmRequest $request, string|int $id): JsonResponse
    {
        try {

            $walletToAtm = Reload::walletToAtm()->find($id);

            if (!$walletToAtm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_atm_model'), $id);
            }

            $inputs = $request->validated();

            if (!Reload::walletToAtm()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.reload.wallet_to_atm_model'), $id);
            }

            return $this->updated(__('restapi::messages.resource.updated', ['model' => 'Wallet To Atm']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *WalletToAtm* resource using id.
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

            $walletToAtm = Reload::walletToAtm()->find($id);

            if (!$walletToAtm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_atm_model'), $id);
            }

            if (!Reload::walletToAtm()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.reload.wallet_to_atm_model'), $id);
            }

            return $this->deleted(__('restapi::messages.resource.deleted', ['model' => 'Wallet To Atm']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *WalletToAtm* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $walletToAtm = Reload::walletToAtm()->find($id, true);

            if (!$walletToAtm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_atm_model'), $id);
            }

            if (!Reload::walletToAtm()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.reload.wallet_to_atm_model'), $id);
            }

            return $this->restored(__('restapi::messages.resource.restored', ['model' => 'Wallet To Atm']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *WalletToAtm* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexWalletToAtmRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToAtmPaginate = Reload::walletToAtm()->export($inputs);

            return $this->exported(__('restapi::messages.resource.exported', ['model' => 'Wallet To Atm']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *WalletToAtm* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return WalletToAtmCollection|JsonResponse
     */
    public function import(ImportWalletToAtmRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToAtmPaginate = Reload::walletToAtm()->list($inputs);

            return new WalletToAtmCollection($walletToAtmPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
