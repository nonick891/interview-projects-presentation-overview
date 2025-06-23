<?php namespace App\Http\Controllers\Webhooks;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ShopifyIntegration\Commands\CustomersDataDeleteWebhookCommand;
use ShopifyIntegration\Commands\CustomersDataRequestWebhookCommand;
use ShopifyIntegration\Commands\OrdersWebHooksCommand;
use ShopifyIntegration\Commands\SetupWebHooksCommand;
use ShopifyIntegration\Commands\CustomersWebHooksCommand;
use ShopifyIntegration\Commands\ShopDataDeleteWebhookCommand;
use Symfony\Component\HttpFoundation\Response;

class ShopifyController extends Controller
{
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function orders(Request $request)
	{
		return dispatch(new OrdersWebHooksCommand($request));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function setup(Request $request)
	{
		return dispatch(new SetupWebHooksCommand($request));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function customers(Request $request)
	{
		return dispatch(new CustomersWebHooksCommand($request));
	}

	/**
	 * @param Request $request
	 *
	 * @return ResponseFactory|Application|Response
	 */
	public function dataRequest(Request $request)
	{
		dispatch(new CustomersDataRequestWebhookCommand(
			$request->all(),
			$request->headers->all()
		));

		return response()->json('OK');
	}

	/**
	 * @param Request $request
	 *
	 * @return ResponseFactory|Application|Response
	 */
	public function deleteCustomersData(Request $request)
	{
		dispatch(new CustomersDataDeleteWebhookCommand(
			$request->all(),
			$request->headers->all()
		));

		return response()->json('OK');
	}

	/**
	 * @param Request $request
	 *
	 * @return ResponseFactory|Application|Response
	 */
	public function deleteShopData(Request $request)
	{
		dispatch(new ShopDataDeleteWebhookCommand(
			$request->all(),
			$request->headers->all()
		));

		return response()->json('OK');
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateTheme()
	{
		return response()->json('OK');
	}
}
