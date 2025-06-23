<?php namespace App\Http\Controllers;

class WebhooksController extends Controller
{
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function customerDataRequest()
	{
		return response()->json(['OK']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function customerRedact()
	{
		return response()->json(['OK']);
	}
	
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function shopRedact()
	{
		return response()->json(['OK']);
	}
}
