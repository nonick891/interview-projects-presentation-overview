<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Promotions\Runtime\Commands\GetScriptCommand;

class ScriptController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 *
	 */
	public function getWidget(Request $request)
	{
		$shop = $request->get('shop');
		
		return dispatch_now(new GetScriptCommand($shop));
    }
}
