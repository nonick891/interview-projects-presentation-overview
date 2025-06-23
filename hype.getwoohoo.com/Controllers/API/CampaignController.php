<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use Promotions\Campaigns\Campaign;
use Promotions\Runtime\Commands\InstallScriptTag;
use Promotions\Runtime\Commands\SaveScriptCommand;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return json([]);
    }
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Promotions\Campaigns\Campaign $campaign
	 * @param UpdateCampaignRequest $request
	 *
	 * @return \Illuminate\Http\Response
	 */
    public function update(Campaign $campaign, UpdateCampaignRequest $request)
    {
	    $data = $request->all();
	
	    $shopId = getShop()->id;
	    
	    data_set($data, 'shop_id', $shopId);
	
	    if (!$resultText = data_get($data, 'message', false))
	    {
		    $resultText = 'Campaign saved';
	    }
	    
	    unset($data->message);
	    
	    $campaign->fill($data);
	
	    if ($campaign->save())
	    {
		    dispatch(new InstallScriptTag(getShop()));
		
		    dispatch_now(new SaveScriptCommand($shopId));
	    }
	
	    return json(['campaign' => $campaign], 200, $resultText);
    }
	
	/**
	 * @param Campaign $campaign
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete(Campaign $campaign)
	{
		$id = false;
		
		$shopId = data_get(getShop(), 'id', false);
		
		if ($campaign->shop_id === $shopId)
		{
			if ($campaign->delete())
			{
				$id = $campaign->id;
				
				dispatch_now(new SaveScriptCommand($shopId));
			}
		}
		
		return json(['campaign' => $id], 200, 'Campaign deleted');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \Promotions\Campaigns\Campaign  $campaign
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaign $campaign)
    {
        return json([]);
    }
}
