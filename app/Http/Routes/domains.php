<?php

/*Domains*/
Route::group(array('prefix' => 'domains', 'as' => 'domains'), function()
{
    Route::get('/',
        [
            'uses' => 'Domain\DomainController@index',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('view',
        [
            'uses' => 'Domain\DomainController@view',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('alt-view',
        [
            'uses' => 'Domain\DomainController@altView',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::match(array('GET', 'POST'), 'edit',
        [
            'uses' => 'Domain\DomainController@edit',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::match(array('GET', 'POST'), 'create',
        [
            'uses' => 'Domain\DomainController@create',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('delete',
        [
            'uses' => 'Domain\DomainController@delete',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::match(array('GET', 'POST'), 'create-renewal-entry',
        [
            'uses' => 'Domain\DomainController@createRenewalEntry',
            'middleware' => ['auth', 'acl']
        ]
    );


});

/*Hostings*/
Route::group(array('prefix' => 'hostings', 'as' => 'hostings'), function()
{
    Route::get('/',
        [
            'uses' => 'Hosting\HostingController@index',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('view',
        [
            'uses' => 'Hosting\HostingController@view',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::match(array('GET', 'POST'), 'edit',
        [
            'uses' => 'Hosting\HostingController@edit',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::match(array('GET', 'POST'), 'create',
        [
            'uses' => 'Hosting\HostingController@create',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('delete',
        [
            'uses' => 'Hosting\HostingController@delete',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::match(array('GET', 'POST'), 'create-renewal-entry',
        [
            'uses' => 'Hosting\HostingController@createRenewalEntry',
            'middleware' => ['auth', 'acl']
        ]
    );


});