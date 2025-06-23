<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
	    $url = $request->get('url');
	    
	    $error = $request->get('error');
	    
	    \Log::error('Front-end error:' . PHP_EOL . 'URL: ' . $url . PHP_EOL . 'Error stack: ' . PHP_EOL . $error);
    }
}
