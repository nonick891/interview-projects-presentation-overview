<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Promotions\Campaigns\Eloquent\Repository;

class FilesManagerController extends Controller
{
	private $campaignRepo;
	
	/**
	 * FilesManagerController constructor.
	 */
	public function __construct()
	{
		$this->campaignRepo = new Repository();
	}
	
	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function uploadIcon(Request $request)
	{
		$shop = getShop();
		
		if (!$request->hasFile('file')) return not_accepted_json('File error.');
		
		$shopId = $shop->id;
		
		$file = $request->file('file');
		
		$promotionId = (int)$request->get('promotion_id', false);
		
		$campaignId = $promotionId
			? $promotionId
			: $this->campaignRepo->getLastId($shopId);
		
		$filePath = getImageName($file, getIconsPath($shopId, $campaignId));
		
		\Storage::put('public' . $filePath, resizeIcon($file), 'public');
		
		return success_json(['icon' => '/storage' . $filePath]);
    }
	
	/**
	 * @param $id
	 * @param $fileName
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deleteIcon($id, $fileName)
	{
		$shop = getShop();
		
		if (!$this->campaignRepo->hasCampaign($shop->id, $id)) return not_accepted_json('Not accepted');
		
		$folder = getIconsPublicPath($shop->id, $id);
		
		$deleteFile = $folder . $fileName;
		
		$isDeleted = \Storage::delete($deleteFile);
		
		if ($isDeleted) deleteFolder($folder);
		
		return success_json($isDeleted ? 'OK' : 'ERROR');
    }
}
