<?php

Route::group(['namespace' => 'Botble\MyStyle\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'my-styles', 'as' => 'my-style.'], function () {

        });
    });

});
