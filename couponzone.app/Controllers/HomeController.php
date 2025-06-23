<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;

class HomeController extends Controller {

    public function index()
    {
    	$shop = ShopifyApp::shop();
    	
    	if (data_get($shop, 'id', false) && !data_get($shop, 'plan_id', false))
    	{
            return Redirect::route('billing');
	    }
	    
	    return view('welcome');
    }
}
