<?php

namespace Fintech\RestApi\Http\Controllers\Reload;

use BackedEnum;
use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Business\Facades\Business;
use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Core\Enums\Auth\SystemRole;
use Fintech\Core\Enums\Reload\DepositStatus;
use Fintech\Core\Enums\Transaction\OrderStatusConfig;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Reload\Events\DepositAccepted;
use Fintech\Reload\Events\DepositCancelled;
use Fintech\Reload\Events\DepositReceived;
use Fintech\Reload\Events\DepositRejected;
use Fintech\Reload\Facades\Reload;
use Fintech\RestApi\Http\Requests\Reload\CheckDepositRequest;
use Fintech\RestApi\Http\Requests\Reload\ImportDepositRequest;
use Fintech\RestApi\Http\Requests\Reload\IndexDepositRequest;
use Fintech\RestApi\Http\Requests\Reload\StoreDepositRequest;
use Fintech\RestApi\Http\Resources\Reload\DepositCollection;
use Fintech\RestApi\Http\Resources\Reload\DepositResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class DepositController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Deposit
 *
 * @lrd:end
 */
class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('imposter', ['only' => ['store', 'reject', 'accept', 'cancel']]);
    }

    /**
     * @lrd:start
     * Return a listing of the *Deposit* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexDepositRequest $request): DepositCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();
            //$inputs['transaction_form_id'] = Transaction::transactionForm()->list(['code' => 'point_reload'])->first()->getKey();
            $inputs['transaction_form_code'] = 'point_reload';
            //$inputs['service_id'] = Business::serviceType()->list(['service_type_slug'=>'bangladesh_top_up']);
            //$inputs['service_type_slug'] = 'fund_deposit';
            $depositPaginate = Reload::deposit()->list($inputs);
            //            $depositPaginate = Transaction::order()->list($inputs);

            return new DepositCollection($depositPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *Deposit* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreDepositRequest $request): JsonResponse
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

                $deposit = Reload::deposit()->create($inputs);

                if (! $deposit) {
                    throw (new StoreOperationException)->setModel(config('fintech.reload.deposit_model'));
                }

                $order_data = $deposit->order_data;
                $order_data['purchase_number'] = entry_number($deposit->getKey(), $deposit->sourceCountry->iso3, OrderStatusConfig::Purchased->value);

                Reload::deposit()->update($deposit->getKey(), ['order_data' => $order_data, 'order_number' => $order_data['purchase_number']]);

                Transaction::orderQueue()->removeFromQueueUserWise($user_id);

                event(new DepositReceived($deposit));

                return response()->created([
                    'message' => __('restapi::messages.resource.created', ['model' => 'Deposit']),
                    'id' => $deposit->id,
                ]);

            } else {
                throw new Exception('Your another order is in process...!');
            }

        } catch (Exception $exception) {
            Transaction::orderQueue()->removeFromQueueUserWise($user_id);

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Deposit* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): DepositResource|JsonResponse
    {
        try {

            $deposit = Reload::deposit()->find($id);

            if (! $deposit) {
                throw (new ModelNotFoundException)->setModel(config('fintech.reload.deposit_model'), $id);
            }

            return new DepositResource($deposit);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Reject a  specified *Deposit* resource found by id.
     * if and only if deposit status is processing
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function reject(CheckDepositRequest $request, string|int $id): JsonResponse
    {
        try {
            if (Transaction::orderQueue()->addToQueueOrderWise($id) > 0) {
                $deposit = $this->authenticateDeposit($id, DepositStatus::Processing, DepositStatus::Rejected);

                $approver = $request->user('sanctum');

                $updateData = $deposit->toArray();
                $updateData['status'] = DepositStatus::Rejected->value;
                $updateData['order_data']['rejected_by'] = $approver->name;
                $updateData['order_data']['rejected_at'] = now();
                $updateData['order_data']['rejected_number'] = entry_number($deposit->getKey(), $deposit->sourceCountry->iso3, OrderStatusConfig::Rejected->value);
                $updateData['order_number'] = entry_number($deposit->getKey(), $deposit->sourceCountry->iso3, OrderStatusConfig::Rejected->value);
                $updateData['order_data']['rejected_by_mobile_number'] = $approver->mobile;
                $updateData['order_data']['previous_amount'] = $depositAccount->user_account_data['available_amount'] ?? 0;
                $updateData['order_data']['current_amount'] = $updateData['order_data']['previous_amount'] - $updateData['amount'];

                if (! Reload::deposit()->update($deposit->getKey(), $updateData)) {
                    throw new Exception(__('reload::messages.status_change_failed', [
                        'current_status' => $deposit->currentStatus(),
                        'target_status' => DepositStatus::Rejected->name,
                    ]));
                }

                Transaction::orderQueue()->removeFromQueueOrderWise($id);

                event(new DepositRejected($deposit));

                return response()->success(__('reload::messages.deposit.status_change_success', [
                    'status' => DepositStatus::Rejected->name,
                ]));
            } else {
                throw new Exception('Your another order is in process...!');
            }

        } catch (ModelNotFoundException $exception) {
            Transaction::orderQueue()->removeFromQueueOrderWise($id);

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {
            Transaction::orderQueue()->removeFromQueueOrderWise($id);

            return response()->failed($exception);
        }
    }

    /**
     * @throws Exception
     */
    private function authenticateDeposit(string|int $id, BackedEnum $requiredStatus, BackedEnum $targetStatus)
    {
        $deposit = Reload::deposit()->find($id);

        if (! $deposit) {
            throw (new ModelNotFoundException)->setModel(config('fintech.reload.deposit_model'), $id);
        }

        if ($deposit->currentStatus() != $requiredStatus->value) {
            throw new Exception(__('reload::messages.deposit.invalid_status', [
                'current_status' => $deposit->currentStatus(),
                'target_status' => $targetStatus->name,
            ])
            );
        }

        return $deposit;
    }

    /**
     * @lrd:start
     * Accept a  specified *Deposit* resource found by id.
     * if and only if deposit status is processing
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function accept(CheckDepositRequest $request, string|int $id): JsonResponse
    {
        try {
            if (Transaction::orderQueue()->addToQueueOrderWise($id) > 0) {
                $deposit = $this->authenticateDeposit($id, DepositStatus::Processing, DepositStatus::Accepted);

                $depositor = $deposit->user;

                $depositedAccount = Transaction::userAccount()->list([
                    'user_id' => $depositor->getKey(),
                    'country_id' => $deposit->destination_country_id,
                ])->first();

                $updateData = $deposit->toArray();
                $updateData['status'] = DepositStatus::Accepted->value;
                $updateData['order_data']['accepted_by'] = $request->user('sanctum')->name;
                $updateData['order_data']['accepted_at'] = now();
                $updateData['order_data']['accepted_number'] = entry_number($deposit->getKey(), $deposit->sourceCountry->iso3, OrderStatusConfig::Accepted->value);
                $updateData['order_number'] = entry_number($deposit->getKey(), $deposit->sourceCountry->iso3, OrderStatusConfig::Accepted->value);
                $updateData['order_data']['accepted_by_mobile_number'] = $request->user('sanctum')->mobile;
                $updateData['order_data']['service_stat_data'] = Business::serviceStat()->serviceStateData($deposit);
                $updateData['order_data']['user_name'] = $depositor->name;

                $updateData['order_data']['previous_amount'] = $depositedAccount->user_account_data['available_amount'];
                $updateData['order_data']['current_amount'] = ($updateData['order_data']['previous_amount'] + $updateData['amount']);

                if (! Reload::deposit()->update($deposit->getKey(), $updateData)) {
                    throw new Exception(__('reload::messages.status_change_failed', [
                        'current_status' => $deposit->currentStatus(),
                        'target_status' => DepositStatus::Accepted->name,
                    ]));
                }

                $transactionOrder = Reload::deposit()->find($deposit->getKey());

                $userUpdatedBalance = Reload::deposit()->accept($transactionOrder);

                //update User Account
                $depositedUpdatedAccount = $depositedAccount->toArray();
                $depositedUpdatedAccount['user_account_data']['deposit_amount'] = (float) $depositedUpdatedAccount['user_account_data']['deposit_amount'] + (float) $userUpdatedBalance['deposit_amount'];
                $depositedUpdatedAccount['user_account_data']['available_amount'] = (float) $userUpdatedBalance['current_amount'];

                if (! Transaction::userAccount()->update($depositedAccount->getKey(), $depositedUpdatedAccount)) {
                    throw new Exception(__('reload::messages.status_change_failed', [
                        'current_status' => $deposit->currentStatus(),
                        'target_status' => DepositStatus::Accepted->name,
                    ]));
                }

                Transaction::orderQueue()->removeFromQueueOrderWise($id);

                event(new DepositAccepted($deposit));

                return response()->success(__('reload::messages.deposit.status_change_success', [
                    'status' => DepositStatus::Accepted->name,
                ]));
            } else {
                throw new Exception('Your another order is in process...!');
            }

        } catch (ModelNotFoundException $exception) {
            Transaction::orderQueue()->removeFromQueueOrderWise($id);

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {
            Transaction::orderQueue()->removeFromQueueOrderWise($id);

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Cancel a  specified *Deposit* resource found by id.
     * if and only if deposit status is accepted
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function cancel(CheckDepositRequest $request, string|int $id): JsonResponse
    {
        try {
            if (Transaction::orderQueue()->addToQueueOrderWise($id) > 0) {
                $deposit = $this->authenticateDeposit($id, DepositStatus::Accepted, DepositStatus::Cancelled);

                $depositor = $deposit->user;

                $depositedAccount = Transaction::userAccount()->list([
                    'user_id' => $depositor->getKey(),
                    'country_id' => $deposit->destination_country_id,
                ])->first();

                $updateData = $deposit->toArray();
                $updateData['status'] = DepositStatus::Cancelled->value;
                $updateData['order_data']['cancelled_by'] = $request->user('sanctum')->name;
                $updateData['order_data']['cancelled_at'] = now();
                $updateData['order_data']['cancelled_number'] = entry_number($deposit->getKey(), $deposit->sourceCountry->iso3, OrderStatusConfig::Cancelled->value);
                $updateData['order_number'] = entry_number($deposit->getKey(), $deposit->sourceCountry->iso3, OrderStatusConfig::Cancelled->value);
                $updateData['order_data']['cancelled_by_mobile_number'] = $request->user('sanctum')->mobile;

                $updateData['order_data']['previous_amount'] = $updateData['order_data']['current_amount'];
                $updateData['order_data']['current_amount'] = ($updateData['order_data']['current_amount'] - $deposit->amount);

                if (! Reload::deposit()->update($deposit->getKey(), $updateData)) {
                    throw new Exception(__('reload::messages.status_change_failed', [
                        'current_status' => $deposit->currentStatus(),
                        'target_status' => DepositStatus::Cancelled->name,
                    ]));
                }

                $transactionOrder = Reload::deposit()->find($deposit->getKey());
                $updatedUserBalance = Reload::deposit()->cancel($transactionOrder);

                //update User Account
                $depositedUpdatedAccount = $depositedAccount->toArray();
                $depositedUpdatedAccount['user_account_data']['deposit_amount'] = (float) $depositedUpdatedAccount['user_account_data']['deposit_amount'] + (float) $updatedUserBalance['deposit_amount'];
                $depositedUpdatedAccount['user_account_data']['available_amount'] = (float) $updatedUserBalance['current_amount'];

                if (! Transaction::userAccount()->update($depositedAccount->getKey(), $depositedUpdatedAccount)) {
                    throw new Exception(__('reload::messages.status_change_failed', [
                        'current_status' => $deposit->currentStatus(),
                        'target_status' => DepositStatus::Accepted->name,
                    ]));
                }

                Transaction::orderQueue()->removeFromQueueOrderWise($id);

                event(new DepositCancelled($deposit));

                return response()->success(__('reload::messages.deposit.status_change_success', [
                    'status' => DepositStatus::Cancelled->name,
                ]));
            } else {
                throw new Exception('Your another order is in process...!');
            }
        } catch (ModelNotFoundException $exception) {
            Transaction::orderQueue()->removeFromQueueOrderWise($id);

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {
            Transaction::orderQueue()->removeFromQueueOrderWise($id);

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Deposit* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexDepositRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $depositPaginate = Reload::deposit()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Deposit']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *Deposit* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportDepositRequest $request): DepositCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $depositPaginate = Reload::deposit()->list($inputs);

            return new DepositCollection($depositPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
