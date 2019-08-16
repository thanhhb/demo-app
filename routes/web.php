<?php

Route::get('/', 'GuestController@index')->name('index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('login/shopify', 'Auth\LoginShopifyController@redirectToProvider')->name('login.shopify');
Route::get('login/shopify/callback', 'Auth\LoginShopifyController@handleProviderCallback');

Route::get('/stores/{storeId}/subscribe', 'SubscriptionController@index')->name('shopify.buy')->middleware('auth');
Route::get('/stores/{storeId}/subscribe/callback', 'SubscriptionController@index')->name('shopify.buy.callback')->middleware('auth');

Route::get('/stores/{storeId}', 'StoreController@index')->name('store.index')->middleware(['subscribed', 'auth']);


