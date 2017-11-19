<?php

/*Tasks*/

Route::group(array('prefix' => 'tasks', 'as' => 'tasks'), function()
{

    Route::match(array('GET', 'POST'), '/',
        [
            'uses' => 'Task\TaskController@index',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('view',
        [
            'uses' => 'Task\TaskController@view',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::match(array('GET', 'POST'), 'edit',
        [
            'uses' => 'Task\TaskController@edit',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('delete',
        [
            'uses' => 'Task\TaskController@delete',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('get-schedule-tasks-ajax',
        [
            'uses' => 'Task\TaskController@getScheduleTasksAjax',
            'middleware' => ['auth', 'acl']
        ]
    );

    Route::get('schedule-request-ajax',
        [
            'uses' => 'Task\TaskController@scheduleRequestAjax'
        ]
    );

    Route::get('book-time',
        [
            'uses' => 'Task\TaskController@bookTime',
            'middleware' => ['auth', 'acl']
        ]
    );


    Route::get('links',
        [
            'uses' => 'Task\TaskController@links',
            'middleware' => ['auth', 'acl']
        ]
    );


    Route::get('events',
        [
            'uses' => 'Task\TaskController@events',
            'middleware' => ['auth', 'acl']
        ]
    );

});