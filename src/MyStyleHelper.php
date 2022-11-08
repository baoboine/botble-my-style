<?php

namespace Botble\MyStyle;

use Illuminate\Support\Arr;

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
    public function registerModule($model): self
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
     * @param string $model
     * @return bool
     */
    public function isSupportedModel(string $model): bool
    {
        return in_array($model, $this->supportedModels());
    }

    /**
     * @param string $model
     * @return $this
     */
    public function unregisterModule(string $model): self
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
    public function setConfig(array $config): self
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
    public function config(?string $key = null, $default = null)
    {
        $options = $this->options;

        if ($key) {
            $options = Arr::get($options, $key, $default);
        }

        return $options;
    }
}
