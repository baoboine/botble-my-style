<?php

namespace Botble\MyStyle\Providers;

use Illuminate\Support\ServiceProvider;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;

class MyStyleServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/my-style')
            ->loadAndPublishConfigurations(['permissions', 'config'])
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        $this->app->booted(function() {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
