<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Promotions\Shops\Shop;

class AdminController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function listOfShops(Request $request)
	{
		$domain = $request->get('domain');
		
		if ($domain && !strstr($domain, '.myshopify.com'))
		{
			$domain = $domain . '.myshopify.com';
		}
		
		$page = 15;
		
		$shops = $domain
			? Shop::where('shopify_domain', $domain)->paginate($page)
			: $this->_getShops($page);
		
		return view('admin.list-of-shops', ['shops' => $shops]);
    }
	
	/**
	 * @param int $page
	 *
	 * @return mixed
	 */
	private function _getShops($page = 15)
	{
		return Shop::select(['shops.*'])
		           ->leftJoin('campaigns as c', 'c.shop_id', '=', 'shops.id')
		           ->whereNull('c.deleted_at')
		           ->groupBy('shops.id')
		           ->orderByRaw('count(c.id) desc')
		           ->paginate($page);
    }
}
