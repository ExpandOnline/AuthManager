<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('InstagramAuthContainer','AuthManager.Lib/Facebook');

use MetzWeb\Instagram\Instagram;

/**
 * Class InstagramAuthManager
 */
class InstagramAuthManager extends MediaPlatformAuthManager {

	/**
	 * @var Instagram
	 */
	protected $_instagram;

	/**
	 * Setup the API.
	 */
	public function __construct() {
		parent::__construct();
		Configure::load('AuthManager.API/Instagram');
		$this->_setInstagram();
	}

	/**
	 *
	 */
	protected function _setInstagram() {
		$this->_instagram = new Instagram([
			'apiKey' => Configure::read('Instagram.client_id'),
			'apiSecret' =>  Configure::read('Instagram.client_secret'),
			'apiCallback' => $this->_getCallbackUrl(MediaPlatform::INSTAGRAM)
		]);
	}

	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		//TODO: Pass correct scopes, once we know which we need.
		return $this->_instagram->getLoginUrl();
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
		$data = $this->_getAccessToken($request);
		if (!is_object($data) || !property_exists($data, 'access_token')) {
			return false;
		}
		return $this->_saveUser($data->user->username, $data->access_token, MediaPlatform::INSTAGRAM);
	}

	/**
	 * @param $request
	 *
	 * @return mixed
	 */
	protected function _getAccessToken($request) {
		return $this->_instagram->getOAuthToken($request->query['code']);
	}

	/**
	 * @param $userId
	 *
	 * @return FacebookAdsAuthContainer
	 */
	public function getAuthContainer($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		}

		$instagramAuthContainer = new InstagramAuthContainer();
		$this->_instagram->setAccessToken($oauthTokens['OauthToken']['access_token']);
		$instagramAuthContainer->instagram = $this->_instagram;

		return $instagramAuthContainer;
	}

}