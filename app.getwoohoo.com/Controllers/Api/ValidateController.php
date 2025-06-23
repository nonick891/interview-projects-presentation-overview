<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ValidateController extends Controller
{
	/**
	 * @param $request
	 * @return bool|array
	 */
	public function _getErrors($request)
	{
		$validator = \Validator::make($request->all(), $request->rules());
		
		if ($validator->fails())
		{
			$errors = $validator->errors();
			
			return $errors;
		}
		
		return false;
	}
	
	/**
	 * @param $request
	 * @return array
	 */
	public function _getRequestKeys($request)
	{
		return array_keys($request->rules());
	}
	
	
}
