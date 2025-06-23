<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LuckyCoupon\AffiliateOwners\Commands\UpdateAffiliateOwnerCommand;
use LuckyCoupon\Affiliates\Commands\GetAffiliatesCommand;
use LuckyCoupon\Requests\Affiliates\UpdateAffiliateOwnerRequest;

class AffiliatesController extends Controller
{
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function index(Request $request)
	{
		return dispatch(new GetAffiliatesCommand($request));
    }
	
	/**
	 * @param UpdateAffiliateOwnerRequest $request
	 * @return mixed
	 */
	public function ownerUpdate(UpdateAffiliateOwnerRequest $request)
	{
		return dispatch(new UpdateAffiliateOwnerCommand($request));
    }
}
