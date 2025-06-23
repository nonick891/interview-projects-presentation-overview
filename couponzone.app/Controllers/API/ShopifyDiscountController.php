<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use CouponZone\ShopifyDiscounts\ShopifyDiscount;
use CouponZone\ShopifyDiscounts\Repository\Eloquent\ShopifyDiscount as ShopifyDiscountRepo;

class ShopifyDiscountController extends Controller
{
	/**
	 * @var ShopifyDiscountRepo
	 */
	private $shopifyDiscountRepo;
	
	/**
	 * ShopifyDiscountController constructor.
	 */
	public function __construct()
	{
		$this->shopifyDiscountRepo = new ShopifyDiscountRepo();
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param ShopifyDiscount $shopifyDiscount
	 * @return \Illuminate\Http\Response
	 */
	public function get(ShopifyDiscount $shopifyDiscount)
	{
		//
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function create(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ShopifyDiscount  $shopifyDiscount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShopifyDiscount $shopifyDiscount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ShopifyDiscount  $shopifyDiscount
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShopifyDiscount $shopifyDiscount)
    {
        //
    }
}
