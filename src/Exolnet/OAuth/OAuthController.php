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

use Controller;
use Input;
use Redirect;

class OAuthController extends Controller
{
	/**
	 * @var \Exolnet\OAuth\OAuthService
	 */
	private $authService;

	/**
	 * OAuthController constructor.
	 *
	 * @param \Exolnet\OAuth\OAuthService $authService
	 */
	public function __construct(OAuthService $authService)
	{
		$this->authService = $authService;
	}

	/**
	 * Redirect the user to the GitHub authentication page.
	 *
	 * @return \Response
	 */
	public function authorize()
	{
		return $this->authService->authorize(route('oauth.callback'));
	}

	/**
	 * Obtain the user information from GitHub.
	 *
	 * @return \Response
	 */
	public function callback()
	{
		return $this->authService->validateAuthorize(Input::all());
	}

	/**
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function logout()
	{
		$this->authService->logout();

		return Redirect::back();
	}
}
