<?php namespace Exolnet\OAuth;

use Illuminate\Support\ServiceProvider;

class OAuthServiceProvider extends ServiceProvider {
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerService();
		$this->registerController();
		$this->registerFilter();
	}

	/**
	 * @return void
	 */
	protected function registerService()
	{
		$this->app['oauth'] = $this->app->share(function ($app) {
			return $app->make(OAuthService::class);
		});
	}

	/**
	 * @return void
	 */
	protected function registerController()
	{
		$this->app['router']->get('oauth', [
			'as' => 'oauth',
			'uses' => '\Exolnet\OAuth\OAuthController@authorize',
		]);

		$this->app['router']->get('oauth/callback', [
			'as' => 'oauth.callback',
			'uses' => '\Exolnet\OAuth\OAuthController@callback',
		]);

		$this->app['router']->get('oauth/logout', [
			'as' => 'oauth.logout',
			'uses' => '\Exolnet\OAuth\OAuthController@logout',
		]);
	}

	/**
	 * @return void
	 */
	private function registerFilter()
	{
		$this->app['router']->filter('oauth', function() {
			if ($this->app['oauth']->guest()) {
				return $this->app['oauth']->login();
			}
		});
	}
}
