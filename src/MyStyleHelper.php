<?php

namespace Botble\MyStyle;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MyStyleHelper
{

    /**
     * array
     */
    private $options;

    /**
     * MyStyleHelper constructor.
     */
    public function __construct()
    {
        $this->options = config('plugins.my-style.config', []);
    }

    /**
     * @param string | array $model
     * @return $this
     */
    public function registerModule($model)
    {
        if (!is_array($model)) {
            $model = [$model];
        }

        $supported = array_merge($this->supportedModels(), $model);

        config(['plugins.my-style.config.supported' => $supported]);

        $this->options['supported'] = $supported;

        return $this;
    }

    /**
     * @return array
     */
    public function supportedModels(): array
    {
        return config('plugins.my-style.config.supported', []);
    }

    /**
     * @return bool
     */
    public function isSupportedModel(string $model): bool
    {
        return in_array($model, array_keys($this->supportedModels()));
    }

    /**
     * @param string $model
     * @return $this
     */
    public function unregisterModule(string $model)
    {
        $supported = $this->supportedModels();

        if (($key = array_search($model, $supported)) !== false) {
            unset($supported[$key]);
        }

        config(['plugins.my-style.config.supported' => $supported]);

        $this->options['supported'] = $supported;

        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $options = array_merge($this->options, $config);

        config(['plugins.my-style.config' => $options]);

        $this->options = $options;

        return $this;
    }

    /**
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function config($key = null, $default = null)
    {
        $options = $this->options;

        if ($key) {
            $options = Arr::get($options, $key, $default);
        }

        return $options;
    }
}
