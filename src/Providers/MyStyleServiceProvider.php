<?php

namespace Botble\MyStyle\Providers;

use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\MyStyle\Facades\MyStyleHelperFacade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class MyStyleServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        Helper::autoload(__DIR__ . '/../../helpers');
        AliasLoader::getInstance()->alias('MyStyleHelper', MyStyleHelperFacade::class);
    }

    public function boot()
    {
        $this->setNamespace('plugins/my-style')
            ->loadAndPublishConfigurations(['permissions', 'config'])
            ->loadAndPublishViews();

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
