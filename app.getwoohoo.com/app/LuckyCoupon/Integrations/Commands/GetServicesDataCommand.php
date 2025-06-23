<?php namespace LuckyCoupon\Integrations\Commands;

use LuckyCoupon\BaseCommand;

/**
 * Class GetServicesDataCommand
 * @package LuckyCoupon\Integrations\Commands
 */
class GetServicesDataCommand extends BaseCommand
{
	private $user;
	
	/**
	 * GetServicesDataCommand constructor.
	 *
	 * @param $user
	 */
	public function __construct($user)
	{
		$this->user = $user;
	}

	public function handle()
	{
		$services = [
			'mail_chimp' => 'mailchimp',
			'klavi_yo' => 'klaviyo',
			'chat_champ' => 'chatchamp',
			'omni_send' => 'omnisend',
			'active_campaign' => 'activecampaign',
			'zapier' => 'zapier',
			'jilt' => 'jilt',
			'campaign_monitor' => 'campaignmonitor'
		];

		return $this->_getServicesData($services);
	}

	/**
	 * @param $services
	 * @return mixed
	 */
	private function _getServicesData($services)
	{
		$result = [];

		foreach ($services as $serviceSnake => $serviceName)
		{
			$result[$serviceSnake] = $this->_getServiceData($serviceName);
		}

		return $result;
	}

	/**
	 * @param $serviceName
	 * @return array
	 */
	private function _getServiceData($serviceName)
	{
		$serviceRows = $this->_getServiceApps($serviceName);

		return $this->_getServicesArray($serviceRows, $serviceName);
	}

	/**
	 * @param $service
	 * @return mixed
	 */
	private function _getServiceApps($service)
	{
		if ( ! $this->user->{$service}) return [];

		$fields = $this->_getFields($service);

		return $this->user->{$service}
			->select(\DB::raw($fields))
			->where('user_id', $this->user->id)
			->get();
	}

	/**
	 * @param string $service
	 * @return string $fields
	 */
	private function _getFields($service)
	{
		$base = 'id, active, user_id, site_id';

		$base .= $this->_getStringFields($service);

		return $base;
	}

	/**
	 * @param $service
	 * @return string
	 */
	private function _getStringFields($service)
	{
		return ', ' . implode(', ', $this->_getAdditionalFields($service));
	}

	/**
	 * @param $service
	 * @param string $withoutValue
	 * @return array
	 */
	private function _getAdditionalFields($service, $withoutValue = '')
	{
		switch ($service)
		{
			case 'mailchimp': $result = ['dc', 'accountname', 'access_token', 'api_endpoint', 'selected_list_id']; break;

			case 'omnisend': $result = ['private_key', 'selected_list_id']; break;

			case 'klaviyo': $result = ['private_key', 'public_key', 'selected_list_id']; break;

			case 'chatchamp': $result = ['game_id', 'api_key']; break;

			case 'activecampaign':$result = ['private_key', 'api_url', 'selected_list_id']; break;

			case 'jilt': case 'zapier': $result = ['private_key']; break;

			case 'campaignmonitor': $result = ['api_key', 'client_id', 'selected_list_id']; break;

			default: $result = []; break;
		}

		if ($deleteKey = array_search($withoutValue, $result))
		{
			unset($result[$deleteKey]);
		}

		return $result;
	}

	/**
	 * @param $serviceRows
	 * @param $serviceName
	 * @return array
	 */
	private function _getServicesArray($serviceRows, $serviceName)
	{
		$result = [];

		foreach ($serviceRows as $serviceRow)
		{
			$result[$serviceRow->site_id] = $this->_getServiceArray($serviceName, $serviceRow);
		}

		return $result;
	}

	/**
	 * @param $serviceName
	 * @param $serviceRow
	 * @return mixed
	 */
	private function _getServiceArray($serviceName, $serviceRow)
	{
		$serviceArray = $serviceRow->toArray();

		if (($serviceName !== 'chatchamp') && ($serviceName !== 'zapier'))
		{
			$serviceArray = $this->_removeAdditionalFields($serviceName, $serviceArray);
		}

		return $this->_getSpecificFields($serviceName, $serviceRow, $serviceArray);
	}

	/**
	 * @param $serviceName
	 * @param $serviceArray
	 * @return mixed
	 */
	private function _removeAdditionalFields($serviceName, $serviceArray)
	{
		$fields = $this->_getAdditionalFields($serviceName, 'selected_list_id');

		foreach ($fields as $field)
		{
			unset($serviceArray[$field]);
		}

		return $serviceArray;
	}

	/**
	 * @param $serviceName
	 * @param $serviceRow
	 * @param $serviceArray
	 * @return mixed
	 */
	private function _getSpecificFields($serviceName, $serviceRow, $serviceArray)
	{
		switch ($serviceName)
		{
			case 'activecampaign':
			case 'omnisend':
			case 'mailchimp':
			case 'klaviyo':
			case 'campaignmonitor':
				$serviceArray['lists'] = dispatch(new GetServiceListsCommand($serviceRow, $serviceName));
				break;
			case 'chatchamp':
				$serviceArray['active'] = dispatch(new CheckServiceConnectionCommand($serviceArray, $serviceRow));
				break;
			case 'zapier':
			case 'jilt':
			default: $serviceArray['lists'] = [];
		}

		return $serviceArray;
	}
}
