<?php namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Integration\Isracard\Commands\UpdateUserIsracardSubscriptionCommand;
use Log;
use LuckyCoupon\IsracardSubscriptions\Commands\GetIsracardPaymentResultCommand;
use LuckyCoupon\Users\Commands\GetUserByIsracardSubscriptionCommand;

class IsracardController extends Controller
{
	public function index(Request $request)
	{
		return dispatch(new GetIsracardPaymentResultCommand($request));
	}
	
	/**
	 * @param Request $request
	 */
	public function subscription(Request $request)
	{
		$variables = $request->all();
		
//		Log::error(print_r($variables, true));
		
		if ($variables['notify_type'])
		{
			$user = dispatch(new GetUserByIsracardSubscriptionCommand($request));
			
			dispatch(new UpdateUserIsracardSubscriptionCommand($user, $request));
		}
	}
}
