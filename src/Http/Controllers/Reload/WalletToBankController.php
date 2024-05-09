<?php

namespace Fintech\RestApi\Http\Controllers\Reload;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Reload\Facades\Reload;
use Fintech\RestApi\Http\Requests\Reload\ImportWalletToBankRequest;
use Fintech\RestApi\Http\Requests\Reload\IndexWalletToBankRequest;
use Fintech\RestApi\Http\Requests\Reload\StoreWalletToBankRequest;
use Fintech\RestApi\Http\Requests\Reload\UpdateWalletToBankRequest;
use Fintech\RestApi\Http\Resources\Reload\WalletToBankCollection;
use Fintech\RestApi\Http\Resources\Reload\WalletToBankResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class WalletToBankController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to WalletToBank
 *
 * @lrd:end
 */
class WalletToBankController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *WalletToBank* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexWalletToBankRequest $request): WalletToBankCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToBankPaginate = Reload::walletToBank()->list($inputs);

            return new WalletToBankCollection($walletToBankPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *WalletToBank* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreWalletToBankRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToBank = Reload::walletToBank()->create($inputs);

            if (!$walletToBank) {
                throw (new StoreOperationException)->setModel(config('fintech.reload.wallet_to_bank_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Wallet To Bank']),
                'id' => $walletToBank->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *WalletToBank* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): WalletToBankResource|JsonResponse
    {
        try {

            $walletToBank = Reload::walletToBank()->find($id);

            if (!$walletToBank) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_bank_model'), $id);
            }

            return new WalletToBankResource($walletToBank);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *WalletToBank* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateWalletToBankRequest $request, string|int $id): JsonResponse
    {
        try {

            $walletToBank = Reload::walletToBank()->find($id);

            if (!$walletToBank) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_bank_model'), $id);
            }

            $inputs = $request->validated();

            if (!Reload::walletToBank()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.reload.wallet_to_bank_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Wallet To Bank']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *WalletToBank* resource using id.
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

            $walletToBank = Reload::walletToBank()->find($id);

            if (!$walletToBank) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_bank_model'), $id);
            }

            if (!Reload::walletToBank()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.reload.wallet_to_bank_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Wallet To Bank']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *WalletToBank* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $walletToBank = Reload::walletToBank()->find($id, true);

            if (!$walletToBank) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_bank_model'), $id);
            }

            if (!Reload::walletToBank()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.reload.wallet_to_bank_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Wallet To Bank']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *WalletToBank* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexWalletToBankRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToBankPaginate = Reload::walletToBank()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Wallet To Bank']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *WalletToBank* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return WalletToBankCollection|JsonResponse
     */
    public function import(ImportWalletToBankRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToBankPaginate = Reload::walletToBank()->list($inputs);

            return new WalletToBankCollection($walletToBankPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
