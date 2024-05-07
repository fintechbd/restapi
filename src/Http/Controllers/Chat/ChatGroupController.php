<?php

namespace Fintech\Chat\Http\Controllers;

use Exception;
use Fintech\Chat\Facades\Chat;
use Fintech\Chat\Http\Requests\ImportChatGroupRequest;
use Fintech\Chat\Http\Requests\IndexChatGroupRequest;
use Fintech\Chat\Http\Requests\StoreChatGroupRequest;
use Fintech\Chat\Http\Requests\UpdateChatGroupRequest;
use Fintech\Chat\Http\Resources\ChatGroupCollection;
use Fintech\Chat\Http\Resources\ChatGroupResource;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ChatGroupController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ChatGroup
 *
 * @lrd:end
 */
class ChatGroupController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *ChatGroup* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexChatGroupRequest $request): ChatGroupCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatGroupPaginate = Chat::chatGroup()->list($inputs);

            return new ChatGroupCollection($chatGroupPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ChatGroup* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreChatGroupRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatGroup = Chat::chatGroup()->create($inputs);

            if (! $chatGroup) {
                throw (new StoreOperationException)->setModel(config('fintech.chat.chat_group_model'));
            }

            return $this->created([
                'message' => __('core::messages.resource.created', ['model' => 'Chat Group']),
                'id' => $chatGroup->id,
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ChatGroup* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ChatGroupResource|JsonResponse
    {
        try {

            $chatGroup = Chat::chatGroup()->find($id);

            if (! $chatGroup) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_group_model'), $id);
            }

            return new ChatGroupResource($chatGroup);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ChatGroup* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateChatGroupRequest $request, string|int $id): JsonResponse
    {
        try {

            $chatGroup = Chat::chatGroup()->find($id);

            if (! $chatGroup) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_group_model'), $id);
            }

            $inputs = $request->validated();

            if (! Chat::chatGroup()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.chat.chat_group_model'), $id);
            }

            return $this->updated(__('core::messages.resource.updated', ['model' => 'Chat Group']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ChatGroup* resource using id.
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

            $chatGroup = Chat::chatGroup()->find($id);

            if (! $chatGroup) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_group_model'), $id);
            }

            if (! Chat::chatGroup()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.chat.chat_group_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Chat Group']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ChatGroup* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $chatGroup = Chat::chatGroup()->find($id, true);

            if (! $chatGroup) {
                throw (new ModelNotFoundException)->setModel(config('fintech.chat.chat_group_model'), $id);
            }

            if (! Chat::chatGroup()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.chat.chat_group_model'), $id);
            }

            return $this->restored(__('core::messages.resource.restored', ['model' => 'Chat Group']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChatGroup* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexChatGroupRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatGroupPaginate = Chat::chatGroup()->export($inputs);

            return $this->exported(__('core::messages.resource.exported', ['model' => 'Chat Group']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChatGroup* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ChatGroupCollection|JsonResponse
     */
    public function import(ImportChatGroupRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chatGroupPaginate = Chat::chatGroup()->list($inputs);

            return new ChatGroupCollection($chatGroupPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
