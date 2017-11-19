<?php

/*API*/
Route::group(array('prefix' => 'api', 'middleware' => ['auth.api']), function()
{

    Route::group(array('prefix' => 'domains'), function()
    {
        Route::match(array('GET', 'POST'), 'domain-reminder',
            [
                'uses' => 'Api\ApiController@domainReminder'
            ]
        );

        Route::match(array('GET', 'POST'), 'send-domain-email',
            [
                'uses' => 'Api\ApiController@sendDomainEmail'
            ]
        );

    });

});