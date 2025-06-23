<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LuckyCoupon\Validation\GetTokenCommand;
use LuckyCoupon\Requests\Validation\GetTokenRequest;
use LuckyCoupon\Requests\Games\CheckEmailStatisticRequest;
use LuckyCoupon\Statistics\Commands\CheckEmailCommand;
use LuckyCoupon\Visitors\Commands\CheckBrowserCommand;

class ValidationController extends Controller
{
	/**
	 * @param CheckEmailStatisticRequest $request
	 * @return bool
	 */
	public function checkEmail(CheckEmailStatisticRequest $request)
	{
		return dispatch(new CheckEmailCommand($request));
    }
	
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function checkBrowser(Request $request)
	{
		return dispatch(new CheckBrowserCommand($request));
    }

	/**
	 * @param GetTokenRequest $request
	 *
	 * @return mixed
	 */
	public function getToken(GetTokenRequest $request)
	{
		return dispatch(new GetTokenCommand($request));
	}
}
