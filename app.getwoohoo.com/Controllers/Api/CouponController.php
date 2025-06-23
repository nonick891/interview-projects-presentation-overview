<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use LuckyCoupon\Core\Commands\SetUserUpdateCoreDataEventCommand;
use LuckyCoupon\Coupons\Commands\DeleteUniqueCouponCommand;
use LuckyCoupon\Coupons\Commands\UpdateCouponsCommand;
use LuckyCoupon\Coupons\CouponRepositoryInterface;
use LuckyCoupon\Coupons\EloquentCouponRepository;
use LuckyCoupon\Coupons\GetCouponCodeCommand;
use LuckyCoupon\Requests\Coupons\DeleteUniqueCouponRequest;
use LuckyCoupon\Requests\Coupons\GetCouponsRequest;
use LuckyCoupon\Requests\Coupons\PatchCouponsRequest;
use LuckyCoupon\Requests\Coupons\PostGetCodeRequest;
use LuckyCoupon\Validation\Extractor;

/**
 * Class CouponController
 * @package App\Http\Controllers\Api
 */
class CouponController extends ValidateController
{
	/**
	 * @var CouponRepositoryInterface
	 */
	private $couponRepo;
	
	private $extractor;
	
	/**
	 * CouponController constructor.
	 * @param CouponRepositoryInterface|EloquentCouponRepository $couponRepo
	 */
	public function __construct(CouponRepositoryInterface $couponRepo)
	{
		$this->couponRepo = $couponRepo;
		
		$this->extractor = new Extractor();
	}

	/**
	 * @param GetCouponsRequest $request
	 * @return array
	 */
	public function index(GetCouponsRequest $request)
	{
		if ($errs = $this->_getErrors($request)) return $errs;

		$gameId = $request->get('game_id');
		
		$isGameValid = $this->extractor->validateGame($gameId);
		
		if (!$isGameValid) return ['coupons' => []];
		
		$coupons = $this->couponRepo->getByGameIdWithShopifyCoupons($gameId)->load('shopifyCoupon');

		return ['coupons' => $coupons];
    }

	/**
	 * @param Request|PatchCouponsRequest $request
	 * @return array|bool
	 */
	public function update(Request $request)
	{
		return dispatch(new UpdateCouponsCommand($request));
    }

	/**
	 * @param Request $request
	 * @return array
	 */
	public function updateBunch(Request $request)
	{
		extract($request->only('coupons'));
		
		$gameId = data_get($coupons, '0.game_id', 0);
		
		$isGameValid = $this->extractor->validateGame($gameId);
		
		if (!$isGameValid) return ['coupons' => []];
		
		dispatch(new SetUserUpdateCoreDataEventCommand());
		
		return $this->couponRepo->updateBunch($coupons);
    }

	/**
	 * @param PostGetCodeRequest $request
	 * @return mixed
	 */
	public function getCode(PostGetCodeRequest $request)
	{
		return dispatch(new GetCouponCodeCommand($request));
    }

	/**
	 * @param DeleteUniqueCouponRequest $request
	 * @return mixed
	 */
	public function deleteUnique(DeleteUniqueCouponRequest $request)
	{
		return dispatch(new DeleteUniqueCouponCommand($request));
    }
}
