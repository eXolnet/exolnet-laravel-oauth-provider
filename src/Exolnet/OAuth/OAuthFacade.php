<?php namespace Exolnet\OAuth;

use Illuminate\Support\Facades\Facade;

class OAuthFacade extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'oauth';
	}
}
