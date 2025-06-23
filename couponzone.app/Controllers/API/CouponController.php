<?php namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use CouponZone\Coupons\Coupon;
use App\Http\Controllers\Controller;
use CouponZone\Coupons\Commands\UpdateCoupon;
use CouponZone\Coupons\Repository\Eloquent\Coupon as CouponRepo;

class CouponController extends Controller
{
	private $couponRepo;
	
	/**
	 * CouponController constructor.
	 */
	public function __construct()
	{
		$this->couponRepo = new CouponRepo();
	}
	
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update(Request $request)
	{
		$coupon = $request->all();
		
		return dispatch_now(new UpdateCoupon($coupon));
	}
	
	/**
	 * @param $couponId
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($couponId, Request $request)
	{
		$shop = getShop();
		
		$result = false;
		
		$coupon = Coupon::findOrFail($couponId);
		
		if ($coupon && $coupon->shop_id === $shop->id)
		{
			deleteCouponImage($shop->id, $coupon->id);
			
			$result = $coupon->delete();
		}
		
		return json($result, 200, 'Coupon deleted.');
	}
}
