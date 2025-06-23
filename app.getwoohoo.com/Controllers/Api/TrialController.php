<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Http\RedirectResponse;

class TrialController extends Controller
{
    /**
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function extraTrial(Request $request)
    {
        $minutes = 60;
        $url = 'https://apps.shopify.com/woohoo';
        return redirect($url)->withCookie(cookie('extraTrial', '30', $minutes));
    }
}
