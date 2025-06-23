<?php namespace App\Http\Controllers\Integrations;

use App\Http\Controllers\Controller;
use App\Integration\Zapier\GetZapierAuthorizeUrlCommand;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Integration\Mailchimp\Commands\GetMailChimpAuthorizeUrlCommand;
use Integration\Mailchimp\Commands\InstallMailChimpAppCommand;
use LuckyCoupon\JiltApps\JiltApp;
use LuckyCoupon\Sites\Site;

class InstallController extends Controller
{
	/**
	 * @param Request $request
	 * @return string
	 */
	public function getAuthUrl(Request $request)
	{
		$service = $request->get('service');

		$siteId = $request->get('site_id');

		switch (strtolower($service)) {
			case 'mailchimp':
				return redirect(dispatch(new GetMailChimpAuthorizeUrlCommand($siteId)));
			case 'zapier':
				$result = dispatch(new GetZapierAuthorizeUrlCommand($siteId));
				die('this is a support off zapier here');
			default:
				return '<script>!function(){ window.close(); }()</script>';
		}
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function mailChimp(Request $request)
	{
		return dispatch(new InstallMailChimpAppCommand($request));
	}

	/**
	 * @param Request $request
	 */
	public function zapier(Request $request)
	{

	}

	public function jilt(Request $request)
	{

		$code = $request->get('code');
		$siteId = $request->get('state');

		$client = new Client();

		try {
			$options = [
				'client_id' => $request->getHttpHost() === 'dev-app.getwoohoo.com' ? '8add7c3779042dfd5c2708ef4e474c1be8cae2ce0ae7bdecf4fbfc0879d4746e' : 'adb699f1c2a9c630e942b579153ae4b63f9b7a09ea5f1cb75d43e4b3cbe08375',
				'client_secret' => $request->getHttpHost() === 'dev-app.getwoohoo.com' ? '2ce13baa170e5e9137f5dcc50506e4eab8f75f9bb643d7c0338bb028dfed9197' : '18b3326d526d5190369c7aebf9ed05dd83746f50dfa35add880192794514274c',
				'redirect_uri' => config('app.url') . 'integrations/jilt',
				'grant_type' => 'authorization_code',
				'code' => $code
			];

			$res = $client->post('https://app.jilt.com/oauth/token', [
				'form_params' => $options
			]);

			$response = \GuzzleHttp\json_decode($res->getBody());

			$accessToken = $response->access_token;
			$refreshToken = $response->refresh_token;

			/** @var Site $site */
			$site = Site::find($siteId);
			$user = $site->user;

			if ($accessToken) {
				$data = [
					'active' => 1,
					'private_key' => $accessToken,
					'refresh_token' => $refreshToken,
				];

				/** @var JiltApp $jilt */
				$jilt = $site->jilt;

				if ($jilt) {
					$jilt->update($data);
				} else {
					$jiltApp = new JiltApp($data);
					$jiltApp->user()->associate($user);
					$jiltApp->site()->associate($site);

					$jiltApp->save();
				}
			}
			return redirect(route('home'));
		} catch (RequestException $requestException) {
			\Log::error($requestException->getMessage());

			return redirect(route('home'));
		} catch (Exception $exception) {
			\Log::error($exception->getMessage());

			return redirect(route('home'));
		}
	}
}
