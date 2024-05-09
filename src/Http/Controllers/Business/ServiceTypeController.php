<?php

namespace Fintech\RestApi\Http\Controllers\Business;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Business\ImportServiceTypeRequest;
use Fintech\RestApi\Http\Requests\Business\IndexServiceTypeRequest;
use Fintech\RestApi\Http\Requests\Business\ServiceTypeListRequest;
use Fintech\RestApi\Http\Requests\Business\StoreServiceTypeRequest;
use Fintech\RestApi\Http\Requests\Business\UpdateServiceTypeRequest;
use Fintech\RestApi\Http\Resources\Business\ServiceTypeCollection;
use Fintech\RestApi\Http\Resources\Business\ServiceTypeListCollection;
use Fintech\RestApi\Http\Resources\Business\ServiceTypeResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ServiceTypeController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ServiceType
 *
 * @lrd:end
 */
class ServiceTypeController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ServiceType* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexServiceTypeRequest $request): ServiceTypeCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceTypePaginate = Business::serviceType()->list($inputs);

            return new ServiceTypeCollection($serviceTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ServiceType* resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreServiceTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceType = Business::serviceType()->create($inputs);

            if (!$serviceType) {
                throw (new StoreOperationException)->setModel(config('fintech.business.service_type_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Service Type']),
                'id' => $serviceType->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ServiceType* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ServiceTypeResource|JsonResponse
    {
        try {

            $serviceType = Business::serviceType()->find($id);

            if (!$serviceType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            return new ServiceTypeResource($serviceType);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ServiceType* resource using id.
     *
     * @lrd:end
     */
    public function update(UpdateServiceTypeRequest $request, string|int $id): JsonResponse
    {
        try {

            $serviceType = Business::serviceType()->find($id);

            if (!$serviceType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            $inputs = $request->validated();

            if (!Business::serviceType()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Service Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ServiceType* resource using id.
     *
     * @lrd:end
     */
    public function destroy(string|int $id): JsonResponse
    {
        try {

            $serviceType = Business::serviceType()->find($id);

            if (!$serviceType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            if (!Business::serviceType()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.business.service_type_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Service Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ServiceType* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     */
    public function restore(string|int $id): JsonResponse
    {
        try {

            $serviceType = Business::serviceType()->find($id, true);

            if (!$serviceType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.business.service_type_model'), $id);
            }

            if (!Business::serviceType()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.business.service_type_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Service Type']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *ServiceType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexServiceTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            //$serviceTypePaginate = Business::serviceType()->export($inputs);
            Business::serviceType()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Service Type']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create an exportable list of the *ServiceType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportServiceTypeRequest $request): ServiceTypeCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $serviceTypePaginate = Business::serviceType()->list($inputs);

            return new ServiceTypeCollection($serviceTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    public function serviceTypeList(ServiceTypeListRequest $request): ServiceTypeListCollection|JsonResponse
    {
        try {
            $input = $request->all();
            //TODO Check after login
            //$input['user_id'] = $request->user_id ?? auth()->user->getKey();
            //$input['role_id'] = $request->role_id ?? auth()->user->roles[0]->getKey();

            if (isset($request->service_type_parent_id)) {
                $input['service_type_parent_id'] = $request['service_type_parent_id'];
            } else {
                $input['service_type_parent_id_is_null'] = true;
            }
            $input['service_type_enabled'] = true;
            $input['sort'] = 'service_types.id';
            $input['dir'] = 'asc';
            $input['paginate'] = false;
            $serviceTypes = Business::serviceType()->list($input);

            $serviceTypeCollection = collect();

            foreach ($serviceTypes as $serviceType) {

                if ($serviceType->service_type_is_parent == 'no') {
                    $inputNo = $input;
                    $inputNo['service_join_active'] = true;
                    $inputNo['service_type_id'] = $serviceType->id;
                    $inputNo['service_enabled'] = true;
                    $inputNo['service_vendor_enabled'] = true;
                    $inputNo['service_stat_enabled'] = true;

                    $fullServiceTypes = Business::serviceType()->list($inputNo);
                    if ($fullServiceTypes->isNotEmpty()) {
                        foreach ($fullServiceTypes as $fullServiceType) {
                            if (isset($fullServiceType['service_stat_data'])) {
                                $fullServiceType['service_stat_data'] = json_decode($fullServiceType['service_stat_data'], true);
                                //                                $fullServiceType['logo_svg'] = json_decode($fullServiceType['service_stat_data'], true);
                            }
                            if (isset($fullServiceType['service_data'])) {
                                $fullServiceType['service_data'] = json_decode($fullServiceType['service_data'], true);
                            }
                            $fullServiceType->logo_svg = null;
                            $fullServiceType->logo_png = null;

                            $fullServiceType->logo_svg = Business::service()->find($fullServiceType->service_id)?->getFirstMediaUrl('logo_svg');
                            $fullServiceType->logo_png = Business::service()->find($fullServiceType->service_id)?->getFirstMediaUrl('logo_png');

                            if (isset($fullServiceType->media)) {
                                unset($fullServiceType->media);
                            }

                            $serviceTypeCollection->push($fullServiceType);
                        }
                    }
                } elseif ($serviceType['service_type_is_parent'] == 'yes') {
                    $inputYes = $input;
                    $collectID = [];
                    $findAllChildServiceType = Business::serviceType()->find($serviceType->getKey());
                    $arrayFindData[$serviceType->getKey()] = $findAllChildServiceType->allChildList ?? [];
                    foreach ($arrayFindData[$serviceType->getKey()] as $allChildAccounts) {
                        $collectID[$serviceType->getKey()][] = $allChildAccounts['id'];
                    }

                    $inputYes['service_type_id_array'] = $collectID[$serviceType->getKey()] ?? [];
                    //TODO may be need to work future
                    $inputYes['service_type_parent_id'] = $serviceType->getKey();
                    $inputYes['service_type_parent_id_is_null'] = false;
                    $inputYes['service_type_id'] = false;
                    $findServiceType = Business::serviceType()->list($inputYes)->count();
                    if ($findServiceType > 0) {
                        $serviceType->logo_svg = $serviceType->getFirstMediaUrl('logo_svg');
                        $serviceType->logo_png = $serviceType->getFirstMediaUrl('logo_png');
                        if (isset($serviceType->media)) {
                            unset($serviceType->media);
                        }
                        $serviceTypeCollection->push($serviceType);
                    }
                } else {
                    if (isset($serviceType->media)) {
                        unset($serviceType->media);
                    }
                    $serviceTypeCollection->push($serviceType);
                }
            }

            //$data['serviceType'] = $arrayData;
            //$data['serviceTypeTotal'] = count($arrayData);
            return new ServiceTypeListCollection($serviceTypeCollection);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }
}
