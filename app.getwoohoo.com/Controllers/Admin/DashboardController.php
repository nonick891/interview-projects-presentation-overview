<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LuckyCoupon\Counters\Commands\SetAdminCounterCommand;
use LuckyCoupon\FilterSubscribers\FilterSubscriber;
use LuckyCoupon\Redis\Classes\Queue\CouponCodeRange;
use LuckyCoupon\Redis\Classes\Queue\ImageRemoveRange;
use LuckyCoupon\Redis\Classes\Queue\ImageUpdateRange;
use LuckyCoupon\Redis\Classes\Queue\LegitimacyCheckRange;
use LuckyCoupon\Redis\Classes\Queue\OutOfImpressionsRange;
use LuckyCoupon\Redis\Classes\Queue\SaveSpacesScriptRange;
use LuckyCoupon\Redis\Classes\Queue\SetCounterRange;
use LuckyCoupon\Redis\Classes\Queue\ShopifyScriptRange;
use LuckyCoupon\Redis\Classes\Queue\ShopSetupRange;
use LuckyCoupon\Redis\Classes\Queue\ShopUninstallRange;
use LuckyCoupon\Redis\Classes\Queue\UpdateGameRange;
use LuckyCoupon\Redis\Classes\Queue\UpdateHitLocationRange;
use LuckyCoupon\Redis\Classes\Wrappers\GameStatisticSubscribersRange;
use LuckyCoupon\Redis\Classes\Wrappers\OrdersActionRange;
use LuckyCoupon\ShopifyAppCharges\Commands\DeleteLocalChargesHistoryCommand;
use LuckyCoupon\ShopifyAppCharges\Commands\GetAppOneTimeChargesAdminViewCommand;
use LuckyCoupon\ShopifyAppCharges\Commands\RenewShopifyAnnualCommand;
use LuckyCoupon\ShopifyApps\Commands\GetShopifySubscribersViewCommand;
use LuckyCoupon\ShopifyApps\Commands\GetAppChargesAdminViewCommand;
use LuckyCoupon\ShopifyBillings\Commands\GetShopifyBillingAdminViewCommand;
use LuckyCoupon\ShopifyBillings\Commands\GetShopifyTrialsAdminViewCommand;
use LuckyCoupon\Statistics\Commands\GetStatisticsViewCommand;
use LuckyCoupon\Statistics\Commands\ShowAllGamesStatistics;
use LuckyCoupon\Statistics\Commands\ShowAllStoresStatistics;
use LuckyCoupon\Users\Commands\AuthUserCommand;
use LuckyCoupon\Users\Commands\GetAdminDataCommand;
use LuckyCoupon\Users\Commands\GetDuplicateUserStoresViewCommand;
use LuckyCoupon\Users\Commands\GetRegularUsersAdminViewCommand;
use LuckyCoupon\Users\Commands\GetShopifyUsersAdminViewCommand;
use LuckyCoupon\Users\Commands\GetUninstalledShopifyStoresViewCommand;
use LuckyCoupon\Users\UserEloquentRepository;
use LuckyCoupon\Visitors\Visitor;
use ShopifyIntegration\Commands\DeleteLoadingScriptCommand;
use ShopifyIntegration\Commands\FetchAndHandleOrdersCommand;
use ShopifyIntegration\Commands\GetLoadingScriptsViewCommand;
use ShopifyIntegration\Commands\GetShopifyDiscountListsCommands;
use ShopifyIntegration\Commands\GetShopifyPriceRulesCommands;
use ShopifyIntegration\Commands\GetShopifyWebHooksCommand;
use ShopifyIntegration\Commands\InstallLoadingScriptCommand;
use ShopifyIntegration\RecurringCharges\Commands\ExtendShopifyTrialCommand;
use ShopifyIntegration\ShopifyAppOrders\Commands\GetShopifyAppOrdersAdminViewCommand;
use ShopifyIntegration\ShopifyAppOrders\Commands\GetShopifyOrdersStatisticsViewCommand;

class DashboardController extends Controller
{
	/**
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index(Request $request)
	{
		return dispatch(new GetShopifyUsersAdminViewCommand($request, true));
	}
	
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function uninstalled(Request $request)
	{
		return dispatch(new GetShopifyUsersAdminViewCommand($request, false));
	}
	
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function uninstalledStoreList(Request $request)
	{
		return dispatch(new GetUninstalledShopifyStoresViewCommand($request));
	}
	
	/**
	 * @param $userId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function charges($userId)
	{
		return dispatch(new GetAppChargesAdminViewCommand($userId));
	}
	
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function getBilling(Request $request)
	{
		return dispatch(new GetShopifyBillingAdminViewCommand($request));
	}
	
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function getTrials(Request $request)
	{
		return dispatch(new GetShopifyTrialsAdminViewCommand($request));
	}
	
	/**
	 * @param $userId
	 * @return mixed
	 */
	public function annualCharges($userId)
	{
		return dispatch(new GetAppOneTimeChargesAdminViewCommand($userId));
	}
	
	/**
	 * @param $userId
	 * @return mixed
	 */
	public function deleteLocalCharges($userId)
	{
		return dispatch(new DeleteLocalChargesHistoryCommand($userId));
	}
	
