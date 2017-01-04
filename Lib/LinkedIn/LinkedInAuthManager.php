<?php
App::uses('MediaPlatformAuthManager', 'AuthManager.Lib');
App::uses('LinkedInAuthContainer', 'AuthManager.Lib/LinkedIn');
App::uses('LinkedInApi', 'AuthManager.Lib/LinkedIn');
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
			'redirectUri' => $this->_getCallbackUrl(MediaPlatform::LINKED_IN),
		]);
		$this->_leagueWrapper = new LeagueOauthWrapper($this->_linkedInProvider);
	}


	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		$options = [];

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
		$token = $this->_leagueWrapper->getAccessToken($request->query['code'] ?? null);
		if (!$token) {
			return false;
		}

		/** @var \League\OAuth2\Client\Provider\LinkedInResourceOwner $user */
		$user = $this->_linkedInProvider->getResourceOwner($token);
		if (empty($user)) {
			return false;
		}

		return $this->_saveUser($user->getFirstName() . ' ' . $user->getLastName(), $token, MediaPlatform::LINKED_IN);
	}


	/**
	 * @param                                         $username
	 * @param \League\OAuth2\Client\Token\AccessToken $accessToken
	 * @param                                         $mediaPlatform
	 *
	 * @return mixed
	 */
	protected function _saveUser($username, $accessToken, $mediaPlatform) {
		return $this->MediaPlatformUser->saveOauthUser(
			$this->_leagueWrapper->getSaveData($username, $accessToken, $mediaPlatform)
		);
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

		if ($this->_expiresIn(strtotime($oauthTokens['OauthToken']['token_expires']), 600)) {
			$oauthTokens = $this->_leagueWrapper->refreshToken($oauthTokens, $this->MediaPlatformUser);
		}

		$authContainer = new LinkedInAuthContainer();
		$authContainer->linkedInProvider = $this->_linkedInProvider;
		$authContainer->linkedInApi = new LinkedInApi($oauthTokens['OauthToken']['access_token']);

		return $authContainer;
	}
}