<?php namespace Exolnet\OAuth;

use Config;
use Illuminate\Session\Store as SessionStore;
use League\OAuth2\Client\Token\AccessToken;
use Redirect;
use Symfony\Component\Yaml\Exception\RuntimeException;
use URL;

class OAuthService {
	/**
	 * @var \Exolnet\OAuth\ExolnetProvider
	 */
	private $provider;

	/**
	 * @var \Illuminate\Session\Store
	 */
	private $session;

	/**
	 * @var \Exolnet\OAuth\ExolnetResourceOwner|null
	 */
	protected $user;

	/**
	 * @param \Exolnet\OAuth\ExolnetProvider $provider
	 * @param \Illuminate\Session\Store              $session
	 */
	public function __construct(ExolnetProvider $provider, SessionStore $session)
	{
		$this->provider = $provider;
		$this->session = $session;

		$this->initializeProvider();
	}

	/**
	 * @return string
	 */
	public function getClientId()
	{
		return Config::get('oauth.client.id');
	}

	/**
	 * @return string
	 */
	public function getClientSecret()
	{
		return Config::get('oauth.client.secret');
	}

	/**
	 * Determine if the current user is authenticated.
	 *
	 * @return bool
	 */
	public function check()
	{
		return ! is_null($this->user());
	}

	/**
	 * Determine if the current user is a guest.
	 *
	 * @return bool
	 */
	public function guest()
	{
		return ! $this->check();
	}

	/**
	 * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface|null
	 */
	public function user()
	{
		if ($this->user !== null) {
			return $this->user;
		}

		$accessToken = $this->accessToken();

		if ($accessToken && $accessToken->hasExpired()) {
			$accessToken = $this->refreshToken();
		}

		if ( ! $accessToken) {
			return null;
		}

		return $this->provider->getResourceOwner($accessToken);
	}

	/**
	 * @return \League\OAuth2\Client\Token\AccessToken|null
	 */
	public function accessToken()
	{
		return $this->session->get($this->getSessionName());
	}

	public function authorize()
	{
		$authorizationUrl = $this->provider->getAuthorizationUrl();

		$this->session->set($this->getSessionStateName(), $this->provider->getState());
		$this->session->reflash();

		return Redirect::to($authorizationUrl);
	}

	/**
	 * @param array $data
	 */
	public function validateAuthorize(array $data)
	{
		$state      = array_get($data, 'state');
		$code       = array_get($data, 'code');
		$savedState = $this->session->get($this->getSessionStateName());

		$this->session->forget($this->getSessionStateName());

		if (empty($code) || empty($state) || $state !== $savedState) {
			// TODO-AD: Put something more appropriate <adeschambeault@exolnet.com>
			throw new RuntimeException;
		}

		$accessToken = $this->provider->getAccessToken('authorization_code', [
			'code' => $code,
		]);

		$this->updateSession($accessToken);

		return Redirect::intended();
	}

	/**
	 * @return \League\OAuth2\Client\Token\AccessToken
	 */
	public function refreshToken()
	{
		$accessToken = $this->accessToken();

		$newAccessToken = $this->provider->getAccessToken('refresh_token', [
			'refresh_token' => $accessToken->getRefreshToken(),
		]);

		$this->updateSession($newAccessToken);

		return $newAccessToken;
	}

	/**
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function login()
	{
		return Redirect::guest(URL::route('oauth'));
	}

	/**
	 * @return $this
	 */
	public function logout()
	{
		$this->session->forget($this->getSessionName());

		return $this;
	}

	/**
	 * @return string
	 */
	protected function getSessionName()
	{
		return 'oauth_'.md5(get_class($this));
	}

	/**
	 * @return string
	 */
	protected function getSessionStateName()
	{
		return $this->getSessionName() .'_state';
	}

	/**
	 * @param \League\OAuth2\Client\Token\AccessToken $accessToken
	 */
	protected function updateSession(AccessToken $accessToken)
	{
		$this->session->put($this->getSessionName(), $accessToken);
	}

	/**
	 * @return void
	 */
	protected function initializeProvider()
	{
		$this->provider
			->setClientId($this->getClientId())
			->setClientSecret($this->getClientSecret())
			->setRedirectUri(route('oauth.callback'));
	}
}
