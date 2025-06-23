<?php namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use CouponZone\Script\Commands\AppendScriptCommand;
use CouponZone\Settings\Setting;
use CouponZone\Settings\Repository\Eloquent\Setting as SettingRepo;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
	/**
	 * @param Setting $setting
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateVisibility(Setting $setting, Request $request)
	{
		$steps = $request->get('steps', '');
		
		updateOption('steps', $steps);
		
		$disabled = (new SettingRepo())->reverseVisibility($setting);
		
		if ($disabled === false)
		{
			dispatch((new AppendScriptCommand(getShop()->id, true))->onQueue('high'));
		}
		
		$word = $disabled ? 'dis' : 'en';
		
		$message = 'Coupon Zone is ' . $word . 'abled from your storefront';
		
		return json(true, 200, $message);
    }
}
