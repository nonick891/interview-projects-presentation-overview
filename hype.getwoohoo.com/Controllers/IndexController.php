<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		return view('spa');
	}
}
