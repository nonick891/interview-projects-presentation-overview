<?php namespace App\Http\Controllers\Api;

use LuckyCoupon\Core\Commands\GetCachedCoreDataCommand;

class CoreDataController extends ValidateController
{
	/**
	 * @return mixed
	 */
	public function index()
	{
		return response(dispatch(new GetCachedCoreDataCommand()), 200)
			->header('Access-Control-Allow-Origin', '*');
    }
}
