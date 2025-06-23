<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use LuckyCoupon\Requests\Users\UpdateUserProfileRequest;
use LuckyCoupon\Users\Commands\GetAuthUserDataCommand;
use LuckyCoupon\Users\Commands\UpdateUserProfileCommand;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends Controller
{

	/**
	 * @return array
	 */
	public function index()
	{
		return dispatch(new GetAuthUserDataCommand(Auth::user()));
    }
	
	/**
	 * @param UpdateUserProfileRequest $request
	 * @return mixed
	 */
	public function updateProfile(UpdateUserProfileRequest $request)
	{
		return dispatch(new UpdateUserProfileCommand($request));
    }
	
	/**
	 * @param Request $request
	 */
	public function updateUserCalendarSettings(Request $request)
	{
		$user = \Auth::user();
		
		$calendarSetting = (int)$request->get('calendar_settings', 4);
		
		switch ($calendarSetting)
		{
			case 1: $setting = 1; break;
			case 2: $setting = 2; break;
			case 3: $setting = 3; break;
			default: case 4: $setting = 4; break;
			case 5: $setting = 5; break;
		}
		
		\Redis::set('user-settings-calendar:' . $user->id, $setting);
    }
}
