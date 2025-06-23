<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use LuckyCoupon\Plans\Commands\GetPlansCommand;
use ShopifyIntegration\Apps\Commands\GetUpgradeLinkCommand;
use ShopifyIntegration\Requests\Apps\GetUpgradeLinkRequest;

class PlansController extends ValidateController
{
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function index(Request $request)
	{
		return dispatch(new GetPlansCommand($request));
    }
	
	/**
	 * @param GetUpgradeLinkRequest $request
	 * @return mixed
	 */
	public function getUpgradeLink(GetUpgradeLinkRequest $request)
	{
		return dispatch(new GetUpgradeLinkCommand($request));
    }
}
