<?php
App::uses('BingAdsAuthContainer','AuthManager.Lib/BingAds');
App::uses('ClientProxyFactory','AuthManager.Lib/BingAds');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('BingAdsApi','AuthManager.Lib/BingAds');
use League\OAuth2\Client\Provider\Microsoft;

/**
 * Class BingAdsAuthManager
 */
class BingAdsAuthManager extends MediaPlatformAuthManager {

/**
 * @var League\OAuth2\Client\Provider\Microsoft
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
			'scopes' => [
				'bingads.manage'
			]
		]);
	}

/**
 * Get the authentication url to add an user.
 *
 * @return string
 */
	public function getAuthUrl() {
		return $this->_microsoftProvider->getAuthorizationUrl();
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
		$username = $this->_getUsername($tokens->accessToken);

		return $this->_saveUser($username, $tokens, MediaPlatform::BING_ADS);
	}

/**
 * @param $request
 *
 * @return mixed
 * @throws \League\OAuth2\Client\Exception\IDPException
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
		$clientProxy = $this->_getClientProxy(BingAdsApi::CUSTOMER_ENDPOINT, $accessToken, BingAdsApi::VERSION_9);
		$request = new \BingAds\v9\CustomerManagement\GetUserRequest();
		$user = $clientProxy->GetService()->GetUser($request)->User;

		return $user->UserName;
	}

/**
 * @param $endPoint
 * @param $accessToken
 * @param $apiVersion
 *
 * @return \BingAds\v10\Proxy\ClientProxy|\BingAds\v9\Proxy\ClientProxy
 */
	protected function _getClientProxy($endPoint, $accessToken, $apiVersion) {
		$clientProxyFactory = $this->_getClientProxyFactory($accessToken, $apiVersion);
		return $clientProxyFactory->createClientProxy($endPoint);
	}

/**
 * @param $accessToken
 * @param $apiVersion
 *
 * @return ClientProxyFactory
 */
	protected function _getClientProxyFactory($accessToken, $apiVersion = BingAdsApi::VERSION_10) {
		$developerToken = Configure::read('BingAds.developer_token');
		$clientProxyFactory = new ClientProxyFactory();
		$clientProxyFactory->setAccessToken($accessToken);
		$clientProxyFactory->setApiVersion($apiVersion);
		$clientProxyFactory->setDeveloperToken($developerToken);

		return $clientProxyFactory;
	}

/**
 * @param $username
 * @param $accessToken
 * @param $mediaPlatform
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
				'access_token' => $accessToken->accessToken,
				'refresh_token' => $accessToken->refreshToken,
				'token_expires' => date('Y-m-d H:i:s', $accessToken->expires),
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
		$bingAdsAuthContainer->clientProxyFactory = $this->_getClientProxyFactory(
			$oauthTokens['OauthToken']['access_token']
		);

		return $bingAdsAuthContainer;
	}

/**
 * @param $oauthTokens
 *
 * @return mixed
 * @throws \League\OAuth2\Client\Exception\IDPException
 */
	protected function _refreshTokens($oauthTokens) {
		$accessToken = $this->_microsoftProvider->getAccessToken('refresh_token', [
			'refresh_token' => $oauthTokens['OauthToken']['refresh_token']
		]);

		return $this->MediaPlatformUser->updateTokenInDatabase($oauthTokens['OauthToken']['id'],
			$accessToken->accessToken,
			date('Y-m-d H:i:s', ($accessToken->expires))
		);
	}

}