<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use LuckyCoupon\LicenseKeys\Commands\AddLicenseKeyCommand;
use LuckyCoupon\Requests\LicenseKeys\AddLicenseKeyRequest;

class LicenseKeyController extends Controller
{
	/**
	 * @param AddLicenseKeyRequest $request
	 * @return mixed
	 */
	public function add(AddLicenseKeyRequest $request)
	{
		return dispatch(new AddLicenseKeyCommand($request));
    }
}
