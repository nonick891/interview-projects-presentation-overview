<?php

namespace App\Http\Controllers\API;

use CouponZone\Script\Commands\AppendScriptCommand;
use CouponZone\Sections\Repository\Prepare;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use CouponZone\Sections\Section as SectionModel;
use CouponZone\Sections\Repository\Eloquent\Section as SectionRepo;
use CouponZone\Settings\Repository\Eloquent\Setting as SettingRepo;

class SectionController extends Controller
{
	/**
	 * @var SectionRepo
	 */
	private $sectionRepo;
	
	private $settingRepo;
	
	/**
	 * ZoneController constructor.
	 */
	public function __construct()
	{
		$this->sectionRepo = new SectionRepo();
		
		$this->settingRepo = new SettingRepo();
	}
	
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update(Request $request)
	{
		$regenerate = true;
		
		$shop = getShop();
		
		dispatch((new AppendScriptCommand($shop->id, $regenerate))->onQueue('high'));
		
		$settings = json_decode($request->get('settings'));
		
		$sections = json_decode($request->get('sections'));
		
		$steps = $request->get('steps', '');
		
		$sectionImages = $request->file('sectionImages', []);
		
		$images = $this->_getImages($sectionImages, $shop);
		
		$updatedSections = $this->sectionRepo->updateByShopId($shop->id, $sections, $images);
		
		$this->settingRepo->updateByShopId($shop->id, $settings);
		
		updateOption('steps', $steps);
		
		return json($updatedSections, 200, 'Sections saved.');
	}
	
	/**
	 * @param $sectionImages
	 * @param $shop
	 *
	 * @return array
	 */
	private function _getImages($sectionImages, $shop)
	{
		$images = [];
		
		foreach ($sectionImages as $image)
		{
			$name = $image->getClientOriginalName();
			
			$images[$name] = saveSectionImage($shop->id, $name, $image);
		}
		
		return $images;
	}
	
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function add(Request $request)
	{
		$shop = getShop();
		
		$shopId = $shop->id;
		
		$data = $request->all();
		
		$image = $request->file('image');
		
		$subSection = json_decode($data['sub_section']);
		
		$response = $this->sectionRepo->insertWith($image, $shopId, $subSection);
		
		return json($response, 200);
	}
	
	/**
	 * @param SectionModel $section
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroy(SectionModel $section)
	{
		$shop = getShop();
		
		$delete = $shop->id === $section->shop_id
			? $section->delete()
			: false;
		
		return json($delete, 200, 'Section deleted');
	}
}
