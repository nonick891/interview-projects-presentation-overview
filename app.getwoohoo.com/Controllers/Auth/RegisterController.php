<?php namespace App\Http\Controllers\Auth;

use App\LuckyCoupon\Users\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use LuckyCoupon\LicenseKeys\Commands\AcquireLicenseToUserCommand;
use LuckyCoupon\OtherSubscriptions\Commands\AddOtherSubscriptionCommand;
use LuckyCoupon\Users\Commands\AddAffiliatesToUserCommand;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
	
	/**
	 * Show the application registration form.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function showRegistrationForm(Request $request)
	{
		if ($request->is('special-deal'))
		{
			$selectedPlan = 'life-time-access';
		}
		else
		{
			$data = $request->all();
			
			$selectedPlan = $this->_getPlanName($data);
		}
		
		return view('auth.register', compact('selectedPlan'));
	}
	
	/**
	 * @param $data
	 * @return string
	 */
	private function _getPlanName($data)
	{
		$plans = ['jvzoo-premium-plus', 'jvzoo-premium'];
		
		foreach ($plans as $plan)
		{
			if (array_key_exists($plan, $data))
			{
				return $plan;
			}
		}
		
		return '';
	}
	
	/**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
	    $validateArray = [
		    'name' => 'required|string|max:255',
		    'email' => 'required|string|email|max:255|unique:users',
		    'password' => 'required|string|min:6|confirmed',
	    ];
    	
	    if (\Request::get('license_key'))
	    {
		    $validateArray['license_key'] = 'license_key';
	    }
	    
        return Validator::make($data, $validateArray);
    }
	
	/**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'is_first_time' => 1
        ]);
	
	    $ref = isset($data['ref']) ? (int)$data['ref'] : 0;
	    
	    dispatch(new AddAffiliatesToUserCommand($user, $ref));
     
	    dispatch(new AddOtherSubscriptionCommand($user, $data['plan_name']));
	    
	    $licenseKey = data_get($data, 'license_key', false);
	
	    if ($licenseKey)
	    {
		    dispatch(new AcquireLicenseToUserCommand($licenseKey, $user));
	    }
	    
	    setcookie('signup', '1');
	    
        return $user;
    }
}
