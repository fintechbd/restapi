<?php

namespace Fintech\Chat\Http\Controllers;

use Exception;
use Fintech\Chat\Facades\Chat;
use Fintech\Chat\Http\Requests\ImportChatMessageRequest;
use Fintech\Chat\Http\Requests\IndexChatMessageRequest;
use Fintech\Chat\Http\Requests\StoreChatMessageRequest;
use Fintech\Chat\Http\Requests\UpdateChatMessageRequest;
use Fintech\Chat\Http\Resources\ChatMessageCollection;
use Fintech\Chat\Http\Resources\ChatMessageResource;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ChatMessageController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ChatMessage
 *
 * @lrd:end
 */
class ChatMessageController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ChatMessage* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexChatMessageRequest $request): ChatMessageCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatMessagePaginate = Chat::chatMessage()->list($inputs);

            return new ChatMessageCollection($chatMessagePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *ChatMessage* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreChatMessageRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatMessage = Chat::chatMessage()->create($inputs);

            if (! $chatMessage) {
                throw (new StoreOperationException)->setModel(config('fintech.chat.chat_message_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Chat Message']),
                'id' => $chatMessage->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *ChatMessage* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ChatMessageResource|JsonResponse
    {
        try {

            $chatMessage = Chat::chatMessage()->find($id);

            if (! $chatMessage) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_message_model'), $id);
            }

            return new ChatMessageResource($chatMessage);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *ChatMessage* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateChatMessageRequest $request, string|int $id): JsonResponse
    {
        try {

            $chatMessage = Chat::chatMessage()->find($id);

            if (! $chatMessage) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_message_model'), $id);
            }

            $inputs = $request->validated();

            if (! Chat::chatMessage()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.chat.chat_message_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Chat Message']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ChatMessage* resource using id.
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

            $chatMessage = Chat::chatMessage()->find($id);

            if (! $chatMessage) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_message_model'), $id);
            }

            if (! Chat::chatMessage()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.chat.chat_message_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Chat Message']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ChatMessage* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $chatMessage = Chat::chatMessage()->find($id, true);

            if (! $chatMessage) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_message_model'), $id);
            }

            if (! Chat::chatMessage()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.chat.chat_message_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Chat Message']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChatMessage* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexChatMessageRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatMessagePaginate = Chat::chatMessage()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Chat Message']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChatMessage* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ChatMessageCollection|JsonResponse
     */
    public function import(ImportChatMessageRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatMessagePaginate = Chat::chatMessage()->list($inputs);

            return new ChatMessageCollection($chatMessagePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
