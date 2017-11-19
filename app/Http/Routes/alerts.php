<?php

/*Alerts*/
Route::group(array('prefix' => 'alerts', 'as' => 'alerts'   ), function() {

    Route::get('/',
        [
            'uses' => 'Alert\AlertController@index'
        ]
    );

    Route::get('update',
        [
            'uses' => 'Alert\AlertController@update',
        ]
    );

    Route::group(array('prefix' => 'settings'), function() {

        Route::get('/',
            [
                'uses' => 'Alert\AlertSettingsController@index'
            ]
        );

        Route::match(array('GET', 'POST'), 'edit',
            [
                'uses' => 'Alert\AlertSettingsController@edit'
            ]
        );

    });

});