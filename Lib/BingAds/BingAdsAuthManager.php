<?php
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;
App::uses('BingAdsAuthContainer','AuthManager.Lib/BingAds');
App::uses('ClientProxyFactory','AuthManager.Lib/BingAds');
App::uses('BingAdsApiWrapper','AuthManager.Lib/BingAds');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('BingAdsApi','AuthManager.Lib/BingAds');

/**
 * Class BingAdsAuthManager
 */
class BingAdsAuthManager extends MediaPlatformAuthManager {

	/**
	 * @var Stevenmaguire\OAuth2\Client\Provider\
	 */
	protected $_microsoftProvider;

/**
 * Setup the API.
 */
	public function __construct() {
		parent::__construct();
		Configure::load('AuthManager.API/BingAds');
		$this->_microsoftProvider = new Microsoft([
			'clientId' => Configure::read('BingAds.client_id'),
			'clientSecret' => Configure::read('BingAds.client_secret'),
			'redirectUri' => $this->_getCallbackUrl(MediaPlatform::BING_ADS),
		]);
	}

/**
 * Get the authentication url to add an user.
 *
 * @return string
 */
	public function getAuthUrl() {
		$options = [
			'scope' => [
				'bingads.manage'
			]
		];
		return $this->_microsoftProvider->getAuthorizationUrl($options);
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

		$tokens = $this->_getAccessToken($request);
		if (!$tokens) {
			return false;
		}
		$username = $this->_getUsername($tokens->getToken());

		return $this->_saveUser($username, $tokens, MediaPlatform::BING_ADS);
	}

/**
 * @param $request
 *
 * @return \League\OAuth2\Client\Token\AccessToken|boolean
 */
	protected function _getAccessToken($request) {
		try {
			return $this->_microsoftProvider->getAccessToken('authorization_code', [
				'code' => $request->query['code']
			]);
		} catch (Exception $e) {
			return false;
		}
	}

/**
 * @param string $accessToken
 *
 * @return string
 */
	protected function _getUsername($accessToken) {
		$bingAdsApiWrapper = new BingAdsApiWrapper($this->_getClientProxyFactory($accessToken));
		$user = $bingAdsApiWrapper->getUser();

		return $user->UserName;
	}

/**
 * @param $accessToken
 * @param $apiVersion
 *
 * @return ClientProxyFactory
 */
	protected function _getClientProxyFactory($accessToken) {
		$developerToken = Configure::read('BingAds.developer_token');
		$clientProxyFactory = new ClientProxyFactory();
		$clientProxyFactory->setAccessToken($accessToken);
		$clientProxyFactory->setDeveloperToken($developerToken);

		return $clientProxyFactory;
	}

	/**
	 * @param                                         $username
	 * @param \League\OAuth2\Client\Token\AccessToken $accessToken
	 * @param                                         $mediaPlatform
	 *
	 * @return mixed
	 */
	protected function _saveUser($username, $accessToken, $mediaPlatform) {
		$saveData = array(
			'MediaPlatformUser' => array(
				'username' => $username,
				'media_platform_id' => $mediaPlatform
			),
			'OauthToken' => array(
				'access_token' => $accessToken->getToken(),
				'refresh_token' => $accessToken->getRefreshToken(),
				'token_expires' => date('Y-m-d H:i:s', $accessToken->getExpires()),
			)
		);

		return $this->MediaPlatformUser->saveOauthUser($saveData);
	}

/**
 * @param $userId
 *
 * @return BingAdsAuthContainer
 */
	public function getAuthContainer($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		}

		if ($this->_expiresIn(strtotime($oauthTokens['OauthToken']['token_expires']), 600)) {
			$oauthTokens = $this->_refreshTokens($oauthTokens);
		}

		$bingAdsAuthContainer = new BingAdsAuthContainer();
		$bingAdsAuthContainer->microsoftProvider = $this->_microsoftProvider;
		$bingAdsAuthContainer->bingAdsApi = new BingAdsApiWrapper($this->_getClientProxyFactory(
			$oauthTokens['OauthToken']['access_token']
		));

		return $bingAdsAuthContainer;
	}

	/**
	 * @param $oauthTokens
	 *
	 * @return mixed
	 */
	protected function _refreshTokens($oauthTokens) {
		$accessToken = $this->_microsoftProvider->getAccessToken('refresh_token', [
			'refresh_token' => $oauthTokens['OauthToken']['refresh_token']
		]);
		return $this->MediaPlatformUser->updateTokenInDatabase(
			$oauthTokens['OauthToken']['id'],
			$accessToken->getToken(),
			date('Y-m-d H:i:s', $accessToken->getExpires())
		);
	}

}