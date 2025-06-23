<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use LuckyCoupon\Requests\ShopifyOrders\GetShopifyOrdersRequest;
use ShopifyIntegration\ShopifyAppOrders\Commands\GetShopifyOrdersCommand;
use ShopifyIntegration\ShopifyAppOrders\Commands\GetShopifyProductPictureCommand;

class ShopifyOrderController extends Controller
{
	/**
	 * @param GetShopifyOrdersRequest $request
	 * @return mixed
	 */
	public function getOrders(GetShopifyOrdersRequest $request)
	{
		return dispatch(new GetShopifyOrdersCommand($request));
    }
	
	/**
	 * @param $productId
	 * @return mixed
	 */
	public function getProductImage($productId)
	{
		return dispatch(new GetShopifyProductPictureCommand($productId));
    }
	
	/**
	 * @param $userId
	 * @param $productId
	 * @return mixed
	 */
	public function getAdminProductImage($userId, $productId)
	{
		return dispatch(new GetShopifyProductPictureCommand($productId, $userId));
    }
}
