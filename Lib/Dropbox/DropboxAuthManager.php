<?php
App::uses('MediaPlatformAuthManager', 'AuthManager.Lib');
App::uses('DropboxAuthContainer', 'AuthManager.Lib/Dropbox');

/**
 * Class DropboxAuthManager
 */
class DropboxAuthManager extends MediaPlatformAuthManager {

	/**
	 * @var DropboxOauthProvider
	 */
	protected $_dropboxProvider;

	public function __construct() {
		parent::__construct();
		Configure::load('AuthManager.API/Dropbox');
		$this->_dropboxProvider = new Stevenmaguire\OAuth2\Client\Provider\Dropbox([
			'clientId' => Configure::read('Dropbox.client_id'),
			'clientSecret' => Configure::read('Dropbox.client_secret'),
			'redirectUri' => $this->_getCallbackUrl(MediaPlatform::DROPBOX),
		]);
	}

	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		return $this->_dropboxProvider->getAuthorizationUrl();
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

		/**
		 * @var \League\OAuth2\Client\Token\AccessToken|boolean $tokens
		 */
		$tokens = $this->_getAccessToken($request);
		if (!$tokens) {
			return false;
		}
		$details = $this->_dropboxProvider->getResourceOwner($tokens);

		return $this->_saveUser($details->toArray()['email'], $tokens, MediaPlatform::DROPBOX);
	}

	/**
	 * @param $request
	 *
	 * @return mixed
	 */
	protected function _getAccessToken($request) {
		try {
			return $this->_dropboxProvider->getAccessToken('authorization_code', [
				'code' => $request->query['code']
			]);
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * @param $userId
	 *
	 * @return DropboxAuthContainer
	 */
	public function getAuthContainer($userId) {
		/** @var MediaPlatformUser $MediaPlatformUser */
		$MediaPlatformUser = ClassRegistry::init('AuthManager.MediaPlatformUser');
		$dropboxAuthContainer = new DropboxAuthContainer();
		$oauthTokens = $MediaPlatformUser->getOauthTokens($userId);
		$dropboxAuthContainer->dropbox = new Dropbox\Dropbox($oauthTokens['OauthToken']['access_token']);

		return $dropboxAuthContainer;
	}

}