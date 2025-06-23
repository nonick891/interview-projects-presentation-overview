<?php

Route::group(['middleware' => ['auth']], function() {

	Route::get('/', 'EntryPoint@index')->name('home');

});

Auth::routes();

Route::get('special-deal', 'Auth\RegisterController@showRegistrationForm');

Route::get('/{id}/script.js', 'Api\TemplateController@getScript');

Route::get('/script.js', 'Api\TemplateController@getShopifyScript');

Route::get('/{game_id}/wheel.svg', 'Api\TemplateController@getWheel');

Route::get('/{game_id}/fullwheel.svg', 'Api\TemplateController@getFullWheel');

Route::get('/{game_id}/spinthewheel.svg', 'Api\TemplateController@getSpinTheWheel');

Route::get('/{game_id}/spinthewheel_background.svg', 'Api\TemplateController@getSpinTheWheelBackground');

Route::get('/{game_id}/reveal.svg', 'Api\TemplateController@revealPattern');

Route::get('/background-image/{user_id}/{game_id}/{file_name?}', 'Api\TemplateController@getBackgroundImage');

Route::get('/download-style/{type}', 'Api\TemplateController@getLoadableStyle');

Route::get('/extended-trial', 'Api\TrialController@extraTrial');

Route::get('/check-browser', 'Api\ValidationController@checkBrowser');

Route::post('/token', 'Api\ValidationController@getToken');

Route::group(['prefix' => 'shopify'], function () {

	Route::get('auth', 'Shopify\InstallAppController@getInstallForm');

	Route::get('auth/install', 'Shopify\InstallAppController@install');

	Route::get('auth/update-token', 'Shopify\InstallAppController@updateToken');

	Route::get('auth/install/complete', 'Shopify\InstallAppController@installComplete');

	Route::get('auth/install/fail', 'Shopify\InstallAppController@installFail');

	Route::get('plans', 'Shopify\InstallAppController@getPlans');

	Route::get('charge', 'Shopify\InstallAppController@charge');

	Route::get('charge/complete', 'Shopify\InstallAppController@chargeComplete');

	Route::get('charge/complete/annual', 'Shopify\InstallAppController@chargeCompleteAnnual');

	Route::patch('app', 'Shopify\InstallAppController@updateApp');

	Route::get('picture/{product_id}.jpg', 'Api\ShopifyOrderController@getProductImage');

	Route::get('picture/{user_id}/{product_id}.jpg', 'Api\ShopifyOrderController@getAdminProductImage');

});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'isAdmin']], function() {

	Route::get('/services', 'Admin\DashboardController@services');

	Route::get('/charges/{id}', 'Admin\DashboardController@charges');

	Route::get('/annual-charges/{id}', 'Admin\DashboardController@annualCharges');

	Route::get('/app-auth/{id}', 'Admin\DashboardController@appAuth');

	Route::get('/regular', 'Admin\DashboardController@regular');

	Route::get('/email-hits', 'Admin\DashboardController@emailHits');
	
	Route::get('/filter-subscribers', 'Admin\DashboardController@filterSubscribers');
	
	Route::get('/fingerprint', 'Admin\DashboardController@fingerprint');
	
	Route::group(['prefix' => 'shopify'], function() {

		Route::get('/', 'Admin\DashboardController@index');
		
		Route::get('/uninstalled', 'Admin\DashboardController@uninstalled');
		
		Route::get('/uninstalled_store_list', 'Admin\DashboardController@uninstalledStoreList');

		Route::get('/billing', 'Admin\DashboardController@getBilling');
		
		Route::get('/trials', 'Admin\DashboardController@getTrials');
		
		Route::get('/statistics', 'Admin\DashboardController@shopifyStatistics');

		Route::get('/subscribers', 'Admin\DashboardController@allShopifySubscribers');

		Route::post('/counter', 'Admin\DashboardController@setCounter');

		Route::get('/webhooks/{user_id}', 'Admin\DashboardController@getWebHooks');

		Route::get('/price-rules/{user_id}', 'Admin\DashboardController@getPriceRules');

		Route::get('/discount/{app_id}/{price_rule}', 'Admin\DashboardController@getDiscountLists');

		Route::get('/orders/', 'Admin\DashboardController@getShopifyOrdersStatistics');

		Route::get('/orders/{user_id}', 'Admin\DashboardController@getShopifyOrders');

		Route::post('/orders/fetch', 'Admin\DashboardController@fetchShopifyOrders');

		Route::get('/scripts/{user_id}', 'Admin\DashboardController@getShopifyScript');

		Route::get('/scripts/install/{user_id}', 'Admin\DashboardController@installShopifyScript');

		Route::delete('/scripts/delete/{script_id}', 'Admin\DashboardController@deleteShopifyScript');

		Route::post('/set-trial', 'Admin\DashboardController@extendShopifyTrial');

		Route::delete('/local-charges/{user_id}', 'Admin\DashboardController@deleteLocalCharges');

		Route::post('/renew-annual', 'Admin\DashboardController@renewAnnual');

		Route::get('duplicate-stores', 'Admin\DashboardController@duplicateStores');
	});

	Route::get('/all-statistics', 'Admin\DashboardController@allStoresStatistics');

	Route::get('/by-games', 'Admin\DashboardController@perGameStatistics');

});

Route::group(['middleware' => ['auth']], function(){

	Route::get('/admin', 'Admin\DashboardController@exitToAdmin');

	Route::group(['prefix' => 'integrations'], function () {

		Route::get('get-auth-url', 'Integrations\InstallController@getAuthUrl');

	});

});

Route::group(['prefix' => 'integrations'], function (){

	Route::get('mailchimp-handler', 'Integrations\InstallController@mailChimp');

	Route::get('jilt', 'Integrations\InstallController@jilt');

});

Route::group(['prefix' => 'webhooks'], function() {

	Route::group(['prefix' => 'isracard'], function() {

		Route::post('subscription', 'Webhooks\IsracardController@subscription');

		Route::get('/', 'Webhooks\IsracardController@index');

	});

	Route::group(['prefix' => 'shopify'], function() {

		Route::post('orders', 'Webhooks\ShopifyController@orders');

		Route::post('setup', 'Webhooks\ShopifyController@setup');

		Route::post('customers', 'Webhooks\ShopifyController@customers');

		Route::post('customers/data_request', 'Webhooks\ShopifyController@dataRequest');

		Route::post('customers/redact', 'Webhooks\ShopifyController@deleteCustomersData');

		Route::post('shop/redact', 'Webhooks\ShopifyController@deleteShopData');

		Route::post('themes/update', 'Webhooks\ShopifyController@updateTheme');

	});

	Route::group(['prefix' => 'fingerprint'], function() {
		
		Route::post('visit', 'Webhooks\FingerPrintController@visit');
		
	});
	
});

/*Route::get('test', function (){

});*/