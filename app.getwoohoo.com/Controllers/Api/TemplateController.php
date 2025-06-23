<?php namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Response;
use LuckyCoupon\Redis\Classes\Wrappers\SiteIdByUrl;
use LuckyCoupon\Redis\Commands\ResolveRedisData;
use LuckyCoupon\Sites\EloquentSiteRepository;
use LuckyCoupon\Templates\Commands\GenerateRevealPatternSvgCommand;
use LuckyCoupon\Templates\Commands\GenerateScriptCommand;
use LuckyCoupon\Templates\Commands\GenerateSpinTheWheelBackgroundSvgCommand;
use LuckyCoupon\Templates\Commands\GenerateSpinTheWheelSvgCommand;
use LuckyCoupon\Templates\Commands\GenerateSvgCommand;
use LuckyCoupon\Templates\Commands\GenerateFullSvgCommand;
use LuckyCoupon\Templates\Commands\GetDownloadableStyles;
use LuckyCoupon\Templates\Commands\GetGameBackgroundCommand;

/**
 * Class TemplateController
 * @package App\Http\Controllers\Api
 */
class TemplateController extends ValidateController
{
	private $scriptAge = '31536000, immutable';

	/**
	 * @param $id
	 * @return mixed
	 */
	public function getScript($id)
	{
		return $this->_response(dispatch(new GenerateScriptCommand($id, true)))
			->header('Cache-Control', 'max_age=' . $this->scriptAge);
	}

	/**
	 * @return \Illuminate\Http\Response
	 */
	public function getShopifyScript()
	{
		return $this->_response('')
		            ->header('Cache-Control', 'max_age=' . $this->scriptAge);
	}

	/**
	 * @return \Illuminate\Http\Response
	 */
	public function getSpyImage()
	{
		$imageResponse = $this->_response(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='), 'data:image/png')
		                      ->header('Access-Control-Allow-Origin', '*');

		$clientIp = getClientIp();

		if (isSameIp($clientIp))
		{
			return $imageResponse;
		}

		setCounter('image-hit:' . $clientIp);

		return $imageResponse;
	}

	/**
	 * @param $gameId
	 * @return \Illuminate\Http\Response
	 */
	public function getWheel($gameId)
	{
		$svg = getGameSvgFile($gameId, 'wheel', GenerateSvgCommand::class);

		return $this->_response($svg, 'image/svg+xml')
					->header('Access-Control-Allow-Origin', '*');
	}

	/**
	 * @param $gameId
	 * @return \Illuminate\Http\Response
	 */
	public function getFullWheel($gameId)
	{
		$svg = getGameSvgFile($gameId, 'full_wheel', GenerateFullSvgCommand::class);

		return $this->_response($svg, 'image/svg+xml')
			->header('Access-Control-Allow-Origin', '*');
	}

	/**
	 * @param $gameId
	 * @return \Illuminate\Http\Response
	 */
	public function getSpinTheWheel($gameId)
	{
		$svg = getGameSvgFile($gameId, 'spin_the_wheel', GenerateSpinTheWheelSvgCommand::class);

		return $this->_response($svg, 'image/svg+xml')
			->header('Access-Control-Allow-Origin', '*');
	}
	

	/**
	 * @param $gameId
	 * @return \Illuminate\Http\Response
	 */
	public function getSpinTheWheelBackground($gameId)
	{
		$svg = getGameSvgFile($gameId, 'spin_the_wheel_background', GenerateSpinTheWheelBackgroundSvgCommand::class);

		return $this->_response($svg, 'image/svg+xml')
			->header('Access-Control-Allow-Origin', '*');
	}
	
	/**
	 * @param $gameId
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function revealPattern($gameId)
	{
		$svg = getGameSvgFile($gameId, 'reveal_pattern', GenerateRevealPatternSvgCommand::class);

		return $this->_response($svg, 'image/svg+xml')
		            ->header('Access-Control-Allow-Origin', '*');
	}
	
	/**
	 * @param $userId
	 * @param $gameId
	 * @param $fileName
	 * @return \Illuminate\Http\Response
	 */
	public function getBackgroundImage($userId, $gameId, $fileName = null)
	{
		return dispatch(new GetGameBackgroundCommand($userId, $gameId, $fileName));
	}
	
	/**
	 * @param $content
	 * @param string $type
	 * @return \Illuminate\Http\Response
	 */
	private function _response($content, $type = 'text/plain')
	{
		$headers = [
			'Content-type' => $type,
			'Content-Length' => strlen($content)
		];
		
		return Response::make($content, 200, $headers);
	}
	
	/**
	 * @param $type
	 *
	 * @return mixed
	 */
	public function getLoadableStyle($type)
	{
		$styles = dispatch(new GetDownloadableStyles($type));
		
		return $this->_response($styles, 'plain/text')
		            ->header('Access-Control-Allow-Origin', '*');
	}

	/**
	 * @param $shop
	 *
	 * @return mixed
	 */
	private function _getSiteId($shop)
	{
		return dispatch(new ResolveRedisData([
			'key' => $shop,
			'redis_wrapper' => SiteIdByUrl::class,
			'repo' => new EloquentSiteRepository(),
			'method' => 'getIdByUrl'
		]));
	}
}
