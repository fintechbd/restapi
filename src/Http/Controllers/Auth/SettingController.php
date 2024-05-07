<?php

namespace Fintech\RestApi\Http\Controllers\Auth;

use Exception;
use Fintech\RestApi\Http\Requests\Auth\StoreSettingRequest;
use Fintech\RestApi\Http\Resources\Auth\SettingResource;
use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Utility;
use Fintech\Core\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class SettingController
 * @package Fintech\RestApi\Http\Controllers\Auth
 *
 * @lrd:start
 * This class handle system setting related to individual user
 * @lrd:end
 *
 */
class SettingController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the configurations in key and value format.
     * *`configuration`* value depends on  number of package configured to system
     * @lrd:end
     *
     * @return SettingResource|JsonResponse
     */
    public function index(): SettingResource|JsonResponse
    {
        try {

            $configurations = (Auth::check())
                ? Core::setting()->list(['user_id' => auth()->id()])
                : collect([]);

            $settings = [];

            $configurations->each(function ($setting) use (&$settings) {
                $settings[$setting->package][$setting->key] = Utility::typeCast($setting->value, $setting->type);
            });

            return new SettingResource($settings);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @LRDparam package string|required|in:dashboard,other
     * @lrd:start
     * Update a specified user settings using configuration
     * @lrd:end
     *
     * @param StoreSettingRequest $request
     * @return JsonResponse
     */
    public function store(StoreSettingRequest $request): JsonResponse
    {
        try {

            $configuration = $request->input('package', 'dashboard');

            $inputs = $request->except('package');

            foreach ($inputs as $key => $value) {
                Core::setting()->setValue($configuration, $key, $value, null, auth()->id());
            }

            return $this->updated(__('core::messages.setting.saved', ['package' => config("fintech.core.packages.{$configuration}", 'System')]));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified setting resource using id.
     * @lrd:end
     *
     * @param string $configuration
     * @return JsonResponse
     */
    public function destroy(string $configuration)
    {
        try {

            $settings = Core::setting()->list(['package' => $configuration]);

            foreach ($settings as $setting) {
                Core::setting()->destroy($setting->getKey());
            }

            return $this->deleted(__('core::messages.setting.deleted', ['model' => 'Setting']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
