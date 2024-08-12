<?php

namespace Fintech\RestApi\Http\Controllers\Reload;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Core\Enums\Auth\SystemRole;
use Fintech\Core\Enums\Reload\DepositStatus;
use Fintech\Core\Enums\Transaction\OrderStatusConfig;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Reload\Events\DepositReceived;
use Fintech\Reload\Facades\Reload;
use Fintech\RestApi\Http\Requests\Reload\ImportWalletToPrepaidCardRequest;
use Fintech\RestApi\Http\Requests\Reload\IndexWalletToPrepaidCardRequest;
use Fintech\RestApi\Http\Requests\Reload\StoreWalletToPrepaidCardRequest;
use Fintech\RestApi\Http\Requests\Reload\UpdateWalletToPrepaidCardRequest;
use Fintech\RestApi\Http\Resources\Reload\WalletToPrepaidCardCollection;
use Fintech\RestApi\Http\Resources\Reload\WalletToPrepaidCardResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class WalletToPrepaidCardController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to WalletToPrepaidCard
 *
 * @lrd:end
 */
class WalletToPrepaidCardController extends Controller
{
    public function __construct()
    {
        $this->middleware('imposter', ['only' => ['store']]);
    }

    /**
     * @lrd:start
     * Return a listing of the *WalletToPrepaidCard* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexWalletToPrepaidCardRequest $request): WalletToPrepaidCardCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToPrepaidCardPaginate = Reload::walletToPrepaidCard()->list($inputs);

            return new WalletToPrepaidCardCollection($walletToPrepaidCardPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *WalletToPrepaidCard* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreWalletToPrepaidCardRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            if (isset($inputs['user_id']) && $request->input('user_id') > 0) {
                $user_id = $request->input('user_id');
            }
            $depositor = $request->user('sanctum');

            if (Transaction::orderQueue()->addToQueueUserWise(($user_id ?? $depositor->getKey())) > 0) {

                $depositAccount = Transaction::userAccount()->list([
                    'user_id' => $user_id ?? $depositor->getKey(),
                    'country_id' => $request->input('source_country_id', $depositor->profile?->country_id),
                ])->first();

                if (! $depositAccount) {
                    throw new Exception("User don't have account deposit balance");
                }

                $masterUser = Auth::user()->list([
                    'role_name' => SystemRole::MasterUser->value,
                    'country_id' => $request->input('source_country_id', $depositor->profile?->country_id),
                ])->first();

                if (! $masterUser) {
                    throw new Exception('Master User Account not found for '.$request->input('source_country_id', $depositor->profile?->country_id).' country');
                }

                //set pre defined conditions of deposit
                $inputs['transaction_form_id'] = Transaction::transactionForm()->list(['code' => 'point_reload'])->first()->getKey();
                $inputs['user_id'] = $user_id ?? $depositor->getKey();
                $delayCheck = Transaction::order()->transactionDelayCheck($inputs);
                if ($delayCheck['countValue'] > 0) {
                    throw new Exception('Your Request For This Amount Is Already Submitted. Please Wait For Update');
                }
                $inputs['sender_receiver_id'] = $masterUser->getKey();
                $inputs['is_refunded'] = false;
                $inputs['status'] = DepositStatus::Processing->value;
                $inputs['risk'] = RiskProfile::Low->value;
                $inputs['order_data']['created_by'] = $depositor->name;
                $inputs['order_data']['created_by_mobile_number'] = $depositor->mobile;
                $inputs['order_data']['created_at'] = now();
                $inputs['order_data']['current_amount'] = ($depositAccount->user_account_data['available_amount'] ?? 0) + $inputs['amount'];
                $inputs['order_data']['previous_amount'] = $depositAccount->user_account_data['available_amount'] ?? 0;
                $inputs['converted_amount'] = $inputs['amount'];
                $inputs['converted_currency'] = $inputs['currency'];
                $inputs['order_data']['master_user_name'] = $masterUser['name'];
                unset($inputs['pin'], $inputs['password']);

                $walletToPrepaidCard = Reload::walletToPrepaidCard()->create($inputs);

                if (! $walletToPrepaidCard) {
                    throw (new StoreOperationException)->setModel(config('fintech.reload.wallet_to_prepaid_card_model'));
                }

                $order_data = $walletToPrepaidCard->order_data;
                $order_data['purchase_number'] = entry_number($walletToPrepaidCard->getKey(), $walletToPrepaidCard->sourceCountry->iso3, OrderStatusConfig::Purchased->value);

                Reload::walletToPrepaidCard()->update($walletToPrepaidCard->getKey(), ['order_data' => $order_data, 'order_number' => $order_data['purchase_number']]);

                Transaction::orderQueue()->removeFromQueueUserWise($user_id);

                event(new DepositReceived($walletToPrepaidCard));

                return response()->created([
                    'message' => __('restapi::messages.resource.created', ['model' => 'Wallet To Prepaid Card']),
                    'id' => $walletToPrepaidCard->id,
                ]);

            } else {
                throw new Exception('Your another order is in process...!');
            }

        } catch (Exception $exception) {

            Transaction::orderQueue()->removeFromQueueUserWise($user_id);

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *WalletToPrepaidCard* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): WalletToPrepaidCardResource|JsonResponse
    {
        try {

            $walletToPrepaidCard = Reload::walletToPrepaidCard()->find($id);

            if (! $walletToPrepaidCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_prepaid_card_model'), $id);
            }

            return new WalletToPrepaidCardResource($walletToPrepaidCard);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *WalletToPrepaidCard* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateWalletToPrepaidCardRequest $request, string|int $id): JsonResponse
    {
        try {

            $walletToPrepaidCard = Reload::walletToPrepaidCard()->find($id);

            if (! $walletToPrepaidCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_prepaid_card_model'), $id);
            }

            $inputs = $request->validated();

            if (! Reload::walletToPrepaidCard()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.reload.wallet_to_prepaid_card_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Wallet To Prepaid Card']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *WalletToPrepaidCard* resource using id.
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

            $walletToPrepaidCard = Reload::walletToPrepaidCard()->find($id);

            if (! $walletToPrepaidCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_prepaid_card_model'), $id);
            }

            if (! Reload::walletToPrepaidCard()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.reload.wallet_to_prepaid_card_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Wallet To Prepaid Card']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *WalletToPrepaidCard* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $walletToPrepaidCard = Reload::walletToPrepaidCard()->find($id, true);

            if (! $walletToPrepaidCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.wallet_to_prepaid_card_model'), $id);
            }

            if (! Reload::walletToPrepaidCard()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.reload.wallet_to_prepaid_card_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Wallet To Prepaid Card']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *WalletToPrepaidCard* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexWalletToPrepaidCardRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToPrepaidCardPaginate = Reload::walletToPrepaidCard()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Wallet To Prepaid Card']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *WalletToPrepaidCard* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return WalletToPrepaidCardCollection|JsonResponse
     */
    public function import(ImportWalletToPrepaidCardRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $walletToPrepaidCardPaginate = Reload::walletToPrepaidCard()->list($inputs);

            return new WalletToPrepaidCardCollection($walletToPrepaidCardPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
