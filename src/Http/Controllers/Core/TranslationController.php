<?php

namespace Fintech\RestApi\Http\Controllers\Core;

use Exception;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Facades\Core;
use Fintech\RestApi\Http\Resources\Core\TranslationResource;
use Fintech\RestApi\Http\Resources\Core\TranslationCollection;
use Fintech\RestApi\Http\Requests\Core\ImportTranslationRequest;
use Fintech\RestApi\Http\Requests\Core\StoreTranslationRequest;
use Fintech\RestApi\Http\Requests\Core\UpdateTranslationRequest;
use Fintech\RestApi\Http\Requests\Core\IndexTranslationRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class TranslationController
 * @package Fintech\RestApi\Http\Controllers\Core
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Translation
 * @lrd:end
 *
 */
class TranslationController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Translation* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexTranslationRequest $request
     * @return TranslationCollection|JsonResponse
     */
    public function index(IndexTranslationRequest $request): TranslationCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $translationPaginate = Core::translation()->list($inputs);

            return new TranslationCollection($translationPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *Translation* resource in storage.
     * @lrd:end
     *
     * @param StoreTranslationRequest $request
     * @return JsonResponse
     * @throws StoreOperationException
     */
    public function store(StoreTranslationRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $translation = Core::translation()->create($inputs);

            if (!$translation) {
                throw (new StoreOperationException)->setModel(config('fintech.core.translation_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Translation']),
                'id' => $translation->id
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Translation* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return TranslationResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): TranslationResource|JsonResponse
    {
        try {

            $translation = Core::translation()->find($id);

            if (!$translation) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.translation_model'), $id);
            }

            return new TranslationResource($translation);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *Translation* resource using id.
     * @lrd:end
     *
     * @param UpdateTranslationRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateTranslationRequest $request, string|int $id): JsonResponse
    {
        try {

            $translation = Core::translation()->find($id);

            if (!$translation) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.translation_model'), $id);
            }

            $inputs = $request->validated();

            if (!Core::translation()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.core.translation_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Translation']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Translation* resource using id.
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

            $translation = Core::translation()->find($id);

            if (!$translation) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.translation_model'), $id);
            }

            if (!Core::translation()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.core.translation_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Translation']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Translation* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $translation = Core::translation()->find($id, true);

            if (!$translation) {
                throw (new ModelNotFoundException)->setModel(config('fintech.core.translation_model'), $id);
            }

            if (!Core::translation()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.core.translation_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Translation']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Translation* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param IndexTranslationRequest $request
     * @return JsonResponse
     */
    public function export(IndexTranslationRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $translationPaginate = Core::translation()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Translation']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Translation* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param ImportTranslationRequest $request
     * @return TranslationCollection|JsonResponse
     */
    public function import(ImportTranslationRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $translationPaginate = Core::translation()->list($inputs);

            return new TranslationCollection($translationPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return an exportable list of the *Translation* resource as JSON document.
     *
     * @lrd:end
     *
     * @param string $locale
     * @return BinaryFileResponse|JsonResponse
     */
    public function download(string $locale): BinaryFileResponse|JsonResponse
    {
        try {
            $translations = Core::translation()->list(['locale' => $locale])->pluck("locale.{$locale}", 'key')->toArray();

            return response()->download($translations, "{$locale}.json}", ['Content-Type' => 'application/json']);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

}
