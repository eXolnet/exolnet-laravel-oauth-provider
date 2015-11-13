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

use Illuminate\Auth\UserInterface;
use JsonSerializable;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;

class ExolnetResourceOwner implements ResourceOwnerInterface, JsonSerializable {
	/**
	 * Domain
	 *
	 * @var string
	 */
	protected $domain;

	/**
	 * @var \League\OAuth2\Client\Token\AccessToken
	 */
	protected $authIdentifier;

	/**
	 * Raw response
	 *
	 * @var array
	 */
	protected $response;

	/**
	 * Creates new resource owner.
	 *
	 * @param array $response
	 */
	public function __construct(array $response = [])
	{
		$this->response = $response;
	}

	/**
	 * Get resource owner id
	 *
	 * @return string|null
	 */
	public function getId()
	{
		return $this->response['id'] ?: null;
	}

	/**
	 * Get resource owner email
	 *
	 * @return string|null
	 */
	public function getEmail()
	{
		return $this->response['email'] ?: null;
	}

	/**
	 * Get resource owner name
	 *
	 * @return string|null
	 */
	public function getName()
	{
		return $this->response['name'] ?: null;
	}

	/**
	 * Get resource owner nickname
	 *
	 * @return string|null
	 */
	public function getNickname()
	{
		return $this->response['login'] ?: null;
	}

	/**
	 * Get resource owner url
	 *
	 * @return string|null
	 */
	public function getUrl()
	{
		return trim($this->domain . '/' . $this->getNickname()) ?: null;
	}

	/**
	 * Set resource owner domain
	 *
	 * @param  string $domain
	 *
	 * @return $this
	 */
	public function setDomain($domain)
	{
		$this->domain = $domain;
		return $this;
	}

	/**
	 * Return all of the owner details available as an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->response;
	}

	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 *        which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize()
	{
		return $this->toArray();
	}
}
