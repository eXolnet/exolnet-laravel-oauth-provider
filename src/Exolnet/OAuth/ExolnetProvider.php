<?php namespace Exolnet\OAuth;
/**
 * Copyright © 2015 eXolnet Inc. All rights reserved. (http://www.exolnet.com)
 *
 * This file contains copyrighted code that is the sole property of eXolnet Inc.
 * You may not use this file except with a written agreement.
 *
 * This code is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND,
 * EITHER EXPRESS OR IMPLIED, AND EXOLNET INC. HEREBY DISCLAIMS ALL SUCH
 * WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT OR NON-INFRINGEMENT.
 *
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use Config;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class ExolnetProvider extends AbstractProvider
{
	use BearerAuthorizationTrait;

	/**
	 * Domain
	 *
	 * @var string
	 */
	public $domain = 'https://accounts.exolnet.com';

	/**
	 * @return string
	 */
	public function getDomain()
	{
		return rtrim(Config::get('oauth.domain') ?: $this->domain, '/');
	}

	/**
	 * @param string $clientId
	 * @return $this
	 */
	public function setClientId($clientId)
	{
		$this->clientId = $clientId;

		return $this;
	}

	/**
	 * @param string $clientSecret
	 * @return $this
	 */
	public function setClientSecret($clientSecret)
	{
		$this->clientSecret = $clientSecret;

		return $this;
	}

	/**
	 * @param string $clientId
	 * @param string $clientSecret
	 * @return $this
	 */
	public function setClient($clientId, $clientSecret)
	{
		return $this
			->setClientId($clientId)
			->setClientSecret($clientSecret);
	}

	/**
	 * @param string $redirectUri
	 * @return $this
	 */
	public function setRedirectUri($redirectUri)
	{
		$this->redirectUri = $redirectUri;

		return $this;
	}

	/**
	 * Returns the base URL for authorizing a client.
	 *
	 * Eg. https://oauth.service.com/authorize
	 *
	 * @return string
	 */
	public function getBaseAuthorizationUrl()
	{
		return $this->getDomain().'/oauth2/authorize';
	}

	/**
	 * Returns the base URL for requesting an access token.
	 *
	 * Eg. https://oauth.service.com/token
	 *
	 * @param array $params
	 * @return string
	 */
	public function getBaseAccessTokenUrl(array $params)
	{
		return $this->getDomain().'/oauth2/token';
	}

	/**
	 * Returns the URL for requesting the resource owner's details.
	 *
	 * @param AccessToken $token
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl(AccessToken $token)
	{
		return $this->getDomain().'/user';
	}

	/**
	 * Returns the default scopes used by this provider.
	 *
	 * This should only be the scopes that are required to request the details
	 * of the resource owner, rather than all the available scopes.
	 *
	 * @return array
	 */
	protected function getDefaultScopes()
	{
		return [];
	}

	/**
	 * Checks a provider response for errors.
	 *
	 * @throws IdentityProviderException
	 * @param  ResponseInterface $response
	 * @param  array|string $data Parsed response data
	 * @return void
	 */
	protected function checkResponse(ResponseInterface $response, $data)
	{
		if ($response->getStatusCode() >= 400) {
			throw new IdentityProviderException(
				$data['error_description'] ?: $response->getReasonPhrase(),
				$response->getStatusCode(),
				$response
			);
		}
	}

	/**
	 * Generates a resource owner object from a successful resource owner
	 * details request.
	 *
	 * @param  array $response
	 * @param  AccessToken $token
	 * @return ResourceOwnerInterface
	 */
	protected function createResourceOwner(array $response, AccessToken $token)
	{
		return (new ExolnetResourceOwner($response))
			->setDomain($this->getDomain());
	}
}
