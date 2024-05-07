<?php

namespace Fintech\RestApi\Http\Controllers\Core;

use Exception;
use Fintech\Core\Facades\Core;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\RestApi\Http\Resources\Core\ConfigurationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

/**
 * Class ConfigurationController
 * @package Fintech\Core\Http\Controllers
 *
 * @lrd:start
 * This class handle system setting related to all individual packages
 * @lrd:end
 *
 */
class ConfigurationController extends Controller
{
    use ApiResponseTrait;

    private array $hiddenFields = ['repositories', 'root_prefix', 'middleware', '^(.*)_model', '^(.*)_rules', 'packages'];

    /**
     * @lrd:start
     * Return a listing of the configurations in key and value format.
     * *`configuration`* value depends on  number of package configured to system
     * @lrd:end
     *
     * @param string $configuration
     * @return ConfigurationResource|JsonResponse
     */
    public function show(string $configuration): ConfigurationResource|JsonResponse
    {
        try {
            $configurations = Config::get("fintech.{$configuration}", []);

            foreach ($configurations as $key => $value) {
                foreach ($this->hiddenFields as $field) {
                    if (preg_match("/{$field}/i", $key) === 1) {
                        unset($configurations[$key]);
                    }
                }
            }

            return new ConfigurationResource($configurations);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified package configurations using configuration
     * @lrd:end
     *
     * @param Request $request
     * @param string $configuration
     * @return JsonResponse
     */
    public function update(string $configuration, Request $request): JsonResponse
    {
        try {

            $inputs = $request->all();

            foreach ($inputs as $key => $value) {
                Core::setting()->setValue($configuration, $key, $value, gettype($value));
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

            return $this->deleted(__('core::messages.setting.deleted', ['model' => ucwords($configuration)]));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
