<?php
App::uses('MediaPlatformAuthManager', 'AuthManager.Lib');
App::uses('LinkedInAuthContainer', 'AuthManager.Lib/LinkedIn');
App::uses('LeagueOauthWrapper', 'AuthManager.Lib');

/**
 * Class LinkedInAuthManager
 */
class LinkedInAuthManager extends MediaPlatformAuthManager {

	private $_linkedInProvider;

	private $_leagueWrapper;

	public function __construct() {
		parent::__construct();
		Configure::load('AuthManager.API/LinkedIn');
		$this->_linkedInProvider = new \League\OAuth2\Client\Provider\LinkedIn([
			'clientId' => Configure::read('LinkedIn.client_id'),
			'clientSecret' => Configure::read('LinkedIn.client_secret'),
			'redirectUri' => $this->_getCallbackUrl($this->getMediaPlatformId()),
		]);
		$this->_leagueWrapper = new LeagueOauthWrapper($this->_linkedInProvider);
	}

	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		$options = [
			'scope' => [
				'r_basicprofile',
				'rw_company_admin',
				'r_ad_campaigns',
				'r_ads',
				'r_ads_reporting',
				'r_organization_social',
				'rw_ads',
				'rw_company_admin',
				'rw_organization',
				'rw_organization_admin',
				'w_member_social',
				'w_organization_social',
				'w_share'
			]
		];

		return $this->_linkedInProvider->getAuthorizationUrl($options);
	}

	/**
	 * Handles the request when being returned to the AuthManager plugin.
	 *
	 * @param CakeRequest $request
	 *
	 * @return bool
	 */
	public function authenticateUser($request) {
		$token = $this->_getAccessToken($request->query['code'] ?? null);
		if (!$token) {
			return false;
		}

		if ($username = $this->_getUsername($token)) {
			return $this->_saveUser($username, $token, MediaPlatform::LINKED_IN);
		}

		return false;
	}

	protected function _getUsername(\League\OAuth2\Client\Token\AccessToken $token) {
		/** @var \League\OAuth2\Client\Provider\LinkedInResourceOwner $user */
		if ($user = $this->_linkedInProvider->getResourceOwner($token)) {
			return $user->getFirstName() . ' ' . $user->getLastName();
		}

		return false;
	}

	/**
	 * @param                                         $username
	 * @param \League\OAuth2\Client\Token\AccessToken $accessToken
	 * @param                                         $mediaPlatform
	 *
	 * @return mixed
	 */
	protected function _saveUser($username, $accessToken, $mediaPlatform) {
		foreach ([MediaPlatform::LINKED_IN_ADS, MediaPlatform::LINKED_IN] as $mediaPlatform) {
			$this->MediaPlatformUser->saveOauthUser(
				$this->_getLeague()->getSaveData($username, $accessToken, $mediaPlatform)
			);
		}
		return true;
	}

	/**
	 * @param $userId
	 *
	 * @return LinkedInAuthContainer
	 */
	public function getAuthContainer($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		}

		$this->_sendTokenExpiresEvent($userId, $oauthTokens['OauthToken']['token_expires']);

		$authContainer = new LinkedInAuthContainer();
		$authContainer->linkedInProvider = $this->_linkedInProvider;

		$api = new \Happyr\LinkedIn\LinkedIn(Configure::read('LinkedIn.client_id'), Configure::read('LinkedIn.client_secret'));
		$api->setAccessToken($oauthTokens['OauthToken']['access_token']);
		$authContainer->linkedInApi = $api;

		return $authContainer;
	}

	protected function _sendTokenExpiresEvent($userId, $tokenExpiresAt) {
		CakeEventManager::instance()->dispatch(new CakeEvent('AuthManager.LinkedInAuthManager.tokenExpiration', $this, array(
			'media_platform_user_id' => $userId,
			'expiration' => $tokenExpiresAt
		)));

		return true;
	}

	/**
	 * @return LeagueOauthWrapper
	 */
	protected function _getLeague() {
		return $this->_leagueWrapper;
	}

	protected function _getAccessToken($code) {
		return $this->_getLeague()->getAccessToken($code);
	}

	/**
	 * @return int
	 */
	protected function getMediaPlatformId() {
		return MediaPlatform::LINKED_IN;
	}

}