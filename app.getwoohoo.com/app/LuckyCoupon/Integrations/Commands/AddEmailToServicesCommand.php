<?php namespace LuckyCoupon\Integrations\Commands;

use Illuminate\Database\Eloquent\Collection;
use LuckyCoupon\BaseCommand;
use LuckyCoupon\Games\EloquentGameRepository;
use LuckyCoupon\Settings\Commands\GetDBSettingsCommand;
use LuckyCoupon\Users\UserEloquentRepository;

class AddEmailToServicesCommand extends BaseCommand
{
	/**
	 * @var
	 */
	private $gameId;
	
	/**
	 * @var
	 */
	private $email;
	
	private $user;
	
	private $game;
	
	private $gameSettings;
	
	private $name;
	
	private $number;
	
	/**
	 * ShopifyIntegration constructor.
	 *
	 * @param $gameId
	 * @param $email
	 * @param $number
	 * @param $name
	 */
	public function __construct($gameId, $email, $number, $name)
	{
		$this->gameId = $gameId;
		
		$this->email = $email;
		
		$this->number = $number;
		
		$this->name = $name;
	}
	
	public function handle(
		EloquentGameRepository $gameRepo,
		UserEloquentRepository $userRepo
	)
	{
		if (!$this->email) return false;

		$this->game = $gameRepo->getById($this->gameId);
		
		$this->gameSettings = dispatch(new GetDBSettingsCommand($this->gameId));
		
		$userId = data_get($this->game, 'user_id', false);
		
		if (!$userId) return false;
		
		$this->user = $userRepo->getFullUser($userId);
		
		try
		{
			$servicesArray = [
				'mailchimp',
				'klaviyo',
				'omnisend',
				'activecampaign',
				'shopify',
				'campaignmonitor'
			];
			
			$this->_dispatchJobs($servicesArray);
		}
		catch (\Exception $e)
		{
			$this->_logError($e);
		}
	}
	
	/**
	 * @param $servicesArray
	 */
	private function _dispatchJobs($servicesArray)
	{
		foreach ($servicesArray as $serviceName)
		{
			if (!$this->_isServiceAvailable($serviceName)) continue;

			$serviceObj = $this->_getServiceObj($serviceName);

			if (!$this->_isPermitted($serviceObj, $serviceName)) continue;
			
			$this->_dispatchJob($serviceObj, $serviceName);
		}
	}
	
	/**
	 * @param $serviceName
	 * @return bool
	 */
	private function _isServiceAvailable($serviceName)
	{
		if (
			$serviceName !== 'klaviyo'
			&& $this->email === 'non-collect-option-on'
			|| ($serviceName === 'klaviyo' && !$this->email && !$this->number)
		)
		{
			return false;
		}

		switch ($serviceName)
		{
			case 'shopify': return $this->_isShopifyServiceAvailable();
			default: return isset($this->user->{$serviceName});
		}
	}
	
	/**
	 * @return bool
	 */
	private function _isShopifyServiceAvailable()
	{
		return data_get($this->gameSettings, 'behavior.addSubscribersToShopifyCustomersList.yes', '0') === '1';
	}
	
	/**
	 * @param $serviceName
	 * @return bool
	 */
	private function _getServiceObj($serviceName)
	{
		if ($serviceName === 'shopify')
		{
			return $this->user->apps;
		}
		else
		{
			return $this->_getRegularService($serviceName);
		}
	}
	
	/**
	 * @param $serviceName
	 * @return Collection|bool
	 */
	private function _getRegularService($serviceName)
	{
		$serviceObj = $this->user->{$serviceName};
		
		$service = $serviceObj
			->where('user_id', $this->user->id)
			->where('site_id', $this->game->site_id)
			->first();
		
		return $service ? : false;
	}
	
	/**
	 * @param $service
	 * @param $serviceName
	 * @return bool
	 */
	private function _isPermitted($service, $serviceName)
	{
		$active = data_get($service, 'active', false);
		
		switch ($serviceName)
		{
			case 'omnisend':
				return $service && $active;
			case 'shopify':
				return (bool)data_get($service, 'access_token', false); // add option check for shopify settings
			default:
				return $service && $active
				       && (data_get($service, 'selected_list_id', false) || $this->_getGameListId($serviceName));
		}
	}
	
	/**
	 * @param $service
	 * @param $serviceName
	 */
	private function _dispatchJob($service, $serviceName)
	{
		$job = (new AddSubscriptionToServiceCommand(
			$service,
			$this->email,
			$serviceName,
			$this->_getGameListId($serviceName),
			$this->gameId,
			$this->number,
			$this->name
		))->onQueue($serviceName);
		
		dispatch($job);
	}
	
	/**
	 * @param $serviceName
	 * @return mixed
	 */
	private function _getGameListId($serviceName)
	{
		return data_get($this->gameSettings, 'behavior.mailingLists.' . $serviceName, '');
	}
	
	/**
	 * @param \Exception $e
	 */
	private function _logError($e)
	{
		\Log::error(print_r(
			[$e->getMessage(),
			$e->getFile(),
			$e->getLine()],
			true));
	}
}