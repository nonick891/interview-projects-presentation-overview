<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Promotions\Integrations\Shopify\Shop;

class ShopifyAuth extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function authenticate(Request $request)
	{
		try
		{
			$shop = getShop();
			
			$shopifyData = (new Shop($shop))->get();
			
			if (!$shopifyData)
			{
				$authShopRequest = app('OhMyBrew\ShopifyApp\Requests\AuthShop');
				
				$authShopRequest->replace($request->all());
				
				return app('OhMyBrew\ShopifyApp\Controllers\AuthController')
					->authenticate(Request::createFromBase($authShopRequest));
			}
			else
			{
				return view('spa');
			}
		}
		catch (\Exception $e)
		{
			return view('shopify-app::auth.index');
		}
    }
}
