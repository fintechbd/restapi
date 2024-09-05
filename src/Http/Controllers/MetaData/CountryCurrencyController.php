<?php

namespace Fintech\RestApi\Http\Controllers\MetaData;

use Exception;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\MetaData\Facades\MetaData;
use Fintech\RestApi\Http\Requests\MetaData\CountryCurrencyRequest;
use Fintech\RestApi\Http\Resources\MetaData\CountryCurrencyCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CountryCurrencyController extends Controller
{
    /**
     * @lrd:start
     * return services to a specified role resource using id.
     *
     * @lrd:end
     */
    public function show(string|int $id): CountryCurrencyCollection|JsonResponse
    {
        try {

            $country = MetaData::country()->find($id);

            if (!$country) {
                throw (new ModelNotFoundException)->setModel(config('fintech.metadata.country_model'), $id);
            }

            $availableCurrencies = MetaData::currency()->list(['enabled' => true, 'paginate' => false]);

            $enabledCurrencies = MetaData::currency()
                ->list(['enabled' => true, 'paginate' => false, 'multi_currency_country_id' => $country->getKey()])
                ->pluck('currency_id')->toArray();

            return new CountryCurrencyCollection(['availableCurrencies' => $availableCurrencies, 'enabledCurrencies' => $enabledCurrencies]);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Assign currencies to a specified role resource using id.
     *
     * @lrd:end
     */
    public function update(CountryCurrencyRequest $request, string|int $id): JsonResponse
    {
        try {

            $country = MetaData::country()->find($id);

            if (!$country) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.country_model'), $id);
            }

            $inputs = $request->validated();

            if (!MetaData::country()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.auth.country_model'), $id);
            }

            return response()->updated(__('metadata::messages.country.currency_assigned', ['country' => strtolower($country->name ?? 'N/A')]));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
