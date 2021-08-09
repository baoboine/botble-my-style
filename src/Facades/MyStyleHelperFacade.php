<?php

namespace Botble\MyStyle\Facades;

use Botble\MyStyle\MyStyleHelper;
use Illuminate\Support\Facades\Facade;

class MyStyleHelperFacade extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MyStyleHelper::class;
    }
}
