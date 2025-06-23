<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use LuckyCoupon\Requests\Zapier\ZapierAuthRequest;
use LuckyCoupon\Requests\Zapier\ZapierPollingRequest;
use LuckyCoupon\ZapierApps\Commands\ZapierAuthCommand;
use LuckyCoupon\ZapierApps\Commands\ZapierPollingCommand;

class ZapierController extends Controller
{
	/**
	 * @param ZapierAuthRequest $request
	 * @return array
	 */
	public function auth(ZapierAuthRequest $request)
	{
		return dispatch(new ZapierAuthCommand($request));
	}
	  
	/**
	 * @param ZapierPollingRequest $request
	 * @return array
	 */
	public function polling(ZapierPollingRequest $request)
	{
		return dispatch(new ZapierPollingCommand($request));
	}

}
