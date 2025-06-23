<?php

namespace App\Http\Controllers;

use CouponZone\Shops\Shop;
use Illuminate\Http\Request;

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
		
		if (!$domain)
		{
			$shops = $this->getShops($page);
		}
		else
		{
			$shops = Shop::where('shopify_domain', $domain)->paginate($page);
		}
		
		return view('list-of-shops', ['shops' => $shops]);
    }
	
	/**
	 * @param $page
	 *
	 * @return mixed
	 */
	private function getShops($page)
	{
		return Shop::leftJoin('counters as c', function ($join){
			$join->on('shops.id', '=', 'c.field_value')
			     ->where('c.name', 'widget-download')
			     ->where('c.field_name', 'shop_id');
		})
         ->select(['shops.*', \DB::raw('c.day + c.week + c.month + c.year + c.total as summ')])
         ->orderBy('summ', 'desc')
         ->paginate($page);
	}
}
