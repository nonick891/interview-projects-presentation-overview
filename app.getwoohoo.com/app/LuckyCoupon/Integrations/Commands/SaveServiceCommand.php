<?php namespace LuckyCoupon\Integrations\Commands;

use \Auth;
use LuckyCoupon\BaseCommand;
use LuckyCoupon\ChatChampApps\ChatChampAppEloquentRepository;
use LuckyCoupon\JiltApps\JiltAppEloquentRepository;
use LuckyCoupon\ActiveCampaignApps\ActiveCampaignAppEloquentRepository;
use LuckyCoupon\KlaviyoApps\KlaviyoAppEloquentRepository;
use LuckyCoupon\OmnisendApps\OmnisendAppEloquentRepository;
use LuckyCoupon\ZapierApps\ZapierAppEloquentRepository;
use LuckyCoupon\CampaignMonitorApps\CampaignMonitorRepository;

/**
 * Class SaveServiceCommand
 * @package LuckyCoupon\Integrations\Commands
 */
class SaveServiceCommand extends BaseCommand
{
	/**
	 * @var KlaviyoAppEloquentRepository
	 */
	private $klaviyoRepo;

	/**
	 * @var ChatChampAppEloquentRepository
	 */
	private $chatchampRepo;

	/**
	 * @var ActiveCampaignAppEloquentRepository
	 */
	private $activecampaignRepo;

	/**
	 * @var OmnisendAppEloquentRepository
	 */
	private $omnisendRepo;

	/**
	 * @var ZapierAppEloquentRepository
	 */
	private $zapierRepo;

	/**
	 * @var JiltAppEloquentRepository
	 */
	private $jiltRepo;

	/**
	 * @var CampaignMonitorRepository
	 */
	private $campaignmonitorRepo;

	/**
	 * @var integer
	 */
	private $userId;

	private $serviceRow;

	/**
	 * SaveServiceCommand constructor.
	 * @param $request
	 */
	public function __construct($request)
	{
		$this->request = $request;

		$this->userId = Auth::user()->id;

		$this->klaviyoRepo = new KlaviyoAppEloquentRepository();

		$this->omnisendRepo = new OmnisendAppEloquentRepository();

		$this->chatchampRepo = new ChatChampAppEloquentRepository();

		$this->activecampaignRepo = new ActiveCampaignAppEloquentRepository();

		$this->zapierRepo = new ZapierAppEloquentRepository();

		$this->jiltRepo = new JiltAppEloquentRepository();

		$this->campaignmonitorRepo = new CampaignMonitorRepository();
	}

	public function handle()
	{
		if ($errs = $this->getErrors($this->request)) return $errs;

		$serviceData = $this->getRequestData();

		$service = $serviceData['service'];

		unset($serviceData['service']);

		if ($this->{$service . 'Repo'}) $this->errorResponse([$service => 'Service object absent.']);

		$result = $this->_saveService($service, $serviceData);

		if (!$result) return $this->errorResponse(['Can\'t add service data.']);

		$serviceData = $this->_setAdditionFields($service, $serviceData);

		return $this->response(['service' => $serviceData]);
	}

	/**
	 * @param $service
	 * @param $serviceData
	 * @return mixed
	 */
	private function _saveService($service, $serviceData)
	{
		$serviceObj = $this->{$service . 'Repo'}->model
			->firstOrNew([
				'site_id' => $serviceData['site_id'],
				'user_id' => $this->userId
			]);

		$this->serviceRow = $this->_updateRow($serviceData, $serviceObj);

		return $this->serviceRow->save();
	}

	/**
	 * @param $serviceData
	 * @param $serviceObj
	 * @return mixed
	 */
	private function _updateRow($serviceData, $serviceObj)
	{
		foreach ($serviceData as $column => $value)
		{
			if (!is_null($value) && in_array($column, $serviceObj->fillable))
			{
				$serviceObj->{$column} = $value;
			}
		}

		return $serviceObj;
	}

	/**
	 * @param $service
	 * @param $serviceData
	 * @return mixed
	 */
	private function _setAdditionFields($service, $serviceData)
	{
		switch ($service)
		{
			case 'chatchamp':

				$serviceData = $this->_getModelFields($service, $serviceData);

				$serviceData['active'] = dispatch(new CheckServiceConnectionCommand($serviceData, $this->serviceRow));

				break;
			case 'omnisend':
				$serviceData = $this->_getModelFields($service, $serviceData);

				$serviceData['lists'] = null;

				break;
			case 'klaviyo':

				$serviceData = $this->_getModelFields($service, $serviceData);

				$lists = $this->_getServiceList($service);

				$serviceData['lists'] = $lists === false ? 'Invalid API key or can\'t reach mail service.' : $lists;

				break;
			case 'activecampaign':
				$serviceData = $this->_getModelFields($service, $serviceData);

				break;
			case 'campaignmonitor':
				$serviceData = $this->_getModelFields($service, $serviceData);

				$lists = $this->_getServiceList($service);

				$serviceData['lists'] = $lists === false ? 'Invalid API key or can\'t reach mail service.' : $lists;

				break;
			default: return $serviceData;
		}

		return $serviceData;
	}

	/**
	 * @param $service
	 * @return mixed
	 */
	private function _getServiceList($service)
	{
		switch ($service)
		{
			case 'klaviyo':
			case 'campaignmonitor':
				return dispatch(new GetServiceListsCommand($this->serviceRow, $service));
			case 'omnisend':
			default: return [];
		}
	}

	/**
	 * @param $service
	 * @param $serviceData
	 * @return array
	 */
	private function _getModelFields($service, $serviceData)
	{
		$result = [];

		$fillable = $this->{$service . 'Repo'}->model->fillable;

		foreach ($fillable as $fieldName)
		{
			if (isset($serviceData[$fieldName]))
			{
				$result[$fieldName] = $serviceData[$fieldName];
			}
		}

		return $result;
	}
}
