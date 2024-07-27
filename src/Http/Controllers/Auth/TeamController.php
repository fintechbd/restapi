<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\RestApi\Http\Requests\Auth\ImportTeamRequest;
use Fintech\RestApi\Http\Requests\Auth\IndexTeamRequest;
use Fintech\RestApi\Http\Requests\Auth\StoreTeamRequest;
use Fintech\RestApi\Http\Requests\Auth\UpdateTeamRequest;
use Fintech\RestApi\Http\Requests\Core\DropDownRequest;
use Fintech\RestApi\Http\Resources\Auth\TeamCollection;
use Fintech\RestApi\Http\Resources\Auth\TeamResource;
use Fintech\RestApi\Http\Resources\Core\DropDownCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class TeamController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to team
 *
 * @lrd:end
 */
class TeamController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the team resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexTeamRequest $request): TeamCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $teamPaginate = Auth::team()->list($inputs);

            return new TeamCollection($teamPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @LRDparam trashed boolean|nullable
     *
     * @lrd:start
     * Create a new team resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreTeamRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $team = Auth::team()->create($inputs);

            if (! $team) {
                throw (new StoreOperationException)->setModel(config('fintech.auth.team_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Team']),
                'id' => $team->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified team resource found by id.
     *
     * @lrd:end
     */
    public function show(string|int $id): TeamResource|JsonResponse
    {
        try {

            $team = Auth::team()->find($id);

            if (! $team) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.team_model'), $id);
            }

            return new TeamResource($team);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified team resource using id.
     *
     * @lrd:end
     */
    public function update(UpdateTeamRequest $request, string|int $id): JsonResponse
    {
        try {

            $team = Auth::team()->find($id);

            if (! $team) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.team_model'), $id);
            }

            $inputs = $request->validated();

            if (! Auth::team()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.auth.team_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Team']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified team resource using id.
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function destroy(string|int $id)
    {
        try {

            $team = Auth::team()->find($id);

            if (! $team) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.team_model'), $id);
            }

            if (! Auth::team()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.auth.team_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Team']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified team resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $team = Auth::team()->find($id, true);

            if (! $team) {
                throw (new ModelNotFoundException)->setModel(config('fintech.auth.team_model'), $id);
            }

            if (! Auth::team()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.auth.team_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Team']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the team resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexTeamRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $teamPaginate = Auth::team()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Team']));

        } catch (Exception $exception) {

            return response()->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the team resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function import(ImportTeamRequest $request): TeamCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $teamPaginate = Auth::team()->list($inputs);

            return new TeamCollection($teamPaginate);

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

            $entries = Auth::team()->list($filters)->map(function ($entry) use ($label, $attribute) {
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
}
