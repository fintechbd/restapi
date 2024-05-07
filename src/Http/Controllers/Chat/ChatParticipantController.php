<?php

namespace Fintech\Chat\Http\Controllers;

use Exception;
use Fintech\Chat\Facades\Chat;
use Fintech\Chat\Http\Requests\ImportChatParticipantRequest;
use Fintech\Chat\Http\Requests\IndexChatParticipantRequest;
use Fintech\Chat\Http\Requests\StoreChatParticipantRequest;
use Fintech\Chat\Http\Requests\UpdateChatParticipantRequest;
use Fintech\Chat\Http\Resources\ChatParticipantCollection;
use Fintech\Chat\Http\Resources\ChatParticipantResource;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ChatParticipantController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ChatParticipant
 *
 * @lrd:end
 */
class ChatParticipantController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *ChatParticipant* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexChatParticipantRequest $request): ChatParticipantCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatParticipantPaginate = Chat::chatParticipant()->list($inputs);

            return new ChatParticipantCollection($chatParticipantPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ChatParticipant* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreChatParticipantRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatParticipant = Chat::chatParticipant()->create($inputs);

            if (! $chatParticipant) {
                throw (new StoreOperationException)->setModel(config('fintech.chat.chat_participant_model'));
            }

            return $this->created([
                'message' => __('core::messages.resource.created', ['model' => 'Chat Participant']),
                'id' => $chatParticipant->id,
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ChatParticipant* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ChatParticipantResource|JsonResponse
    {
        try {

            $chatParticipant = Chat::chatParticipant()->find($id);

            if (! $chatParticipant) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_participant_model'), $id);
            }

            return new ChatParticipantResource($chatParticipant);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ChatParticipant* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateChatParticipantRequest $request, string|int $id): JsonResponse
    {
        try {

            $chatParticipant = Chat::chatParticipant()->find($id);

            if (! $chatParticipant) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_participant_model'), $id);
            }

            $inputs = $request->validated();

            if (! Chat::chatParticipant()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.chat.chat_participant_model'), $id);
            }

            return $this->updated(__('core::messages.resource.updated', ['model' => 'Chat Participant']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ChatParticipant* resource using id.
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

            $chatParticipant = Chat::chatParticipant()->find($id);

            if (! $chatParticipant) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_participant_model'), $id);
            }

            if (! Chat::chatParticipant()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.chat.chat_participant_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Chat Participant']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ChatParticipant* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $chatParticipant = Chat::chatParticipant()->find($id, true);

            if (! $chatParticipant) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_participant_model'), $id);
            }

            if (! Chat::chatParticipant()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.chat.chat_participant_model'), $id);
            }

            return $this->restored(__('core::messages.resource.restored', ['model' => 'Chat Participant']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChatParticipant* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexChatParticipantRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatParticipantPaginate = Chat::chatParticipant()->export($inputs);

            return $this->exported(__('core::messages.resource.exported', ['model' => 'Chat Participant']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChatParticipant* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ChatParticipantCollection|JsonResponse
     */
    public function import(ImportChatParticipantRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatParticipantPaginate = Chat::chatParticipant()->list($inputs);

            return new ChatParticipantCollection($chatParticipantPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
