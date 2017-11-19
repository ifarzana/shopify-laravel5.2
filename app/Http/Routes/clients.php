<?php

/*Clients*/
Route::group(array('prefix' => 'clients', 'as' => 'clients'), function()
{

    Route::get('/',
        [
            'uses' => 'Client\ClientController@index',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('view',
        [
            'uses' => 'Client\ClientController@view',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::match(array('GET', 'POST'), 'edit',
        [
            'uses' => 'Client\ClientController@edit',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::match(array('GET', 'POST'), 'create',
        [
            'uses' => 'Client\ClientController@create',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('delete',
        [
            'uses' => 'Client\ClientController@delete',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('domains',
        [
            'uses' => 'Client\ClientController@domains',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('hostings',
        [
            'uses' => 'Client\ClientController@hostings',
            'middleware' => ['auth', 'acl']
        ]
    );

    /*Ajax actions*/
    Route::get('search-ajax',
        [
            'uses' => 'Client\ClientController@searchAjax'
        ]
    );

    Route::get('get-ajax',
        [
            'uses' => 'Client\ClientController@getAjax'
        ]
    );


});