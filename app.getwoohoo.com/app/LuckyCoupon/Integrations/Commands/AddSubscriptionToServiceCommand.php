<?php namespace LuckyCoupon\Integrations\Commands;

use Exception;
use LuckyCoupon\Settings\Commands\GetDBSettingsCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddSubscriptionToServiceCommand implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * The number of times the job may be attempted.
	 *
	 * @var int
	 */
	public $tries = 3;

	/**
	 * The number of seconds the job can run before timing out.
	 *
	 * @var int
	 */
	public $timeout = 120;

	/**
	 * @var string
	 */
	private $serviceName;
	
	private $service;
	
	private $email;
	/**
	 * @var string
	 */
	private $gameListId;
	/**
	 * @var bool
	 */
	private $gameId;
	
	private $number;
	
	private $name;
	
	/**
	 * AddMailchimpSubscriptionCommand constructor.
	 *
	 * @param $service
	 * @param $email
	 * @param $serviceName
	 * @param string $gameListId
	 * @param bool $gameId
	 * @param $number
	 * @param $name
	 */
	public function __construct($service, $email, $serviceName, $gameListId = '', $gameId = false, $number, $name)
	{
		$this->service = $service;
		
		$this->email = $email;
		
		$this->number = $number;
		
		$this->name = $name;
		
		$this->gameId = $gameId;
		
		$this->serviceName = $serviceName;
		
		$this->gameListId = $gameListId;
	}
	
	public function handle()
	{
		try
		{
			$serviceListObj = $this->_dispatchList($this->service);

			$response = $this->_addEmail($this->service, $serviceListObj);
			
			dispatch(new AfterEmailSubscriptionCommand($this->serviceName, $this->service, $this->gameId, $response));
		}
		catch (\Exception $exception)
		{
			$this->_logIfError($exception);
		}
	}
	
	/**
	 * @param $service
	 * @return mixed
	 */
	private function _dispatchList($service)
	{
		$listClassName = '\Integration\\' . ucfirst($this->serviceName) . '\API\Lists';
		
		return new $listClassName($this->_getListParams($service));
	}
	
	/**
	 * @param $service
	 * @param $serviceListObj
	 * @return Exception|object
	 */
	private function _addEmail($service, $serviceListObj)
	{
		$listId = $this->gameListId === '' ? $service->selected_list_id : $this->gameListId;

		$response = $serviceListObj->subscribeAddress($listId, $this->_getParams());

		$this->_logIfError($response);
		
		return $response;
	}
	
	/**
	 * @param $service
	 * @return mixed
	 */
	private function _getListParams($service)
	{
		switch ($this->serviceName)
		{
			case 'omnisend': return $service->private_key;
			case 'shopify':
			case 'klaviyo':
			case 'activecampaign':
			case 'mailchimp':
			case 'campaignmonitor':
				return $service;
			default:
				return false;
		}
	}
	
	/**
	 * @return array
	 */
	private function _getParams()
	{
		switch ($this->serviceName)
		{
			case 'klaviyo': return $this->_getKlaviyoParams();
			case 'mailchimp': return $this->_getMailchimpParams();
			case 'shopify': return $this->_getShopifyParams();
			case 'omnisend': return $this->_getEmailParam();
            case 'activecampaign': return $this->_getActiveCampaignParams();
			case 'campaignmonitor': return $this->_getCampaignMonitorParams();
			default: return [];
		}
	}
	
	/**
	 * @return array
	 */
	private function _getShopifyParams()
	{
		$settings = dispatch(new GetDBSettingsCommand($this->gameId));
		
		$tagsCondition = data_get($settings, 'behavior.addSubscribersToShopifyCustomersList.yes');
		
		$tags = data_get($settings, 'behavior.addSubscribersToShopifyCustomersList.input');
		
		$tagsArray = $tagsCondition === '1' ? ['tags' => $tags] : [];

		$name = $this->name ? ['name' => $this->name] : [];
		
		return array_merge(['email' => $this->email], $name, $tagsArray);
	}
	
	/**
	 * @return array
	 */
	private function _getKlaviyoParams()
	{
		$name = explode(' ', $this->name);

		$firstName = data_get($name, '0');
		
		$lastName = data_get($name, '1');
		
		$nameFallback = data_get(explode('@', $this->email), '0');

		$profile = [
			'first_name' => $this->_getNotEmpty($firstName, $nameFallback),
			'last_name' => $this->_isEmpty($firstName) ? '' : $this->_getNotEmpty($lastName, ''),
		];

		$email = $this->email && $this->email !== 'non-collect-option-on'
			? ['email' => $this->email]
			: [];

		$number = $this->number
			? array_merge([
				'phone_number' => str_replace(['(', ' ', '-', ')'], '', $this->number),
				'sms_consent' => true,
			], !$email ? ['$consent' => ['sms']] : [])
			: [];

		$data = ($number || $email)
			? [array_merge($profile, $number, $email)]
			: [];

		return ['profiles' => $data];
	}
	
	/**
	 * @param $value
	 * @param $fallBack
	 *
	 * @return mixed
	 */
	private function _getNotEmpty($value, $fallBack)
	{
		return ($value && $value !== 'null') ? $value : $fallBack;
	}
	
	/**
	 * @param $value
	 *
	 * @return bool
	 */
	private function _isEmpty($value)
	{
		return !$value || $value === 'null';
	}
	
	/**
	 * @return array
	 */
	private function _getMailchimpParams()
	{
		return [
			'email_address' => $this->email,
			'status' => 'subscribed'
		];
	}

    /**
     * @return array
     */
    private function _getActiveCampaignParams()
    {
        return [
            'email_address' => $this->email,
        ];
    }

    /**
     * @return array
     */
    private function _getCampaignMonitorParams() : array
    {
        return [
            'EmailAddress' => $this->email,
			'ConsentToTrack' => 'yes'
        ];
    }
	
	/**
	 * @return mixed
	 */
	private function _getEmailParam()
	{
		return $this->email;
	}
	
	/**
	 * @param Exception|object $response
	 * @return bool
	 */
	private function _logIfError($response)
	{
		if (!$this->_isException($response)) return false;
		
		\Log::error(print_r([
			$response->getMessage(),
			$response->getFile(),
			$response->getLine()
		], true));
		
		return true;
	}
	
	/**
	 * @param $response
	 * @return bool
	 */
	private function _isException($response)
	{
		return is_object($response) && strstr(get_class($response), 'Exception') !== false;
	}
}