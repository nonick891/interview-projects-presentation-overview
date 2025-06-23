<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Promotions\Shopify\Products\Repository as ProductsRepo;

class ProductsController extends Controller
{
	public function get(Request $request)
	{
		$filterTitle = $request->get('query', null);
		
		$products = (new ProductsRepo())->get($filterTitle);
		
		return success_json($products);
    }
}
