<?php namespace App\Http\Controllers\API\Pub;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\ApplyCodeRequest;
use App\Http\Requests\Events\CopyCodeRequest;
use App\Http\Requests\Events\DownloadWidgetRequest;
use App\Http\Requests\Events\GrabCodeRequest;
use App\Http\Requests\Events\OpenWidgetRequest;
use CouponZone\Events\Commands\ApplyCode;
use CouponZone\Events\Commands\CopyCode;
use CouponZone\Events\Commands\DownloadWidget;
use CouponZone\Events\Commands\GrabCode;
use CouponZone\Events\Commands\OpenWidget;

class EventsController extends Controller
{
	private $corsHeader;
	
	/**
	 * EventsController constructor.
	 */
	public function __construct()
	{
		$this->corsHeader = ['Access-Control-Allow-Origin' => '*'];
	}
	
	
	/**
	 * @param OpenWidgetRequest $request
	 *
	 * @return mixed
	 */
	public function openWidget(OpenWidgetRequest $request)
	{
		dispatch_now(new OpenWidget($request->validated()));
		
		return json(true, 200, null, $this->corsHeader);
    }
	
	/**
	 * @param DownloadWidgetRequest $request
	 *
	 * @return mixed
	 */
	public function downloadWidget(DownloadWidgetRequest $request)
	{
		dispatch_now(new DownloadWidget($request->validated()));
		
		return json(true, 200, null, $this->corsHeader);
    }
	
	/**
	 * @param GrabCodeRequest $request
	 *
	 * @return mixed
	 */
	public function grabCode(GrabCodeRequest $request)
	{
		dispatch_now(new GrabCode($request->validated()));
		
		return json(true, 200, null, $this->corsHeader);
    }
	
	/**
	 * @param ApplyCodeRequest $request
	 *
	 * @return mixed
	 */
	public function applyCode(ApplyCodeRequest $request)
	{
		dispatch_now(new ApplyCode($request->validated()));
		
		return json(true, 200, null, $this->corsHeader);
    }
	
	/**
	 * @param CopyCodeRequest $request
	 *
	 * @return mixed
	 */
	public function copyCode(CopyCodeRequest $request)
	{
		dispatch_now(new CopyCode($request->validated()));
		
		return json(true, 200, null, $this->corsHeader);
    }
}
