<?php namespace LuckyCoupon\Integrations\Commands;

use Illuminate\Http\Request;
use LuckyCoupon\ActiveCampaignApps\ActiveCampaignAppEloquentRepository;
use LuckyCoupon\BaseCommand;
use LuckyCoupon\CampaignMonitorApps\CampaignMonitorRepository;
use LuckyCoupon\JiltApps\JiltAppEloquentRepository;
use LuckyCoupon\KlaviyoApps\KlaviyoAppEloquentRepository;
use LuckyCoupon\MailchimpApps\MailchimpAppEloquentRepository;
use LuckyCoupon\OmnisendApps\OmnisendAppEloquentRepository;
use LuckyCoupon\ZapierApps\ZapierAppEloquentRepository;

/**
 * Class UpdateServiceCommand
 * @package LuckyCoupon\Integrations\Commands
 */
class UpdateServiceCommand extends BaseCommand
{
	/**
	 * @var MailchimpAppEloquentRepository
	 */
    private $mailchimpRepo;

    /**
     * @var KlaviyoAppEloquentRepository
     */
	private $klaviyoRepo;

	/**
     * @var OmnisendAppEloquentRepository
     */
	private $omnisendRepo;

	/**
     * @var ActiveCampaignAppEloquentRepository
     */
    private $activecampaignRepo;

	/**
	 * @var ZapierAppEloquentRepository
	 */
    private $zapierRepo;

	/**
	 * @var JiltAppEloquentRepository
	 */
    private $campaignmonitorRepo;

	/**
	 * @var CampaignMonitorRepository
	 */

    private $jiltRepo;

	/**
	 * UpdateServiceCommand constructor.
	 * @param Request $request
	 */
	public function __construct($request)
	{
		$this->request = $request;

		$this->mailchimpRepo = new MailchimpAppEloquentRepository();

		$this->klaviyoRepo = new KlaviyoAppEloquentRepository();

		$this->omnisendRepo = new OmnisendAppEloquentRepository();

		$this->activecampaignRepo = new ActiveCampaignAppEloquentRepository();

		$this->zapierRepo = new ZapierAppEloquentRepository();

		$this->jiltRepo = new JiltAppEloquentRepository();

		$this->campaignmonitorRepo = new CampaignMonitorRepository();
	}

	public function handle()
	{
		if ($errs = $this->getErrors($this->request)) return $errs;

		$serviceData = $this->getRequestData();

		$userId = \Auth::user()->id;

		$service = strtolower($serviceData['service']);

		$serviceData = $this->_unsetData($serviceData, $service);

		if (!$this->{$service . 'Repo'}) return ['service' => false];

		return [
			'service' => $this->{$service . 'Repo'}->updateByUserId($userId, $serviceData),
			'updated' => array_merge(['service' => $service], $serviceData)
		];
	}

	/**
	 * @param $serviceData
	 * @param $service
	 * @return mixed
	 */
	private function _unsetData($serviceData, $service)
	{
		unset($serviceData['service']);

		if (in_array($service, [
				'mailchimp',
				'klaviyo',
		        'omnisend',
		        'activecampaign',
				'zapier',
				'jilt',
				'campaignmonitor'
			]))
		{
			unset($serviceData['game_id']);
		}

		if ($service == 'zapier' || $service == 'jilt') {
			unset($serviceData['selected_list_id']);
		}

		return $serviceData;
	}
}