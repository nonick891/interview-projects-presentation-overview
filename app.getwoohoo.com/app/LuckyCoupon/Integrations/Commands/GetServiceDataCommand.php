<?php namespace LuckyCoupon\Integrations\Commands;

use Illuminate\Http\Request;
use LuckyCoupon\BaseCommand;

/**
 * Class GetServiceDataCommand
 * @package LuckyCoupon\Integrations\Commands
 */
class GetServiceDataCommand extends BaseCommand
{
	
	/**
	 * GetIntegrationServiceDataCommand constructor.
	 * @param Request $request
	 */
	public function __construct($request)
	{
		$this->request = $request;
	}
	
	public function handle()
	{
		if ($errs = $this->getErrors($this->request)) return $errs;
		
		$serviceData = $this->getRequestData();
		
		$user = \Auth::user();
		
		$result = $this->_getServiceData($serviceData, $user);
		
		return ['service' => $result];
	}
	
	/**
	 * @param $serviceData
	 * @param $user
	 * @return array|mixed
	 */
	private function _getServiceData($serviceData, $user)
	{
		$result = [];
		
		$service = $serviceData['service'];
		
		$siteId = $serviceData['site_id'];
		
		if ( ! $user->{$service}) return $result;
		
		$serviceRow = $this->_getServiceObj(
			$user->{$service}, $user->id,
			$siteId, true
		);
		
		$result = $this->_handleServiceData($serviceRow, $user->id, $siteId);
		
		$result = $this->_getSpecificFields($service, $serviceRow, $result);
		
		return $result;
	}
	
	/**
	 * @param $service
	 * @param $userId
	 * @param $siteId
	 * @return mixed
	 */
	private function _handleServiceData($service, $userId, $siteId)
	{
		$result = $this->_getServiceObj($service, $userId, $siteId);
		
		return $result ? $result->toArray() : [];
	}
	
	/**
	 * @param $service
	 * @param $userId
	 * @param $siteId
	 * @param bool $allColumns
	 * @return bool
	 */
	private function _getServiceObj($service, $userId, $siteId, $allColumns = false)
	{
		if (gettype($service) !== 'object') return false;
		
		$columns = ($allColumns ? ' * ' : $this->_getSelectField($service));
		
		$result = $service
			->select(\DB::raw($columns))
			->where('user_id', $userId)
			->where('site_id', $siteId)
			->first();
		
		return $result ? $result : false;
	}
	
	/**
	 * @param $service
	 * @return string
	 */
	private function _getSelectField($service)
	{
		if ( ! $service) return '*';
		
		$columns = 'active, user_id, site_id';
		
		if (strstr(get_class($service), 'KlaviyoApp'))
		{
			$columns = $columns . ', selected_list_id';
		}
		else if (strstr(get_class($service), 'MailchimpApp'))
		{
			$columns = $columns . ', accountname, selected_list_id';
		}
		else if (strstr(get_class($service), 'ChatChampApp'))
		{
			$columns = $columns . ', api_key';
		}
		
		return $columns;
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
			case 'mailchimp':
			case 'klaviyo':
				$serviceArray['lists'] = dispatch(new GetServiceListsCommand($serviceRow, $serviceName));
				break;
			case 'chatchamp':
				$serviceArray['active'] = dispatch(new CheckServiceConnectionCommand($serviceArray, $serviceRow));
				break;
		}
		
		return $serviceArray;
	}
	
}