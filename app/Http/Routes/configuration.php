<?php

/*Configuration*/
Route::group(array('prefix' => 'configuration', 'as' => 'configuration', 'middleware' => ['auth']), function()
{

    /*Settings*/
    Route::group(array('prefix' => 'settings'), function() {

        Route::get('/',
            [
                'uses' => 'Configuration\Setting\SettingController@index',
            ]
        );

    });

    /*Task settings*/
    Route::group(array('prefix' => 'task-settings'), function() {

        Route::get('/',
            [
                'uses' => 'Configuration\Task\TaskSettingController@index'
            ]
        );

        Route::get('view',
            [
                'uses' => 'Configuration\Task\TaskSettingController@view'
            ]
        );

        Route::match(array('GET', 'POST'), 'edit',
            [
                'uses' => 'Configuration\Task\TaskSettingController@edit'
            ]
        );

    });

    /*Domain settings*/
    Route::group(array('prefix' => 'domain-settings'), function() {

        Route::get('/',
            [
                'uses' => 'Configuration\Domain\DomainSettingController@index'
            ]
        );

        Route::get('view',
            [
                'uses' => 'Configuration\Domain\DomainSettingController@view'
            ]
        );

        Route::match(array('GET', 'POST'), 'edit',
            [
                'uses' => 'Configuration\Domain\DomainSettingController@edit'
            ]
        );

    });

    /*Email accounts*/
    Route::group(array('prefix' => 'email-accounts'), function() {

        Route::get('/',
            [
                'uses' => 'Configuration\Email\EmailAccountController@index'
            ]
        );

        Route::get('view',
            [
                'uses' => 'Configuration\Email\EmailAccountController@view'
            ]
        );

        Route::get('test',
            [
                'uses' => 'Configuration\Email\EmailAccountController@test'
            ]
        );

        Route::match(array('GET', 'POST'), 'edit',
            [
                'uses' => 'Configuration\Email\EmailAccountController@edit'
            ]
        );

    });

    /*Users*/
    Route::group(array('prefix' => 'users'), function()
    {

        Route::get('/',
            [
                'uses' => 'Configuration\User\UserController@index',
            ]
        );

        Route::get('view',
            [
                'uses' => 'Configuration\User\UserController@view',
            ]
        );

        Route::match(array('GET', 'POST'), 'edit',
            [
                'uses' => 'Configuration\User\UserController@edit',
            ]
        );

        Route::match(array('GET', 'POST'), 'create',
            [
                'uses' => 'Configuration\User\UserController@create',
            ]
        );

        Route::get('delete',
            [
                'uses' => 'Configuration\User\UserController@delete',
            ]
        );

        Route::get('sessions',
            [
                'uses' => 'Configuration\User\UserController@sessions',
            ]
        );

        Route::get('domains',
            [
                'uses' => 'Configuration\User\UserController@domains',
            ]
        );

        /*User groups*/
        Route::group(array('prefix' => 'groups'), function()
        {

            Route::get('/',
                [
                    'uses' => 'Configuration\User\GroupController@index',
                ]
            );

            Route::match(array('GET', 'POST'), 'edit',
                [
                    'uses' => 'Configuration\User\GroupController@edit',
                ]
            );

            Route::match(array('GET', 'POST'), 'create',
                [
                    'uses' => 'Configuration\User\GroupController@create',
                ]
            );

            Route::get('delete',
                [
                    'uses' => 'Configuration\User\GroupController@delete',
                ]
            );

            Route::get('permissions',
                [
                    'uses' => 'Configuration\User\GroupController@permissions',
                ]
            );

            Route::get('change-resource',
                [
                    'uses' => 'Configuration\User\GroupController@changeResource',
                ]
            );

            Route::get('change-permission',
                [
                    'uses' => 'Configuration\User\GroupController@changePermission',
                ]
            );

        });

    });

    /*System*/
    Route::group(array('prefix' => 'system'), function() {

        Route::get('/',
            [
                'uses' => 'Configuration\System\SystemController@index'
            ]
        );

        Route::get('about',
            [
                'uses' => 'Configuration\System\SystemController@about'
            ]
        );

        /*Audit log*/
        Route::group(array('prefix' => 'audit-log'), function()
        {

            Route::get('/',
                [
                    'uses' => 'Configuration\System\AuditLogController@index'
                ]
            );

            Route::get('view',
                [
                    'uses' => 'Configuration\System\AuditLogController@view'
                ]
            );

        });

    });

});