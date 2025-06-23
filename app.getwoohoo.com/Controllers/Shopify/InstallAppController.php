<?php namespace App\Http\Controllers\Shopify;

use Illuminate\Http\Request;
use LuckyCoupon\Plans\Commands\GetShopifyPlansViewCommand;
use LuckyCoupon\ShopifyApps\Commands\UpdateAccessTokenCommand;
use LuckyCoupon\ShopifyApps\Commands\UpdateShopifyAppCommand;
use ShopifyIntegration\Apps\Commands\ChargeCommand;
use ShopifyIntegration\Apps\Commands\ChargeCompleteCommand;
use ShopifyIntegration\Apps\Commands\CheckBeforeInstallAppCommand;
use ShopifyIntegration\Apps\Commands\InstallAppCommand;
use ShopifyIntegration\Requests\Apps\GetInstallAppRequest;
use App\Http\Controllers\Controller;
use ShopifyIntegration\Apps\Commands\DispatchInstallAppCommand;

class InstallAppController extends Controller
{
	/**
	 * @param GetInstallAppRequest $request
	 * @return mixed
	 */
	public function install(GetInstallAppRequest $request)
	{
		$shopUrl = removeProtocol($request->get('shop_url'));
		
		$isActiveAndHasAccess = dispatch(new CheckBeforeInstallAppCommand($shopUrl));
		
		return $isActiveAndHasAccess && !$request->get('pass', null)
			? dispatch(new InstallAppCommand($shopUrl))
			: dispatch(new DispatchInstallAppCommand($request));
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getInstallForm()
	{
		return view('shopify.auth_form');
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function installComplete()
	{
		return view('shopify.thank_auth');
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function installFail()
	{
		return view('shopify.fail_auth');
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getPlans()
	{
		return dispatch(new GetShopifyPlansViewCommand());
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function charge(Request $request)
	{
		return dispatch(new ChargeCommand($request));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function chargeComplete(Request $request)
	{
		return dispatch(new ChargeCompleteCommand($request));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function chargeCompleteAnnual(Request $request)
	{
		return dispatch(new ChargeCompleteCommand($request, true));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function updateApp(Request $request)
	{
		return dispatch(new UpdateShopifyAppCommand($request));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function updateToken(Request $request)
	{
		return dispatch(new UpdateAccessTokenCommand($request));
	}
}
