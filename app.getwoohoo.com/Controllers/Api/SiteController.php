<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use LuckyCoupon\Core\Commands\SetUserUpdateCoreDataEventCommand;
use LuckyCoupon\Requests\Sites\GetSiteRequest;
use LuckyCoupon\Requests\Sites\PutSiteRequest;
use LuckyCoupon\Requests\Sites\PatchSiteRequest;
use LuckyCoupon\Sites\Commands\AddSiteCommand;
use LuckyCoupon\Sites\Commands\DeleteSiteCommand;
use LuckyCoupon\Sites\SiteRepositoryInterface;

/**
 * Class SiteController
 * @package App\Http\Controllers\Api
 */
class SiteController extends ValidateController
{

	/**
	 * @var SiteRepositoryInterface
	 */
	private $siteRepo;

	function __construct(SiteRepositoryInterface $siteRepo)
	{
		$this->siteRepo = $siteRepo;
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$userId = $request->user()->id;

		return ['sites' => $this->siteRepo->getByUserId($userId)];
	}

	/**
	 * @param Request|GetSiteRequest $request
	 * @return array
	 */
	public function get(GetSiteRequest $request)
	{
		if ($errs = $this->_getErrors($request)) return $errs;

		extract($request->only('id'));

		$site = $this->siteRepo->getById($id);

		return ['site' => $site];
    }

	/**
	 * @param Request|PutSiteRequest $request
	 * @return array
	 */
	public function add(PutSiteRequest $request)
	{
		return dispatch(new AddSiteCommand($request));
	}

	/**
	 * @param Request|PatchSiteRequest $request
	 * @return array
	 */
	public function update(PatchSiteRequest $request)
	{
		if ($errs = $this->_getErrors($request)) return $errs;

		$update = $request->all();
		
		dispatch(new SetUserUpdateCoreDataEventCommand());
		
		return ['update' => $this->siteRepo->update($update)];
	}

	/**
	 * @param Request|GetSiteRequest $request
	 * @return array
	 */
	public function delete(GetSiteRequest $request)
	{
		return dispatch(new DeleteSiteCommand($request));
	}
	
}
