<?php namespace App\Http\Controllers\Api;

use Integration\Isracard\Commands\GetIsracardSubscriptionCommand;
use Integration\Isracard\Requests\Subscriptions\PostGenerateSubscriptionRequest;

class IsracardController extends ValidateController
{
	/**
	 * @param PostGenerateSubscriptionRequest $request
	 * @return mixed
	 */
	public function generateSubscription(PostGenerateSubscriptionRequest $request)
	{
		return dispatch(new GetIsracardSubscriptionCommand($request));
	}
}