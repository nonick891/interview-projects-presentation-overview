<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function() {
	
	Route::group(['middleware' => ['auth:api']], function() {
		
		Route::group(['prefix' => 'core'], function() {
			
			Route::get('/', 'Api\CoreDataController@index');
			
		});
		
	});
	
	Route::group(['middleware' => ['auth:api', 'subscriber']], function() {
		
		Route::group(['prefix' => 'user'], function() {
			
			Route::get('/', 'Api\UserController@index');
			
			Route::patch('/update-profile', 'Api\UserController@updateProfile');
			
			Route::patch('/update-calendar-settings', 'Api\UserController@updateUserCalendarSettings');
			
		});
		
		Route::group(['prefix' => 'site'], function() {
	
			Route::post('/all', 'Api\SiteController@index');
	
			Route::post('/', 'Api\SiteController@get');
	
			Route::put('/add', 'Api\SiteController@add');
	
			Route::patch('/update', 'Api\SiteController@update');
	
			Route::delete('/delete', 'Api\SiteController@delete');
	
		});
	
		Route::group(['prefix' => 'game'], function() {
	
			Route::post('/all', 'Api\GameController@index');
			
			Route::put('/add', 'Api\GameController@add');
			
			Route::put('/create-with-site', 'Api\GameController@createWithSite');
			
			Route::patch('/update', 'Api\GameController@update');
			
			Route::delete('/delete', 'Api\GameController@delete');

			Route::delete('/{user_id}/delete-list', 'Api\GameController@deleteList');

			Route::post('/subscribers', 'Api\GameController@getSubscribers');
			
			Route::post('/subscribers-account', 'Api\GameController@getAccountSubscribers');
			
			Route::post('/subscribers/count', 'Api\GameController@getSubscribersCount');
			
			Route::post('/update-delete-customer-webhook', 'Api\GameController@updateDeleteWebhookCustomer');
	
			Route::post('/sync-old-emails-to-shopify', 'Api\GameController@syncOldGameHits');
		});
		
		Route::group(['prefix' => 'coupon'], function() {
			
			Route::post('/all', 'Api\CouponController@index');
			
			Route::patch('/update', 'Api\CouponController@update');
			
			Route::post('/update/bunch', 'Api\CouponController@updateBunch');
			
			Route::get('/shopify-collections', 'Api\ShopifyAppController@getCollections');
			
			Route::delete('/unique-coupon', 'Api\CouponController@deleteUnique');
			
		});
		
		Route::group(['prefix' => 'setting'], function() {
			
			Route::post('/all', 'Api\SettingController@index');
			
			Route::post('/all/list', 'Api\SettingController@get');
			
			Route::put('/add', 'Api\SettingController@add');
			
			Route::patch('/update', 'Api\SettingController@update');
			
			Route::post('/update/file', 'Api\SettingController@updateFile');
			
			Route::delete('/delete/file', 'Api\SettingController@deleteFile');
			
			Route::post('/update/wheel-file-logo', 'Api\SettingController@updateWheelFileLogo');
			
			Route::delete('/delete/wheel-file-logo', 'Api\SettingController@deleteWheelFileLogo');
			
			Route::post('/update/reveal-file-logo', 'Api\SettingController@updateRevealFileLogo');
			
			Route::delete('/delete/reveal-file-logo', 'Api\SettingController@deleteRevealFileLogo');
			
			Route::post('/update/spinthewheel-file-logo', 'Api\SettingController@updateSpinTheWheelFileLogo');
			
			Route::delete('/delete/spinthewheel-file-logo', 'Api\SettingController@deleteSpinTheWheelFileLogo');
			
			Route::post('/update/spinthewheel-file-mobile-logo', 'Api\SettingController@updateSpinTheWheelFileMobileLogo');
			
			Route::delete('/delete/spinthewheel-file-mobile-logo', 'Api\SettingController@deleteSpinTheWheelFileMobileLogo');
			
			Route::post('/update/spinthewheelinform-file-logo', 'Api\SettingController@updateSpinTheWheelInformFileLogo');
			
			Route::delete('/delete/spinthewheelinform-file-logo', 'Api\SettingController@deleteSpinTheWheelInformFileLogo');
			
			Route::post('/update/email-logo', 'Api\SettingController@updateEmailLogo');
			
			Route::delete('/delete/email-logo', 'Api\SettingController@deleteEmailLogo');
			
			Route::post('/update/cookie-reset', 'Api\SettingController@updateCookieReset');
			
		});
		
		Route::group(['prefix' => 'shopify-app'], function() {
			
			Route::post('app', 'Api\ShopifyAppController@getApp');
			
			Route::post('app/update-modal-flag', 'Api\ShopifyAppController@updateModalFlag');
			
			Route::get('orders', 'Api\ShopifyOrderController@getOrders');
			
		});
		
		Route::group(['prefix' => 'integrations'], function() {
			
			Route::get('get', 'Api\Integrations@getServices');
			
			Route::get('get/service', 'Api\Integrations@getService');
			
			Route::post('add', 'Api\Integrations@saveService');
			
			Route::patch('update', 'Api\Integrations@updateService');
			
		});
		
		Route::group(['prefix' => 'license-key'], function() {
			
			Route::put('/add', 'Api\LicenseKeyController@add');
			
		});
		
	});
	
	Route::group(['middleware' => ['auth:api']], function() {
		
		Route::group(['prefix' => 'subscription'], function() {
			
			Route::post('/generate-isracard-subscription', 'Api\SubscriptionController@generateIsracardSubscription');
			
		});
		
		Route::group(['prefix' => 'plans'], function() {
			
			Route::get('/all', 'Api\PlansController@index');
			
			Route::get('/upgrade', 'Api\PlansController@getUpgradeLink');
			
		});
		
		Route::group(['prefix' => 'affiliates'], function() {
			
			Route::get('/all', 'Api\AffiliatesController@index');
			
			Route::patch('/owner/update', 'Api\AffiliatesController@ownerUpdate');
			
		});
		
	});
	
	Route::group(['prefix' => 'game'], function() {
		
		Route::post('/add/impression', 'Api\GameController@addImpression');
		
		Route::post('/add/hit', 'Api\GameController@addHit');
		
		Route::post('/log-errors', 'Api\GameController@logFrontendErrors');
		
	});
	
	Route::group(['prefix' => 'coupon'], function() {
		
		Route::post('code', 'Api\CouponController@getCode');
		
	});
	
	Route::post('check/email', 'Api\ValidationController@checkEmail');

	Route::group(['prefix' => 'zapier'], function() {
		Route::get('/Auth', 'Api\ZapierController@auth');
		Route::get('/polling', 'Api\ZapierController@polling');
	});
});