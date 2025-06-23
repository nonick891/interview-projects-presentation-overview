<?php

namespace App\Http\Controllers\API;

use CouponZone\Script\Commands\AppendScriptCommand;
use CouponZone\Shops\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use CouponZone\Sections\Repository\Prepare as PrepareSection;
use CouponZone\Sections\Repository\Eloquent\Section as SectionRepo;
use CouponZone\Settings\Repository\Eloquent\Setting as SettingRepo;
use Illuminate\Support\Facades\Storage;

class StartedDataController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
	    $shop = getShop();
    	
	    dispatch((new AppendScriptCommand($shop->id, true))->onQueue('high'));
	
	    $data = [
		    'shop' => $shop,
		    'sections' => $this->_getSections($shop),
		    'plain_sections' => $this->_getPlainSections($shop),
		    'settings' => $this->_getSettings($shop),
		    'options' => $this->_getOptions($shop),
		    'coupons' => $shop->coupons,
		    'counters' => $this->_getCounters($shop),
		    'user' => data_get($shop, 'user.id', false),
	        'image_hosting_url' => Storage::disk()->url('')
	    ];
	
	    if ($shop->fresh === 1)
	    {
		    Shop::where('id', $shop->id)->update(['fresh' => 0]);
	    }
	    
	    return json($data);
    }
	
	/**
	 * @param $shop
	 *
	 * @return array|false
	 */
	private function _getSections($shop)
	{
		$sections = $shop->sections;
		
		if ($shop->fresh === 1)
		{
			$sections = (new SectionRepo())->saveByShopId($shop->id);
		}
		
		return (new PrepareSection('shopId', $shop->id))
			->getSectionsStructure($sections);
	}
	
	/**
	 * @param $shop
	 *
	 * @return array
	 */
	private function _getPlainSections($shop)
	{
		return $shop->plainSections;
	}
	
	/**
	 * @param $shop
	 *
	 * @return mixed
	 */
	private function _getSettings($shop)
	{
		$settings = $shop->settings;
		
		if ($shop->fresh === 1)
		{
			$settings = (new SettingRepo())->saveByShopId($shop->id);
		}
		
		return [
			'id' => data_get($settings, 'id', false),
			'values' => json_decode(data_get($settings, 'content', ''))
		];
	}
	
	/**
	 * @param $shop
	 *
	 * @return mixed
	 */
	public function _getOptions($shop)
	{
		$options = $shop->options;
		
		if (!$options)
		{
			$options = updateOption('vote_modal_showed', 0);
		}
		
		return $options;
	}
	
	/**
	 * @param $shop
	 *
	 * @return mixed
	 */
	private function _getCounters($shop)
	{
		$result = [];
		
		foreach ($shop->counters as $counter)
		{
			$result[$counter->name] = $counter->day + $counter->week + $counter->month;
		}
		
		return $result;
	}
}
