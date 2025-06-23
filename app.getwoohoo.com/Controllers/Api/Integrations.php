<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use LuckyCoupon\Integrations\Commands\GetServiceDataCommand;
use LuckyCoupon\Integrations\Commands\GetServicesDataCommand;
use LuckyCoupon\Integrations\Commands\SaveServiceCommand;
use LuckyCoupon\Integrations\Commands\UpdateServiceCommand;
use LuckyCoupon\Requests\Integrations\GetIntegrationServiceDataRequest;
use LuckyCoupon\Requests\Integrations\PostIntegrationServiceRequest;
use LuckyCoupon\Requests\Integrations\UpdateIntegrationServiceRequest;

class Integrations extends Controller
{
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getServices(Request $request)
	{
		return dispatch(new GetServicesDataCommand($request->user()));
	}
	
	/**
	 * @param GetIntegrationServiceDataRequest $request
	 * @return mixed
	 */
	public function getService(GetIntegrationServiceDataRequest $request)
	{
		return dispatch(new GetServiceDataCommand($request));
    }
	
	/**
	 * @param PostIntegrationServiceRequest $request
	 * @return mixed
	 */
	public function saveService(PostIntegrationServiceRequest $request)
	{
		return dispatch(new SaveServiceCommand($request));
    }
    
	/**
	 * @param UpdateIntegrationServiceRequest $request
	 * @return mixed
	 */
	public function updateService(UpdateIntegrationServiceRequest $request)
	{
		return dispatch(new UpdateServiceCommand($request));
    }
    
}
