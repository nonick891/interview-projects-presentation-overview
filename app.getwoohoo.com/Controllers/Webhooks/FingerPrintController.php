<?php namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LuckyCoupon\Visitors\Visitor;

class FingerPrintController extends Controller
{
	public function visit(Request $request)
	{
		$data = $request->all();
		
		$visitorId = data_get($data, 'visitorId');
		
		$requestId = data_get($data, 'requestId');
		
		$query = parse_url(data_get($data, 'url'), PHP_URL_QUERY);
		
		$arrayQuery = [];
		
		parse_str($query, $arrayQuery);
		
		$shop = data_get($arrayQuery, 'shop', '');
		
		$shopName = $shop ? $shop : data_get($arrayQuery, 'shop_url', '');
		
		Visitor::create([
			'visitor_id' => $visitorId,
			'request_id' => $requestId,
			'shop_name' => $shopName,
			'payload' => json_encode($data)
		]);
		
		return '200 OK';
    }
}
