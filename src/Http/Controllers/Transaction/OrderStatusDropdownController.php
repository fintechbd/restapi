<?php

namespace Fintech\RestApi\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Exception;
use Fintech\Core\Enums\Transaction\OrderStatus;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Illuminate\Http\JsonResponse;

class OrderStatusDropdownController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $filters = $request->all();

            $entries = collect();

            foreach (OrderStatus::toArray() as $key => $status) {
                $entries->push(['label' => $status, 'attribute' => $key]);
            }

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception);
        }
    }
}
