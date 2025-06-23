<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use LuckyCoupon\Requests\Settings\DeleteSettingsEmailLogoRequest;
use LuckyCoupon\Requests\Settings\DeleteSettingsFileRequest;
use LuckyCoupon\Requests\Settings\GetSettingsRequest;
use LuckyCoupon\Requests\Settings\PostSettingsRevealFileLogoRequest;
use LuckyCoupon\Requests\Settings\PostSettingsSpinTheWheelFileLogoRequest;
use LuckyCoupon\Requests\Settings\PostSettingsSpinTheWheelFileMobileLogoRequest;
use LuckyCoupon\Requests\Settings\PostSettingsSpinTheWheelInformFileLogoRequest;
use LuckyCoupon\Requests\Settings\PostSettingsWheelFileLogoRequest;
use LuckyCoupon\Requests\Settings\PostSettingsFullWheelFileLogoRequest;
use LuckyCoupon\Requests\Settings\PostSettingsFileRequest;
use LuckyCoupon\Requests\Settings\UpdateCookieResetRequest;
use LuckyCoupon\Requests\Settings\UpdateSettingsEmailLogoRequest;
use LuckyCoupon\Settings\Commands\DeleteSettingFileCommand;
use LuckyCoupon\Settings\Commands\GetGameSettingsCommand;
use LuckyCoupon\Settings\Commands\GetSettingsAndCouponsCommand;
use LuckyCoupon\Settings\Commands\UpdateCookieResetSettingsCommand;
use LuckyCoupon\Settings\Commands\UpdateSettingEmailLogoCommand;
use LuckyCoupon\Settings\Commands\UpdateSettingFileCommand;
use LuckyCoupon\Settings\Commands\UpdateSettingRevealFileLogoCommand;
use LuckyCoupon\Settings\Commands\UpdateSettingSpinTheWheelFileLogoCommand;
use LuckyCoupon\Settings\Commands\UpdateSettingSpinTheWheelInformFileLogoCommand;
use LuckyCoupon\Settings\Commands\UpdateSettingSpinTheWheelFileMobileLogoCommand;
use LuckyCoupon\Settings\Commands\UpdateSettingWheelFileLogoCommand;
use LuckyCoupon\Settings\Commands\UpdateSettingFullWheelFileLogoCommand;
use LuckyCoupon\Settings\Commands\UpdateSettingsCommand;

/**
 * Class SettingController
 * @package App\Http\Controllers\Api
 */
class SettingController extends ValidateController
{
	/**
	 * @param Request|GetSettingsRequest $request
	 * @return array|bool
	 */
	public function index(GetSettingsRequest $request)
	{
		return dispatch(new GetGameSettingsCommand($request));
    }
	
	/**
	 * @param GetSettingsRequest $request
	 * @return mixed
	 */
	public function get(GetSettingsRequest $request)
	{
		return dispatch(new GetSettingsAndCouponsCommand($request));
    }
    
	/**
	 * @param Request $request
	 * @return array
	 */
	public function update(Request $request)
	{
		return dispatch(new UpdateSettingsCommand($request));
    }
	
	/**
	 * @param PostSettingsFileRequest $request
	 * @return mixed
	 */
	public function updateFile(PostSettingsFileRequest $request)
	{
		return dispatch(new UpdateSettingFileCommand($request));
	}
	
	/**
	 * @param DeleteSettingsFileRequest $request
	 * @return mixed
	 */
	public function deleteFile(DeleteSettingsFileRequest $request)
	{
		return dispatch(new DeleteSettingFileCommand($request, 'image'));
	}
	
	/**
	 * @param PostSettingsWheelFileLogoRequest $request
	 * @return mixed
	 */
	public function updateWheelFileLogo(PostSettingsWheelFileLogoRequest $request)
	{
		return dispatch(new UpdateSettingWheelFileLogoCommand($request));
	}
	
	/**
	 * @param DeleteSettingsFileRequest $request
	 * @return mixed
	 */
	public function deleteWheelFileLogo(DeleteSettingsFileRequest $request)
	{
		return dispatch(new DeleteSettingFileCommand($request, 'wheelLogo'));
	}
	
