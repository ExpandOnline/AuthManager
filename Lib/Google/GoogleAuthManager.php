<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('GoogleAuthContainer','AuthManager.Lib/Google');

/**
 * Class GoogleAnalyticsAuthManager
 */
abstract class GoogleAuthManager extends MediaPlatformAuthManager {

	/**
	 * Set the google client and service.
	 */
	public function __construct() {
		parent::__construct();
		$this->_setGoogleClient();
		$this->_setGoogleService();
	}

/**
 * @var Google_Client
 */
	protected $_client;

/**
 * @var Google_AnalyticsService|Google_Service_Analytics|Google_Service_Dfareporting
 */
	protected $_service;

/**
 * @return String
 */
	protected abstract function _getUserName();

/**
 * @return int
 */
	protected abstract function _getPlatformId();

/**
 * @return array
 */
	protected abstract function _getScopes();

/**
 *
 */
	protected abstract function _setGoogleService();

/**
 *
 */
	protected abstract function _getConfigFilePath();

/**
 *
 */
	protected function _setGoogleClient() {
		$googleClient = new Google_Client();
		$googleClient->setAuthConfigFile($this->_getConfigFilePath());
		$googleClient->addScope($this->_getScopes());
		$googleClient->setRedirectUri(Router::url(array(
			'plugin' => 'auth_manager',
			'controller' => 'media_platform_users',
			'action' => 'callback',
			$this->_getPlatformId()
		), true));
		// This will force Google to always return the refresh_token.
		$googleClient->setAccessType('offline');
		$googleClient->setApprovalPrompt('force');
		$this->_client = $googleClient;
	}


/**
 * Get the authorization URL to redirect to.
 *
 * @return string
 */
	public function getAuthUrl() {
		return $this->_client->createAuthUrl();
	}

/**
 * @param CakeRequest $request
 *
 * @return bool
 */
	public function authenticateUser($request) {
		$data = $request->query;
		if (!array_key_exists('code', $data)) {
			return false;
		}
		$oauthTokens = $this->_getOauthTokens($data['code']);
		$username = $this->_getUserName();
		return $this->_saveUser($username, $oauthTokens, $this->_getPlatformId());
	}

/**
 * @param $userId
 *
 * @return GoogleAuthContainer
 */
	public function getAuthContainer($userId) {
		$this->_setTokenOnClient($userId);
		$authContainer = new GoogleAuthContainer();
		$authContainer->client = $this->_client;
		$authContainer->service = $this->_service;
		$authContainer->userId = $userId;
		return $authContainer;
	}

/**
 * @param $userId
 */
	protected function _setTokenOnClient($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		} elseif ($this->_expiresIn(strtotime($oauthTokens['OauthToken']['token_expires']), 600)
			|| Configure::read('debug') >= 1
		) {
			$this->_refreshTokens($oauthTokens);
		} else {
			$this->_setTokens($oauthTokens);
		}
	}

/**
 * @param $oauthTokens
 */
	protected function _refreshTokens($oauthTokens) {
		$this->_client->refreshToken($oauthTokens['OauthToken']['refresh_token']);
		$token = json_decode($this->_client->getAccessToken());
		$this->MediaPlatformUser->updateTokenInDatabase($oauthTokens['OauthToken']['id'], $token->access_token,
			date('Y-m-d H:i:s', ($token->created + $token->expires_in)));
	}

/**
 * @param $oauthTokens
 */
	protected function _setTokens($oauthTokens) {
		$token = json_encode(array(
			'access_token' => $oauthTokens['OauthToken']['access_token'],
			'expires_in' => 3600,
			'created' => strtotime($oauthTokens['OauthToken']['token_expires']) - 3600
		));

		$this->_client->setAccessToken($token);
	}
/**
 * @param string $username
 * @param array $oauthTokens
 * @param int $mediaPlatformId
 *
 * @return mixed
 * @throws Exception
 */
	protected function _saveUser($username, $oauthTokens, $mediaPlatformId) {
		$saveData = array(
			'MediaPlatformUser' => array(
				'username' => $username,
				'media_platform_id' => $mediaPlatformId
			),
			'OauthToken' => array(
				'access_token' => $oauthTokens['access_token'],
				'refresh_token' => $oauthTokens['refresh_token'],
				'token_expires' => date('Y-m-d H:i:s', $oauthTokens['created'] + $oauthTokens['expires_in']),
			)
		);
		return $this->MediaPlatformUser->saveOauthUser($saveData);
	}

/**
 * Get OAUTH tokens based on the given code.
 *
 * @param $code
 *
 * @return array
 */
	protected function _getOauthTokens($code) {
		$tokens = $this->_client->authenticate($code);
		return json_decode($tokens, true);
	}

}