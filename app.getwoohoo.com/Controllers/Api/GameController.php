<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use LuckyCoupon\Games\Commands\AddGameCommand;
use LuckyCoupon\Games\Commands\AddGameWithSiteCommand;
use LuckyCoupon\Games\Commands\DeleteGameCommand;
use LuckyCoupon\Games\Commands\DeleteGamesListCommand;
use LuckyCoupon\Games\Commands\GetAccountSubscribersCommand;
use LuckyCoupon\Games\Commands\GetGamesCommand;
use LuckyCoupon\Games\Commands\GetSubscribersCommand;
use LuckyCoupon\Games\Commands\GetSubscribersCountCommand;
use LuckyCoupon\Games\Commands\UpdateGameCommand;
use LuckyCoupon\Games\Commands\UpdateGameStatisticCommand;
use LuckyCoupon\Requests\Games\AddGameStatisticRequest;
use LuckyCoupon\Requests\Games\DeleteGameRequest;
use LuckyCoupon\Requests\Games\GetGamesRequest;
use LuckyCoupon\Requests\Games\LogFrontendGameErrorsRequest;
use LuckyCoupon\Requests\Games\PatchGameRequest;
use LuckyCoupon\Requests\Games\PostGetAccountSubscribers;
use LuckyCoupon\Requests\Games\PostGetSubscribers;
use LuckyCoupon\Requests\Games\PutGameRequest;
use LuckyCoupon\Requests\Games\AddEmailAndStatistic;
use LuckyCoupon\Requests\Games\PutGameWithSiteRequest;
use LuckyCoupon\Requests\Games\SyncOldGameHitsWithShopifyRequest;
use LuckyCoupon\Requests\Games\UpdateDeleteWebhookCustomerRequest;
use LuckyCoupon\ShopifyAppCustomers\Commands\SyncOldGameHitsWithShopifyCommand;
use LuckyCoupon\ShopifyApps\Commands\UpdateDeleteWebhookCustomerCommand;

/**
 * Class GameController
 * @package App\Http\Controllers\Api
 */
class GameController extends ValidateController
{
	
	/**
	 * @param GetGamesRequest $request
	 * @return array
	 */
	public function index(GetGamesRequest $request)
	{
		return dispatch(new GetGamesCommand($request));
    }
	
	/**
	 * @param PutGameRequest $request
	 * @return array|bool
	 */
	public function add(PutGameRequest $request)
	{
		return dispatch(new AddGameCommand($request));
    }
	
	/**
	 * @param PutGameWithSiteRequest $request
	 * @return mixed
	 */
	public function createWithSite(PutGameWithSiteRequest $request)
	{
		return dispatch(new AddGameWithSiteCommand($request));
    }
    
	/**
	 * @param PatchGameRequest $request
	 * @return array|bool
	 */
	public function update(PatchGameRequest $request)
	{
		return dispatch(new UpdateGameCommand($request));
    }
	
	/**
	 * @param DeleteGameRequest $request
	 * @return array|bool
	 */
	public function delete(DeleteGameRequest $request)
	{
		return dispatch(new DeleteGameCommand($request));
    }

	/**
	 * @param int $userId
	 *
	 * @return mixed
	 */
	public function deleteList(int $userId)
	{
		return dispatch(new DeleteGamesListCommand($userId));
	}

	/**
	 * @param AddGameStatisticRequest $request
	 * @return bool
	 */
	public function addImpression(AddGameStatisticRequest $request)
	{
		//@TODO: separate logic impressions from hits
		//@TODO: find a way about separation of hits and impressions
		return dispatch(new UpdateGameStatisticCommand('impressions', $request));
	}
	
	/**
	 * @param AddEmailAndStatistic $request
	 * @return bool
	 */
	public function addHit(AddEmailAndStatistic $request)
	{
		//@TODO: separate logic impressions from hits
		//@TODO: find a way about separation of hits and impressions
		return dispatch(new UpdateGameStatisticCommand('hits', $request));
	}
	
	/**
	 * @param PostGetSubscribers $request
	 * @return mixed
	 */
	public function getSubscribers(PostGetSubscribers $request)
	{
		return dispatch(new GetSubscribersCommand($request));
	}
	
	/**
	 * @param PostGetAccountSubscribers $request
	 * @return mixed
	 */
	public function getAccountSubscribers(PostGetAccountSubscribers $request)
	{
		return dispatch(new GetAccountSubscribersCommand($request));
	}
	
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getSubscribersCount(Request $request)
	{
		return dispatch(new GetSubscribersCountCommand($request));
	}
	
	/**
	 * @param LogFrontendGameErrorsRequest $request
	 * @return mixed
	 */
	public function logFrontendErrors(LogFrontendGameErrorsRequest $request)
	{
		return $this->_response('{"error": "200 OK"}');
		// return dispatch(new LogFrontendErrorsCommand($request));
	}
	
	/**
	 * @param Request|UpdateDeleteWebhookCustomerRequest $request
	 * @return mixed
	 */
	public function updateDeleteWebhookCustomer(UpdateDeleteWebhookCustomerRequest $request)
	{
		return dispatch(new UpdateDeleteWebhookCustomerCommand($request));
	}
	
	/**
	 * @param SyncOldGameHitsWithShopifyRequest|Request $request
	 * @return mixed
	 */
	public function syncOldGameHits(SyncOldGameHitsWithShopifyRequest $request)
	{
		return dispatch(new SyncOldGameHitsWithShopifyCommand($request));
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public function _response($data)
	{
		return response($data, 200)
			->header('Access-Control-Allow-Origin', '*');
	}
}
