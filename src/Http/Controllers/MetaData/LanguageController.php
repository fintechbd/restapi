<?php

namespace Fintech\RestApi\Http\Controllers\MetaData;

use Exception;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\MetaData\Facades\MetaData;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Requests\MetaData\IndexLanguageRequest;
use Fintech\RestApi\Http\Requests\MetaData\UpdateLanguageRequest;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Fintech\RestApi\Http\Resources\MetaData\LanguageCollection;
use Fintech\RestApi\Http\Resources\MetaData\LanguageResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class LanguageController
 * @package Fintech\MetaData\Http\Controllers
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Language
 * @lrd:end
 *
 */
class LanguageController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *Language* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexLanguageRequest $request
     * @return LanguageCollection|JsonResponse
     */
    public function index(IndexLanguageRequest $request): LanguageCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $languagePaginate = MetaData::language()->list($inputs);

            return new LanguageCollection($languagePaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *Language* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return LanguageResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): LanguageResource|JsonResponse
    {
        try {

            $language = MetaData::language()->find($id);

            if (!$language) {
                throw (new ModelNotFoundException())->setModel('Language', $id);
            }

            return new LanguageResource($language);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified country as localization/language enabled or not.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function toggle(string|int $id): JsonResponse
    {
        try {

            $language = MetaData::language()->find($id);

            if (!$language) {
                throw (new ModelNotFoundException())->setModel('Language', $id);
            }

            $countryData = $language->country_data;

            if ($language->language['code'] == null || $language->language['name'] == null) {
                throw new Exception(__('metadata::messages.country.language_field_missing'));
            }

            $inputs['enabled'] = !($countryData['language_enabled'] ?? false);

            if (!MetaData::language()->update($id, $inputs)) {

                throw (new UpdateOperationException())->setModel('Language', $id);
            }

            return $this->updated(__('metadata::messages.country.status_changed', ['field' => 'Language']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *Language* resource using id.
     * @lrd:end
     *
     * @param UpdateLanguageRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function update(UpdateLanguageRequest $request, string|int $id): JsonResponse
    {
        try {

            $language = MetaData::language()->find($id);

            if (!$language) {
                throw (new ModelNotFoundException())->setModel('Language', $id);
            }

            $inputs = $request->validated();

            if (!MetaData::language()->update($id, $inputs)) {

                throw (new UpdateOperationException())->setModel('Language', $id);
            }

            return $this->updated(__('core::messages.resource.updated', ['model' => 'Language']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @param DropDownRequest $request
     * @return DropDownCollection|JsonResponse
     */
    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $filters['enabled'] = true;

            $label = 'name';

            $attribute = 'code';

            if (!empty($filters['label'])) {
                $label = $filters['label'];
                unset($filters['label']);
            }

            if (!empty($filters['attribute'])) {
                $attribute = $filters['attribute'];
                unset($filters['attribute']);
            }

            $entries = MetaData::language()->list($filters)->map(function ($entry) use ($label, $attribute) {
                $json_data = $entry->language;

                return [
                    'attribute' => $json_data[$attribute] ?? null,
                    'label' => $json_data[$label] ?? null,
                    'logo_svg' => $entry->getFirstMediaUrl('logo_svg'),
                    'logo_png' => $entry->getFirstMediaUrl('logo_png'),
                    'country_id' => $entry->id ?? null,
                    'phone_code' => $entry->phone_code ?? null,
                ];
            })->toArray();

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return $this->failed($exception->getMessage());
        }
    }
}
