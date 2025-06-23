<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\GetProductRequest;
use App\Http\Requests\Statistics\UpdateProductRequest;
use Promotions\Counters\Repository\Eloquent\Counter as CounterRepo;
use Promotions\Events\Repository\EventProductView;
use Promotions\Integrations\BufferCounter;

class WidgetStatisticsController extends Controller
{
	private $counterRepo;
	private $eventProductViewRepo;
	
	/**
	 * WidgetStatisticsController constructor.
	 */
	public function __construct()
	{
		$this->counterRepo = new CounterRepo();
		
		$this->eventProductViewRepo = new EventProductView();
	}
	
	/**
	 * @param GetProductRequest $request
	 *
	 * @return mixed
	 */
	public function getProductViewCount(GetProductRequest $request)
	{
		$productId = $request->get('product_id', false);
		
		return external_json([
			'product_viewed' => $this->counterRepo->getProductViewed($productId)
		], 200);
	}
	
	/**
	 * @param UpdateProductRequest $request
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public function updateProductViewCount(UpdateProductRequest $request)
	{
		try
		{
			$productId = $request->get('product_id', false);
			
			$domain = $request->get('domain', false);
			
			if (!$productId)
			{
				logError('Can\'t find product id ' . $productId);
				
				return external_json('OK', 200);
			}
			
			$id = data_get(findShop($domain), 'id', false);
			
			if ($id)
			{
				BufferCounter::add($id, 'view-:' . $productId);
			}
			
			return external_json('OK', 200);
		}
		catch (\Exception $e)
		{
			logError(' ' . $e->getMessage() . ' ' . $e->getFile() . ' line: ' . $e->getLine());
			
			return external_json('OK', 200);
		}
    }
}
