<?php

namespace Fintech\RestApi\Http\Controllers\Card;

use Exception;
use Fintech\Card\Facades\Card;
use Fintech\Core\Enums\Card\PrepaidCardStatus;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\RestApi\Http\Requests\Card\ImportPrepaidCardRequest;
use Fintech\RestApi\Http\Requests\Card\IndexPrepaidCardRequest;
use Fintech\RestApi\Http\Requests\Card\StorePrepaidCardRequest;
use Fintech\RestApi\Http\Requests\Card\UpdatePrepaidCardRequest;
use Fintech\RestApi\Http\Requests\Card\UpdatePrepaidCardStatusRequest;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Card\PrepaidCardCollection;
use Fintech\RestApi\Http\Resources\Card\PrepaidCardResource;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class PrepaidCardController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to InstantCard
 *
 * @lrd:end
 */
class PrepaidCardController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *InstantCard* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexPrepaidCardRequest $request): PrepaidCardCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $prepaidCardPaginate = Card::prepaidCard()->list($inputs);

            return new PrepaidCardCollection($prepaidCardPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *InstantCard* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StorePrepaidCardRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $prepaidCard = Card::prepaidCard()->create($inputs);

            if (! $prepaidCard) {
                throw (new StoreOperationException)->setModel(config('fintech.card.prepaid_card_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Prepaid Card']),
                'id' => $prepaidCard->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *InstantCard* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): PrepaidCardResource|JsonResponse
    {
        try {

            $prepaidCard = Card::prepaidCard()->find($id);

            if (! $prepaidCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.prepaid_card_model'), $id);
            }

            return new PrepaidCardResource($prepaidCard);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *InstantCard* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdatePrepaidCardRequest $request, string|int $id): JsonResponse
    {
        try {

            $prepaidCard = Card::prepaidCard()->find($id);

            if (! $prepaidCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.prepaid_card_model'), $id);
            }

            $inputs = $request->validated();

            if (! Card::prepaidCard()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.card.prepaid_card_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Prepaid Card']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *InstantCard* resource using id.
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

            $prepaidCard = Card::prepaidCard()->find($id);

            if (! $prepaidCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.prepaid_card_model'), $id);
            }

            if (! Card::prepaidCard()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.card.prepaid_card_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Prepaid Card']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *InstantCard* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $prepaidCard = Card::prepaidCard()->find($id, true);

            if (! $prepaidCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.prepaid_card_model'), $id);
            }

            if (! Card::prepaidCard()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.card.prepaid_card_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Prepaid Card']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *InstantCard* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexPrepaidCardRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $prepaidCardPaginate = Card::prepaidCard()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Prepaid Card']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *InstantCard* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return PrepaidCardCollection|JsonResponse
     */
    public function import(ImportPrepaidCardRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $prepaidCardPaginate = Card::prepaidCard()->list($inputs);

            return new PrepaidCardCollection($prepaidCardPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *InstantCard* resource status using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function status(UpdatePrepaidCardStatusRequest $request, string|int $id): JsonResponse
    {
        try {

            $prepaidCard = Card::prepaidCard()->find($id);

            if (! $prepaidCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.prepaid_card_model'), $id);
            }

            $inputs = $request->validated();

            if (! Card::prepaidCard()->statusChange($prepaidCard, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.card.prepaid_card_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Prepaid Card']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $label = 'name';

            $attribute = 'id';

            if (! empty($filters['label'])) {
                $label = $filters['label'];
                unset($filters['label']);
            }

            if (! empty($filters['attribute'])) {
                $attribute = $filters['attribute'];
                unset($filters['attribute']);
            }

            $entries = Card::prepaidCard()->list($filters)->map(function ($entry) use ($label, $attribute) {
                return [
                    'label' => $entry->{$label} ?? 'name',
                    'attribute' => $entry->{$attribute} ?? 'id',
                ];
            });

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception->getMessage());
        }
    }

    public function statusDropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $entries = collect();

            foreach (PrepaidCardStatus::toArray() as $key => $status) {
                $entries->push(['label' => $status, 'attribute' => $key]);
            }

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception->getMessage());
        }
    }
}
