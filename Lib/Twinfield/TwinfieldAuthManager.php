<?php

App::uses('TwinfieldAuthContainer','AuthManager.Lib/Twinfield');
App::uses('TwinfieldOAuthProvider','AuthManager.Lib/Twinfield');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');

/**
 * Class TwinfieldAuthManager
 */
class TwinfieldAuthManager extends MediaPlatformAuthManager {

	/**
	 * @var TwinfieldOAuthProvider
	 */
	private $provider;

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct();
		Configure::load('AuthManager.API/Twinfield');
		$this->provider = new TwinfieldOAuthProvider([
			'clientId' => Configure::read('Twinfield.client_id'),
			'clientSecret' => Configure::read('Twinfield.client_secret'),
			'redirectUri' => $this->_getCallbackUrl(MediaPlatform::TWINFIELD),
		]);
	}


	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		return $this->provider->getAuthorizationUrl([
			'scope' => [
				TwinfieldOAuthProvider::SCOPE_ORGANISATION_USER,
				TwinfieldOAuthProvider::SCOPE_OPEN_ID,
				TwinfieldOAuthProvider::SCOPE_OFFLINE_ACCESS,
				TwinfieldOAuthProvider::SCOPE_ORGANISATION,
			]
		]);
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
		$accessToken  = $this->provider->getAccessToken('authorization_code', ['code' => $request->query['code']]);
		$refreshToken = $accessToken->getRefreshToken();
		if ($refreshToken === false) {
			return false;
		}
		$resourceOwner = $this->provider->getResourceOwner($accessToken);
		$resourceOwner = json_decode(base64_decode($resourceOwner->toArray()['sub']), true);
		return $this->MediaPlatformUser->saveOauthUser([
			'MediaPlatformUser' => [
				'username' => $resourceOwner['OrganisationUser'],
				'media_platform_id' => MediaPlatform::TWINFIELD
			],
			'OauthToken' => [
				'access_token' => $accessToken->getToken(),
				'refresh_token' => $refreshToken,
				'token_expires' => date('Y-m-d H:i:s', $accessToken->getExpires()),
			]
		]);
	}

	/**
	 * @param $userId
	 *
	 * @return Object|TwinfieldAuthContainer
	 * @throws Exception nj
	 */
	public function getAuthContainer($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		}

		$twinfieldAuthContainer = new TwinfieldAuthContainer();
		$twinfieldAuthContainer->provider = $this->provider;
		// Only refresh token when there's less than 10 minutes left!
		if (strtotime($oauthTokens['OauthToken']['token_expires']) - time() >= 600) {
			$twinfieldAuthContainer->accessToken = $oauthTokens['OauthToken']['access_token'];
		} else {
			$accessToken = $this->provider->getAccessToken(
				'refresh_token',
				['refresh_token' => $oauthTokens['OauthToken']['refresh_token']]
			);
			$this->MediaPlatformUser->updateTokenInDatabase(
				$oauthTokens['OauthToken']['id'],
				$accessToken->getToken(),
				date('Y-m-d H:i:s', $accessToken->getExpires())
			);
			$twinfieldAuthContainer->accessToken = $accessToken->getToken();
		}
		return $twinfieldAuthContainer;
	}

}