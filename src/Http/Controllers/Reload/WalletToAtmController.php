<?php

namespace Fintech\RestApi\Http\Controllers\Reload;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Banco\Facades\Banco;
use Fintech\Business\Facades\Business;
use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Core\Enums\Auth\SystemRole;
use Fintech\Core\Enums\Transaction\OrderStatus;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Reload\Facades\Reload;
use Fintech\Remit\Facades\Remit;
use Fintech\RestApi\Http\Requests\Reload\ImportWalletToAtmRequest;
use Fintech\RestApi\Http\Requests\Reload\IndexWalletToAtmRequest;
use Fintech\RestApi\Http\Requests\Reload\StoreWalletToAtmRequest;
use Fintech\RestApi\Http\Requests\Reload\UpdateWalletToAtmRequest;
use Fintech\RestApi\Http\Resources\Reload\WalletToAtmCollection;
use Fintech\RestApi\Http\Resources\Reload\WalletToAtmResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

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
    public function __construct()
    {
        $this->middleware('imposter', ['only' => ['store']]);
    }

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

            $inputs['transaction_form_id'] = Transaction::transactionForm()->list(['code' => 'local_atm_transfer'])->first()->getKey();
            $walletToAtmPaginate = Reload::walletToAtm()->list($inputs);

            return new WalletToAtmCollection($walletToAtmPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
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
        DB::beginTransaction();
        try {
            $inputs = $request->validated();
            if ($request->input('user_id') > 0) {
                $user_id = $request->input('user_id');
            }
            $depositor = $request->user('sanctum');
            if (Transaction::orderQueue()->addToQueueUserWise(($user_id ?? $depositor->getKey())) > 0) {
                $depositAccount = Transaction::userAccount()->list([
                    'user_id' => $user_id ?? $depositor->getKey(),
                    'country_id' => $request->input('source_country_id', $depositor->profile?->country_id),
                ])->first();

                if (!$depositAccount) {
                    throw new Exception("User don't have account deposit balance");
                }

                $masterUser = Auth::user()->list([
                    'role_name' => SystemRole::MasterUser->value,
                    'country_id' => $request->input('source_country_id', $depositor->profile?->country_id),
                ])->first();

                if (!$masterUser) {
                    throw new Exception('Master User Account not found for ' . $request->input('source_country_id', $depositor->profile?->country_id) . ' country');
                }

                //set pre defined conditions of deposit
                $inputs['transaction_form_id'] = Transaction::transactionForm()->list(['code' => 'local_atm_transfer'])->first()->getKey();
                $inputs['user_id'] = $user_id ?? $depositor->getKey();
                $delayCheck = Transaction::order()->transactionDelayCheck($inputs);
                if ($delayCheck['countValue'] > 0) {
                    throw new Exception('Your Request For This Amount Is Already Submitted. Please Wait For Update');
                }
                $inputs['sender_receiver_id'] = $masterUser->getKey();
                $inputs['is_refunded'] = false;
                $inputs['status'] = OrderStatus::Pending->value;
                $inputs['risk'] = RiskProfile::Low->value;
                $inputs['reverse'] = true;
                $inputs['order_data']['currency_convert_rate'] = Business::currencyRate()->convert($inputs);
                unset($inputs['reverse']);
                $inputs['converted_amount'] = $inputs['order_data']['currency_convert_rate']['converted'];
                $inputs['converted_currency'] = $inputs['order_data']['currency_convert_rate']['output'];
                $inputs['order_data']['created_by'] = $depositor->name;
                $inputs['order_data']['created_by_mobile_number'] = $depositor->mobile;
                $inputs['order_data']['created_at'] = now();
                $inputs['order_data']['master_user_name'] = $masterUser['name'];
                //$inputs['order_data']['operator_short_code'] = $request->input('operator_short_code', null);
                $inputs['order_data']['assign_order'] = 'no';
                $inputs['order_data']['system_notification_variable_success'] = 'local_atm_transfer_success';
                $inputs['order_data']['system_notification_variable_failed'] = 'local_atm_transfer_failed';
                unset($inputs['pin'], $inputs['password']);

                $walletToAtm = Reload::walletToAtm()->create($inputs);

                if (!$walletToAtm) {
                    throw (new StoreOperationException)->setModel(config('fintech.reload.wallet_to_atm_model'));
                }
                $order_data = $walletToAtm->order_data;
                $service = Business::service()->find($inputs['service_id']);
                $order_data['service_slug'] = $service->service_slug;
                $order_data['service_name'] = $service->service_name;
                $order_data['purchase_number'] = entry_number($walletToAtm->getKey(), $walletToAtm->sourceCountry->iso3, OrderStatus::Successful->value);
                $order_data['service_stat_data'] = Business::serviceStat()->serviceStateData($walletToAtm);
                $order_data['user_name'] = $walletToAtm->user->name;
                $walletToAtm->order_data = $order_data;
                $userUpdatedBalance = Reload::walletToBank()->debitTransaction($walletToAtm);

                $depositedAccount = Transaction::userAccount()->list([
                    'user_id' => $depositor->getKey(),
                    'country_id' => $walletToAtm->source_country_id,
                ])->first();
                //update User Account
                $depositedUpdatedAccount = $depositedAccount->toArray();
                $depositedUpdatedAccount['user_account_data']['spent_amount'] = (float)$depositedUpdatedAccount['user_account_data']['spent_amount'] + (float)$userUpdatedBalance['spent_amount'];
                $depositedUpdatedAccount['user_account_data']['available_amount'] = (float)$userUpdatedBalance['current_amount'];

                if (((float)$depositedUpdatedAccount['user_account_data']['available_amount']) < ((float)config('fintech.transaction.minimum_balance'))) {
                    throw new Exception(__('Insufficient balance!', [
                        'previous_amount' => ((float)$depositedUpdatedAccount['user_account_data']['available_amount']),
                        'current_amount' => ((float)$userUpdatedBalance['spent_amount']),
                    ]));
                }

                $order_data['previous_amount'] = (float)$depositedAccount->user_account_data['available_amount'];
                $order_data['current_amount'] = ((float)$order_data['previous_amount'] + (float)$inputs['converted_currency']);

                if (!Transaction::userAccount()->update($depositedAccount->getKey(), $depositedUpdatedAccount)) {
                    throw new Exception(__('User Account Balance does not update', [
                        'current_status' => $walletToAtm->currentStatus(),
                        'target_status' => OrderStatus::Success->value,
                    ]));
                }
                //TODO ALL Beneficiary Data with bank and branch data
                $beneficiaryData = Banco::beneficiary()->manageBeneficiaryData($order_data);
                $order_data['beneficiary_data'] = $beneficiaryData;

                Remit::bankTransfer()->update($walletToAtm->getKey(), ['order_data' => $order_data, 'order_number' => $order_data['purchase_number']]);
                Transaction::orderQueue()->removeFromQueueUserWise($user_id ?? $depositor->getKey());

                //event(new RemitTransferRequested('bank_deposit', $walletToAtm));

                DB::commit();

                return response()->created([
                    'message' => __('restapi::messages.resource.created', ['model' => 'Wallet To ATM']),
                    'id' => $walletToAtm->id,
                ]);

            } else {
                throw new Exception('Your another order is in process...!');
            }
        } catch (Exception $exception) {

            DB::rollBack();
            Transaction::orderQueue()->removeFromQueueUserWise($user_id ?? $depositor->getKey());

            return response()->failed($exception);
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

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Wallet To Atm']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
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

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
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

                throw (new DeleteOperationException)->setModel(config('fintech.reload.wallet_to_atm_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Wallet To Atm']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
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

                throw (new RestoreOperationException)->setModel(config('fintech.reload.wallet_to_atm_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Wallet To Atm']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
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

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Wallet To Atm']));

        } catch (Exception $exception) {

            return response()->failed($exception);
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

            return response()->failed($exception);
        }
    }
}
