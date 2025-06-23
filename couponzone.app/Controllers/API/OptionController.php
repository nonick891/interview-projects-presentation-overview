<?php namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OptionController extends Controller
{
	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function showedVote()
	{
		$options = updateOption('vote_modal_showed', 1);
		
		return json(['options' => $options], 200);
    }
	
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update(Request $request)
	{
		$result = '';
		
		$options = $request->all();
		
		foreach ($options as $field => $value)
		{
			$result = updateOption($field, $value);
		}
		
		return json(['options' => $result], 200);
    }
}