	/**
	 * @param $userId
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function appAuth($userId)
	{
		return dispatch(new AuthUserCommand($userId));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function regular(Request $request)
	{
		return dispatch(new GetRegularUsersAdminViewCommand($request));
	}
	
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function allShopifySubscribers(Request $request)
	{
		return dispatch(new GetShopifySubscribersViewCommand($request));
	}
	
	/**
	 * @return mixed
	 */
	public function shopifyStatistics()
	{
		return dispatch(new GetStatisticsViewCommand());
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function exitToAdmin(Request $request)
	{
		$userId = dispatch(new GetAdminDataCommand($request));
		
		return dispatch(new AuthUserCommand($userId, '/admin/shopify'));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function setCounter(Request $request)
	{
		return dispatch(new SetAdminCounterCommand($request));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function allStoresStatistics(Request $request)
	{
		return dispatch(new ShowAllStoresStatistics($request));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function perGameStatistics(Request $request)
	{
		return dispatch(new ShowAllGamesStatistics($request));
	}
	
	/**
	 * @param $userId
	 * @return mixed
	 */
	public function getWebHooks($userId)
	{
		return dispatch(new GetShopifyWebHooksCommand($userId));
	}
	
	/**
	 * @param $userId
	 * @return mixed
	 */
	public function getPriceRules($userId)
	{
		return dispatch(new GetShopifyPriceRulesCommands($userId));
	}
	
	/**
	 * @param $appId
	 * @param $priceRuleID
	 * @return mixed
	 */
	public function getDiscountLists($appId, $priceRuleID)
	{
		return dispatch(new GetShopifyDiscountListsCommands($appId, $priceRuleID));
	}
	
	/**
	 * @param $userId
	 * @return mixed
	 */
	public function getShopifyOrders($userId)
	{
		return dispatch(new GetShopifyAppOrdersAdminViewCommand($userId));
	}
	
	/**
	 * @return mixed
	 */
	public function getShopifyOrdersStatistics()
	{
		return dispatch(new GetShopifyOrdersStatisticsViewCommand());
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function fetchShopifyOrders(Request $request)
	{
		return dispatch(new FetchAndHandleOrdersCommand(data_get(
			(new UserEloquentRepository())->getUserById($request->get('user_id')),
		'app_id', 0)));
	}
	
	/**
	 * @param $userId
	 * @return mixed
	 */
	public function getShopifyScript($userId)
	{
		return dispatch(new GetLoadingScriptsViewCommand($userId));
	}
	
	/**
	 * @param $userId
	 * @return mixed
	 */
	public function installShopifyScript($userId)
	{
		return dispatch(new InstallLoadingScriptCommand($userId));
	}
	
	/**
	 * @param $scriptId
	 * @param Request $request
	 * @return mixed
	 * @internal param $scriptId
	 */
	public function deleteShopifyScript($scriptId, Request $request)
	{
		return dispatch(new DeleteLoadingScriptCommand($scriptId, $request));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function extendShopifyTrial(Request $request)
	{
		return dispatch(new ExtendShopifyTrialCommand($request));
	}
	
	/**
	 * @return mixed
	 */
	public function duplicateStores()
	{
		return dispatch(new GetDuplicateUserStoresViewCommand());
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function renewAnnual(Request $request)
	{
		return dispatch(new RenewShopifyAnnualCommand($request));
	}
	
	public function emailHits()
	{
		return  view('admin.redis_hit_counts');
	}
	
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function filterSubscribers(Request $request)
	{
		$filterNoEmail = $request->get('filter', null);
		
		$filterSubscribers = FilterSubscriber::orderBy('created_at', 'desc');
		
		if ($filterNoEmail === 'true' || is_null($filterNoEmail))
		{
			$filterSubscribers = $filterSubscribers->where('email_address', '<>', 'non-collect-option-on');
		}
		
		$filteredHits = $filterSubscribers->paginate(100);
		
		return view('admin.filter_subscribers', compact(['filteredHits']));
	}
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function fingerprint()
	{
		$visitors = Visitor::orderBy('created_at', 'desc')->paginate(25);
		
		return view('admin.fingerprint', compact('visitors'));
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
	 */
	public function services()
	{
		return view('admin.services', [
			'subscribers' => (new GameStatisticSubscribersRange())->lLen('all'),
			'orders' => (new OrdersActionRange())->lLen('all'),
			'out_of_impressions' => (new OutOfImpressionsRange())->lLen('all'),
			'coupon_code' => (new CouponCodeRange())->lLen('all'),
			'shopify_script' => (new ShopifyScriptRange())->lLen('all'),
			'save_spaces_script' => (new SaveSpacesScriptRange())->lLen('all'),
			'update_hit_location' => (new UpdateHitLocationRange())->lLen('all'),
			'shop_setup_range' => (new ShopSetupRange())->lLen('all'),
			'shop_uninstall_range' => (new ShopUninstallRange())->lLen('all'),
			'legitimacy_check_range' => (new LegitimacyCheckRange())->lLen('all'),
			'image_update_range' => (new ImageUpdateRange())->lLen('all'),
			'set_counter_range' => (new SetCounterRange())->lLen('all'),
			'update_game_range' => (new UpdateGameRange())->lLen('all'),
			'image_remove_range' => (new ImageRemoveRange())->lLen('all'),
		]);
	}
}
