<?php

/*Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', 'HomeController@index');*/

Route::get('/',
    [
        'uses' => 'Index\IndexController@index'
    ]
);

/*Dashboard*/
Route::get('home', [
        'as' => 'home',
        'uses' => 'HomeController@index'
    ]
);

Route::match(array('GET', 'POST'), 'create',
    [
        'uses' => 'HomeController@create'
    ]
);