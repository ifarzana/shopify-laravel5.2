<?php

Route::group(array('prefix' => 'invoices', 'as' => 'invoices', 'middleware' => ['auth', 'acl']), function()
{

    Route::get( '/',
        [
            'uses' => 'Invoice\InvoiceController@index'
        ]
    );

    Route::get( 'view',
        [
            'uses' => 'Invoice\InvoiceController@view'
        ]
    );

    Route::get('download',
        [
            'uses' => 'Invoice\InvoiceController@download'
        ]
    );

    Route::match(array('GET', 'POST'), 'email',
        [
            'uses' => 'Invoice\InvoiceController@email'
        ]
    );

    Route::get('cancel',
        [
            'uses' => 'Invoice\InvoiceController@cancel'
        ]
    );

    Route::get('sent',
        [
            'uses' => 'Invoice\InvoiceController@sent'
        ]
    );

    /*Payments*/
    Route::group(array('prefix' => 'payments'), function()
    {

        Route::get('/',
            [
                'uses' => 'Invoice\InvoicePaymentController@index'
            ]
        );

        Route::match(array('GET', 'POST'), 'create',
            [
                'uses' => 'Invoice\InvoicePaymentController@create'
            ]
        );

        Route::match(array('GET', 'POST'), 'create-sagepay',
            [
                'uses' => 'Invoice\InvoicePaymentController@createSagepay'
            ]
        );

        Route::match(array('GET', 'POST'), 'edit',
            [
                'uses' => 'Invoice\InvoicePaymentController@edit'
            ]
        );

        Route::get('delete',
            [
                'uses' => 'Invoice\InvoicePaymentController@delete',
                'middleware' => ['super-administrator']
            ]
        );

        Route::post('payment-method-select',
            [
                'uses' => 'Invoice\InvoicePaymentController@paymentMethodSelect'
            ]
        );

        Route::get('refunded-online',
            [
                'uses' => 'Invoice\InvoicePaymentController@refundedOnline'
            ]
        );

        Route::match(array('GET', 'POST'), 'refund',
            [
                'uses' => 'Invoice\InvoicePaymentController@refund'
            ]
        );

    });


});