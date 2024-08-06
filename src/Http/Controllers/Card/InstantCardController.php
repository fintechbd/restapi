<?php

namespace Fintech\RestApi\Http\Controllers\Card;
use Exception;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Card\Facades\Card;
use Fintech\Core\Enums\Ekyc\InstantCardStatus;
use Fintech\RestApi\Http\Resources\Card\InstantCardResource;
use Fintech\RestApi\Http\Resources\Card\InstantCardCollection;
use Fintech\RestApi\Http\Requests\Card\ImportInstantCardRequest;
use Fintech\RestApi\Http\Requests\Card\StoreInstantCardRequest;
use Fintech\RestApi\Http\Requests\Card\UpdateInstantCardRequest;
use Fintech\RestApi\Http\Requests\Card\UpdateInstantCardStatusRequest;
use Fintech\RestApi\Http\Requests\Card\IndexInstantCardRequest;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class InstantCardController
 * @package Fintech\RestApi\Http\Controllers\Card
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to InstantCard
 * @lrd:end
 *
 */

class InstantCardController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *InstantCard* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexInstantCardRequest $request
     * @return InstantCardCollection|JsonResponse
     */
    public function index(IndexInstantCardRequest $request): InstantCardCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $instantCardPaginate = Card::instantCard()->list($inputs);

            return new InstantCardCollection($instantCardPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *InstantCard* resource in storage.
     * @lrd:end
     *
     * @param StoreInstantCardRequest $request
     * @return JsonResponse
     * @throws StoreOperationException
     */
    public function store(StoreInstantCardRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $instantCard = Card::instantCard()->create($inputs);

            if (!$instantCard) {
                throw (new StoreOperationException)->setModel(config('fintech.card.instant_card_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Instant Card']),
                'id' => $instantCard->id
             ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *InstantCard* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return InstantCardResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): InstantCardResource|JsonResponse
    {
        try {

            $instantCard = Card::instantCard()->find($id);

            if (!$instantCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.instant_card_model'), $id);
            }

            return new InstantCardResource($instantCard);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *InstantCard* resource using id.
     * @lrd:end
     *
     * @param UpdateInstantCardRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateInstantCardRequest $request, string|int $id): JsonResponse
    {
        try {

            $instantCard = Card::instantCard()->find($id);

            if (!$instantCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.instant_card_model'), $id);
            }

            $inputs = $request->validated();

            if (!Card::instantCard()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.card.instant_card_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Instant Card']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *InstantCard* resource using id.
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

            $instantCard = Card::instantCard()->find($id);

            if (!$instantCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.instant_card_model'), $id);
            }

            if (!Card::instantCard()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.card.instant_card_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Instant Card']));

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
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $instantCard = Card::instantCard()->find($id, true);

            if (!$instantCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.instant_card_model'), $id);
            }

            if (!Card::instantCard()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.card.instant_card_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Instant Card']));

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
     *
     * @param IndexInstantCardRequest $request
     * @return JsonResponse
     */
    public function export(IndexInstantCardRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $instantCardPaginate = Card::instantCard()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Instant Card']));

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
     * @param ImportInstantCardRequest $request
     * @return InstantCardCollection|JsonResponse
     */
    public function import(ImportInstantCardRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $instantCardPaginate = Card::instantCard()->list($inputs);

            return new InstantCardCollection($instantCardPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *InstantCard* resource status using id.
     * @lrd:end
     *
     * @param UpdateInstantCardStatusRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function status(UpdateInstantCardStatusRequest $request, string|int $id): JsonResponse
    {
        try {

            $instantCard = Card::instantCard()->find($id);

            if (!$instantCard) {
                throw (new ModelNotFoundException)->setModel(config('fintech.card.instant_card_model'), $id);
            }

            $inputs = $request->validated();

            if (!Card::instantCard()->statusChange($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.card.instant_card_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Instant Card']));

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

            $entries = Card::instantCard()->list($filters)->map(function ($entry) use ($label, $attribute) {
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

            foreach (InstantCardStatus::toArray() as $key => $status) {
                $entries->push(['label' => $status, 'attribute' => $key]);
            }

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception->getMessage());
        }
    }
}