	/**
	 * @param PostSettingsRevealFileLogoRequest $request
	 * @return mixed
	 */
	public function updateRevealFileLogo(PostSettingsRevealFileLogoRequest $request)
	{
		return dispatch(new UpdateSettingRevealFileLogoCommand($request));
	}
	
	/**
	 * @param DeleteSettingsFileRequest $request
	 * @return mixed
	 */
	public function deleteRevealFileLogo(DeleteSettingsFileRequest $request)
	{
		return dispatch(new DeleteSettingFileCommand($request, 'revealLogo'));
	}
	
	/**
	 * @param PostSettingsSpinTheWheelFileLogoRequest $request
	 *
	 * @return mixed
	 */
	public function updateSpinTheWheelFileLogo(PostSettingsSpinTheWheelFileLogoRequest $request)
	{
		return dispatch(new UpdateSettingSpinTheWheelFileLogoCommand($request));
	}
	
	/**
	 * @param DeleteSettingsFileRequest $request
	 * @return mixed
	 */
	public function deleteSpinTheWheelFileLogo(DeleteSettingsFileRequest $request)
	{
		return dispatch(new DeleteSettingFileCommand($request, 'spinTheWheelLogo'));
	}
	
	/**
	 * @param PostSettingsSpinTheWheelInformFileLogoRequest $request
	 *
	 * @return mixed
	 */
	public function updateSpinTheWheelInformFileLogo(PostSettingsSpinTheWheelInformFileLogoRequest $request)
	{
		return dispatch(new UpdateSettingSpinTheWheelInformFileLogoCommand($request));
	}
	
	/**
	 * @param PostSettingsSpinTheWheelFileMobileLogoRequest $request
	 *
	 * @return mixed
	 */
	public function updateSpinTheWheelFileMobileLogo(PostSettingsSpinTheWheelFileMobileLogoRequest $request)
	{
		return dispatch(new UpdateSettingSpinTheWheelFileMobileLogoCommand($request));
	}
	
	/**
	 * @param DeleteSettingsFileRequest $request
	 * @return mixed
	 */
	public function deleteSpinTheWheelFileMobileLogo(DeleteSettingsFileRequest $request)
	{
		return dispatch(new DeleteSettingFileCommand($request, 'spinTheWheelMobileLogo'));
	}
	
	/**
	 * @param DeleteSettingsFileRequest $request
	 * @return mixed
	 */
	public function deleteSpinTheWheelInformFileLogo(DeleteSettingsFileRequest $request)
	{
		return dispatch(new DeleteSettingFileCommand($request, 'spinTheWheelInformLogo'));
	}
	
	/**
	 * @param PostSettingsFullWheelFileLogoRequest $request
	 * @return mixed
	 */
	public function updateFullWheelFileLogo(PostSettingsFullWheelFileLogoRequest $request)
	{
		return dispatch(new UpdateSettingFullWheelFileLogoCommand($request));
	}

	/**
	 * @param DeleteSettingsFileRequest $request
	 * @return mixed
	 */
	public function deleteFullWheelFileLogo(DeleteSettingsFileRequest $request)
	{
		return dispatch(new DeleteSettingFileCommand($request, 'fullwheelLogo'));
	}
	
	/**
	 * @param UpdateSettingsEmailLogoRequest $request
	 * @return mixed
	 */
	public function updateEmailLogo(UpdateSettingsEmailLogoRequest $request)
	{
		return dispatch(new UpdateSettingEmailLogoCommand($request));
	}
	
	/**
	 * @param DeleteSettingsEmailLogoRequest $request
	 * @return mixed
	 */
	public function deleteEmailLogo(DeleteSettingsEmailLogoRequest $request)
	{
		return dispatch(new DeleteSettingFileCommand($request, 'receiveEmailLogoImage'));
	}
	
	/**
	 * @param UpdateCookieResetRequest $request
	 * @return mixed
	 */
	public function updateCookieReset(UpdateCookieResetRequest $request)
	{
		return dispatch(new UpdateCookieResetSettingsCommand($request));
	}
}
