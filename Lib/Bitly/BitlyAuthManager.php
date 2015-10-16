<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('BitlyAuthContainer','AuthManager.Lib/Facebook');
App::import('Vendor', array('file' => 'bitly' . DS . 'bitly-api-php' . DS . 'Bitly'));

/**
 * Class BitlyAuthManager
 */
class BitlyAuthManager extends MediaPlatformAuthManager {

/**
 * @var Bitly
 */
	private $_Bitly;

/**
 * Setup the API.
 */
	public function __construct() {
		parent::__construct();
		Configure::load('AuthManager.API/Bitly');
		$this->_Bitly = new Bitly(
			Configure::read('Bitly.client_id'),
			Configure::read('Bitly.client_secret')
		);
	}

/**
 * Get the authentication url to add an user.
 *
 * @return string
 */
	public function getAuthUrl() {
		return $this->_Bitly->getAuthenticationUrl($this->_getCallbackUrl(MediaPlatform::BITLY));
	}

/**
 * Handles the request when being returned to the AuthManager plugin.
 *
 * @param CakeRequest $request
 *
 * @return bool
 */
	public function authenticateUser($request) {
		if (!array_key_exists('code', $request->query)) {
			return false;
		}
		$accessTokenData = $this->_Bitly->getAccessTokenData(
			$request->query['code'],
			$this->_getCallbackUrl(MediaPlatform::BITLY)
		);
		if (empty($accessTokenData)) {
			return false;
		}
		return $this->_saveUser($accessTokenData['login'], $accessTokenData['access_token'], MediaPlatform::BITLY);
	}

/**
 * @param $userId
 *
 * @return BitlyAuthContainer
 */
	public function getAuthContainer($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		}

		$bitlyAuthContainer = new BitlyAuthContainer();
		$this->_Bitly->accessToken = $oauthTokens['OauthToken']['access_token'];
		$bitlyAuthContainer->bitly = $this->_Bitly;

		return $bitlyAuthContainer;
	}

}