<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Fintech\RestApi\Http\Requests\Auth\VerifyIdDocTypeRequest;
use Fintech\RestApi\Http\Resources\Auth\VerifyIdDocTypeResource;
use Illuminate\Http\JsonResponse;

class VerifyIdDocumentController extends Controller
{
    /**
     * @lrd:start
     * Verify *IdDocType* is already exists or not in storage.
     * @lrd:end
     *
     * @param VerifyIdDocTypeRequest $request
     * @return VerifyIdDocTypeResource|JsonResponse
     */
    public function __invoke(VerifyIdDocTypeRequest $request): VerifyIdDocTypeResource|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $idDocType = \Fintech\Auth\Facades\Auth::profile()->list($inputs)->first();

            return new VerifyIdDocTypeResource($idDocType);

        } catch (\Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
