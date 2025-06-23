<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Promotions\Campaigns\Eloquent\Repository as CampaignsRepo;
use Promotions\Shopify\Products\Repository as ProductsRepo;

class DataController extends Controller
{
	/**
	 * @var CampaignsRepo
	 */
	private $campaignsRepo;
	
	private $productsRepo;
	
	/**
	 * DataController constructor.
	 */
	public function __construct()
	{
		$this->campaignsRepo = new CampaignsRepo();
		
		$this->productsRepo = new ProductsRepo();
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function entryPoint()
	{
		$shop = getShop();
		
		$shop->user = $shop->user;
		
		$shop = $shop->toArray();
		
		data_set($shop, 'shopify_token', null);
		
		$response = [
			'shop' => $shop,
			'icons' => getShopIcons($shop['id']),
			'campaigns' => $this->campaignsRepo->get(),
			'products' => $this->productsRepo->get(),
		    'features' => config('sticker.features')
		];
		
		$cookie = ['shop_domain' => data_get($shop, 'shopify_domain', '')];
		
		return success_json($response, $cookie);
	}
}
