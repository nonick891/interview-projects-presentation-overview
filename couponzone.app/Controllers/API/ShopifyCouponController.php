<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use CouponZone\ShopifyCoupons\ShopifyCoupon;
use CouponZone\ShopifyCoupons\Repository\Eloquent\ShopifyCoupon as ShopifyCouponRepo;

class ShopifyCouponController extends Controller
{
	/**
	 * @var ShopifyCouponRepo
	 */
	private $shopifyCouponRepo;
	
	/**
	 * ZoneController constructor.
	 */
	public function __construct()
	{
		$this->shopifyCouponRepo = new ShopifyCouponRepo();
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param ShopifyCoupon $shopifyCoupon
	 * @return \Illuminate\Http\Response
	 */
	public function get(ShopifyCoupon $shopifyCoupon)
	{
		
    }
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
	
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  ShopifyCoupon  $shopifyCoupon
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, ShopifyCoupon $shopifyCoupon)
	{
	
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  ShopifyCoupon  $shopifyCoupon
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(ShopifyCoupon $shopifyCoupon)
	{
	
	}
}
