<?php

namespace App\Http\Controllers;

class EditorController extends Controller
{
	/**
	 * Handle the incoming request.
	 *
	 * @param $id
	 *
	 * @return \Illuminate\Http\Response
	 *
	 */
    public function __invoke($id)
    {
    	$url = str_replace(['https:', '/'], '', config('app.url'));
    	
	    return response()
	        ->view('editor', ['shopId' => $id], 200)
	        ->header('X-Frame-Options', 'ALLOW-FROM ' . config('app.url'))
	        ->header('Content-Security-Policy', 'frame-ancestors ' . $url);
    }
}
