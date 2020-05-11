<?php

Route::group([
    'namespace' => 'Seat\Akturis\WinFleet\Http\Controllers',
    'prefix' => 'winfleet'
], function () {
    Route::group([
        'middleware' => ['web', 'auth', 'locale'],
    ], function () {
        Route::get('/', [
            'as'   => 'winfleet.view',
            'uses' => 'WinFleetController@index',
            'middleware' => 'bouncer:winfleet.view'
        ]);
        Route::post('/', [
            'as'   => 'winfleet.update',
            'uses' => 'WinFleetController@update',
            'middleware' => 'bouncer:winfleet.update'
        ]);

        Route::get('/settings', [
            'as'   => 'winfleet.settings',
            'uses' => 'SettingsController@index',
            'middleware' => 'bouncer:winfleet.settings'
        ]);        
        Route::post('/settings', [
            'as'   => 'winfleet.settings',
            'uses' => 'SettingsController@update',
            'middleware' => 'bouncer:winfleet.settings'
        ]);        
        
        Route::get('/operation', [
            'as'   => 'winfleet.operation',
            'uses' => 'AjaxController@getOperation',
            'middleware' => 'bouncer:winfleet.view'
        ]);

        Route::get('/awards', [
            'as'   => 'winfleet.awards',
            'uses' => 'AjaxController@getAwards',
            'middleware' => 'bouncer:winfleet.view'
        ]);

        Route::get('/awards2', [
            'as'   => 'winfleet.awards2',
            'uses' => 'AjaxController@getAwards2',
            'middleware' => 'bouncer:winfleet.view'
        ]);
        
        Route::post('/save', [
            'as' => 'winfleet.save',
            'uses' => 'AjaxController@updateWinners',
            'middleware' => 'bouncer:winfleet.update'
        ]);

        Route::post('/delete', [
            'as' => 'winfleet.delete',
            'uses' => 'AjaxController@deleteWinners',
            'middleware' => 'bouncer:winfleet.update'
        ]);
        
        Route::post('/status', [
            'as' => 'winfleet.status',
            'uses' => 'AjaxController@updateStatus',
            'middleware' => 'bouncer:winfleet.status'
        ]);

    });
    
    
});
