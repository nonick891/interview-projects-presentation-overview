<?php

namespace App\Http\Controllers;

use CouponZone\Sections\Repository\Prepare;
use CouponZone\Shops\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScriptController extends Controller
{
	private $shop;
	
	private $settings;
	
	private $request;
	
	/**
	 * Handle the incoming request.
	 *
	 * @param Shop $shop
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\Response
	 * @internal param Request $request
	 */
    public function __invoke(Shop $shop, Request $request)
	{
		$this->shop = $shop;
		
		$this->request = $request;
		
		$content = $this->_isShowCondition()
			? $this->_getContent()
			: '';
		
		return response($content, 200, $this->_getHeaders($content));
	}
	
	/**
	 * @return bool
	 */
	private function _isShowCondition()
	{
		return (
			$this->_isConditionsPassed()
			&& !$this->_isDeleted()
	       )
	       || $this->_isEditor();
	}
	
	/**
	 * @return mixed
	 */
	private function _isConditionsPassed()
	{
		$this->settings = $settings = $this->shop->settings->toArray();
		
		$content = json_decode(data_get($settings, 'content', ''));
		
		return data_get($content, '0.2.slide-right-content.radioGroup.0.value', false);
	}
	
	/**
	 * @return bool
	 */
	private function _isDeleted()
	{
		return !is_null($this->shop->deleted_at);
	}
	
	/**
	 * @return string
	 */
	private function _getContent()
	{
		return "!function({$this->_getVariables()}) {
		{$this->_getPreProcessLogic()}
		}({$this->_getParameters()})";
	}
	
	/**
	 * @return bool|string
	 */
	public function _getPreProcessLogic()
	{
		return file_get_contents(app_path('/CouponZone/Script/data-preprocessor-logic.js'));
	}
	
	/**
	 * @return string
	 */
	private function _getVariables()
	{
		return 'config, data, settings, styles, templates';
	}
	
	/**
	 * @return string
	 */
	private function _getParameters()
	{
		return "'{$this->_getConfig()}', '{$this->_getData()}', '{$this->_getSettings()}', '{$this->_getStyles()}', '{$this->_getTemplates()}'";
	}
	
	/**
	 * @return string
	 */
	private function _getConfig()
	{
		return dbEncode([
			'url' => config('app.url'),
			'image_hosting_url' => Storage::disk()->url(''),
			'isDev' => $this->_isDev(),
			'runtime' => $this->_getRuntimeUrl(),
			'hostname' => data_get(explode('/', config('app.url')), '2'),
		    'isEditor' => $this->_isEditor()
		]);
	}
	
	/**
	 * @return string
	 */
	public function _getRuntimeUrl()
	{
		$postfix = $this->_isDev() ? '.dev' : '';
		
		$version = $this->_getRuntimeVersion();
		
		return config('app.url') . "/js/widget/runtime{$postfix}.js?v{$version}";
	}
	
	/**
	 * @return mixed
	 */
	private function _isDev()
	{
		return \App::environment(['local', 'development']);
	}
	
	/**
	 * @return bool|string
	 */
	private function _getRuntimeVersion()
	{
		return file_get_contents(base_path('/runtime/.version'));
	}
	
	/**
	 * @return string
	 */
	private function _getData()
	{
		$sections = $this->shop->sections->toArray();
		
		return dbEncode([
			'sections' => (new Prepare())->getPreparedSections($sections),
		    'coupons' => $this->_makeStructureCoupons(),
		    'shopId' => $this->shop->id
		]);
	}
	
	/**
	 * @return array
	 */
	private function _makeStructureCoupons()
	{
		$coupons = $this->shop->publicCoupons->toArray();
		
		$structured = [];
		
		foreach ($coupons as $coupon)
		{
			$structured[$coupon['section_id']][$coupon['id']] = $coupon;
		}
		
		return $structured;
	}
	
	/**
	 * @return string
	 */
	private function _getSettings()
	{
		return dbEncode(json_decode($this->settings['content']));
	}
	
	/**
	 * @return string
	 */
	private function _getStyles()
	{
		$variables = $this->_getCssVariables();
		
		return dbEncode([
			'container' => file_get_contents(public_path('/js/widget/css/container.css')),
			'trigger' => $variables . file_get_contents(public_path('/js/widget/css/trigger.css')),
			'widget' => $variables . file_get_contents(public_path('/js/widget/css/widget.css'))
		]);
	}
	
	/**
	 * @return string
	 */
	private function _getCssVariables()
	{
		$headerColor = $this->_getColor('header');
		
		$triggerColor = $this->_getColor('launcher');
		
		$headerTextColor = $this->_getColor('header text');
		
		$variables = ":root{
			--header-background-color: {$headerColor};
			--launcher-background-color: {$triggerColor};
			--header-text-color: {$headerTextColor};
		};";
		
		return $variables;
	}
	
	/**
	 * @param $colorName
	 *
	 * @return string
	 */
	private function _getColor($colorName)
	{
		$colorName = strtolower($colorName);
		
		$settings = json_decode($this->shop->settings->toArray()['content']);
		
		$colors = data_get($settings, '0.0.slide-right-content.colorPicker', false);
		
		foreach ($colors as $color)
		{
			if (strtolower($color->name) === $colorName)
			{
				return $color->color;
			}
		}
		
		return '#0f0f0f';
	}
	
	/**
	 * @param $index
	 *
	 * @return string
	 */
	private function _getColorPath($index)
	{
		return '0.0.slide-right-content.colorPicker.' . $index . '.color';
	}
	
	/**
	 * @return string
	 */
	private function _getTemplates()
	{
		return dbEncode([
			'containers' => [
				'main' => runtimeTemplate('containers/main.html'),
				'trigger' => runtimeTemplate('containers/trigger.html'),
				'widget' => runtimeTemplate('containers/widget.html')
			],
			'content' => [
				'home' => runtimeTemplate('content/screen/home.html'),
				'page' => runtimeTemplate('content/screen/page.html'),
				'coupon' => runtimeTemplate('content/screen/coupon.html')
			],
			'chunks' => [
				'become-member' => runtimeTemplate('content/chunks/become-member.html'),
				'section' => runtimeTemplate('content/chunks/section.html'),
				'section-button' => runtimeTemplate('content/chunks/section-button.html'),
				'promotion-button' => runtimeTemplate('content/chunks/promotion-button.html'),
			    'back-button' => runtimeTemplate('content/chunks/back-button.html')
			],
			'theme' => [
				'style' => 'light',
				'font' => [
					'banner' => 'dark',
					'header' => 'dark',
					'buttons' => 'light',
					'launcher' => 'light'
				]
			],
			'trigger' => runtimeTemplate('trigger.html'),
			'widget' => runtimeTemplate('widget.html')
		]);
	}
	
	/**
	 * @param $content
	 *
	 * @return array
	 */
	private function _getHeaders($content)
	{
		return [
			'Content-type' => 'text/javascript',
			'Content-Length' => strlen($content)
		];
	}
	
	/**
	 * @return bool
	 */
	private function _isEditor()
	{
		return $this->request->get('type', false) === 'editor';
	}
}
