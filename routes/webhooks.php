<?php

Route::group(['prefix' => 'webhook', 'middleware' => ['webhook'], 'namespace' => 'Webhooks'], function () {
    Route::post('/uninstall', 'AppController@uninstall')->name('webhook-uninstall-app');

    Route::group(['prefix' => 'products'], function () {
        Route::post('/create', 'ProductsController@handleCreate')->name('webhook-products-create');
        Route::post('/update', 'ProductsController@handleUpdate')->name('webhook-products-update');
        Route::post('/delete', 'ProductsController@handleDelete')->name('webhook-products-delete');
    });
});
