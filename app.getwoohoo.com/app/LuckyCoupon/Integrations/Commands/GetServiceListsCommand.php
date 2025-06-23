<?php namespace LuckyCoupon\Integrations\Commands;

use LuckyCoupon\ActiveCampaignApps\ActiveCampaignApp;
use LuckyCoupon\CampaignMonitorApps\CampaignMonitorApp;
use LuckyCoupon\KlaviyoApps\KlaviyoApp;

/**
 * Class GetServiceListsCommand
 * @package LuckyCoupon\Integrations\Commands
 */
class GetServiceListsCommand
{
	private $serviceRow;
	
	private $serviceName;
	
	/**
	 * GetIntegrationServiceListsCommand constructor.
	 * @param $serviceRow
	 * @param $serviceName
	 */
	public function __construct($serviceRow, $serviceName)
	{
		$this->serviceRow = $serviceRow;

		$this->serviceName = $serviceName;
	}
	
	/**
	 * @return array
	 */
	public function handle()
	{
		if (!$this->serviceRow) return [];
		
		$listsObj = $this->_getListsObject();
		
		return $this->_getListsArray($this->_getReturnedData($listsObj));
	}
	
	/**
	 * @return mixed
	 */
	private function _getListsObject()
	{
		$className = $this->_getListClassName();

		return new $className($this->_getListClassParam($this->serviceRow));
	}
	
	/**
	 * @return string
	 */
	private function _getListClassName()
	{
		return '\Integration\\' . ucfirst($this->serviceName) . '\API\Lists';
	}
	
	/**
	 * @param $serviceRow
	 * @return mixed
	 */
	private function _getListClassParam($serviceRow)
	{
		$className = get_class($serviceRow);

		switch ($className)
		{
			case ActiveCampaignApp::class:
			case CampaignMonitorApp::class:
			case KlaviyoApp::class:
				return $serviceRow;
		}

		return $serviceRow->private_key ?? $serviceRow;
	}
	
	/**
	 * @param $listsObj
	 * @return mixed
	 */
	private function _getReturnedData($listsObj)
	{
		$requestedLists = $listsObj->get();
		
		return $requestedLists && get_class($requestedLists) !== 'stdClass'
			? []
			: ($requestedLists
				? ($requestedLists->lists ?? data_get($requestedLists, 'data', false))
				: false);
	}
	
	/**
	 * @param $lists
	 * @return array|bool
	 */
	private function _getListsArray($lists)
	{
		if ($lists === false) return false;
		
		$result = [];
		
		foreach ($lists as $list)
		{
			if (isset($list->list_type) && $list->list_type !== 'list') continue;

			$nameKey = $this->serviceName === 'campaignmonitor' ? 'Name' : 'name';

			$name = data_get($list, $nameKey, false);

			if (!$name && $this->serviceName === 'klaviyo')
			{
				$name = data_get($list, 'attributes.name');
			}

			$result[] = [
				'id' => data_get($list, $this->_getIdFieldName(), false),
				'name' => $name
			];
		}
		
		return $result;
	}
	
	public function _getIdFieldName()
	{
		switch ($this->serviceName)
		{
			case 'omnisend': return 'listID';
			case 'campaignmonitor': return 'ListID';
			case 'klaviyo':
			default: return 'id';
		}
	}
}