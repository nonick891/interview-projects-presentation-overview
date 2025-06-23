<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LuckyCoupon\Requests\ShopifyApps\GetShopifyAppRequest;
use LuckyCoupon\Requests\ShopifyApps\UpdateShopifyAppModalFlagRequest;
use LuckyCoupon\ShopifyApps\Commands\GetShopifyAppCommand;
use LuckyCoupon\ShopifyApps\Commands\UpdateShopifyAppModalFlagCommand;
use LuckyCoupon\ShopifyUniqueCoupons\Commands\GetShopifyProductCollectionsCommand;

class ShopifyAppController extends Controller
{
	/**
	 * @param GetShopifyAppRequest $request
	 * @return mixed
	 */
	public function getApp(GetShopifyAppRequest $request)
	{
		return dispatch(new GetShopifyAppCommand($request));
    }
	
	/**
	 * @param UpdateShopifyAppModalFlagRequest $request
	 * @return mixed
	 */
	public function updateModalFlag(UpdateShopifyAppModalFlagRequest $request)
	{
		return dispatch(new UpdateShopifyAppModalFlagCommand($request));
    }
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getCollections(Request $request)
	{
		return dispatch(new GetShopifyProductCollectionsCommand($request));
    }
}
