<?php namespace App\Http\Controllers;

class EntryPoint extends Controller
{
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$shopifyAppId = data_get(\Auth::user(), 'app_id', false);
		
		return view('layouts.app', ['shopify_app_id' => $shopifyAppId]);
    }
}
