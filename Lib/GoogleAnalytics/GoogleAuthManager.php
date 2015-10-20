<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('GoogleAnalyticsAuthContainer','AuthManager.Lib/GoogleAnalytics');

/**
 * Class GoogleAnalyticsAuthManager
 */
abstract class GoogleAuthManager extends MediaPlatformAuthManager {

/**
 * @var Google_Client
 */
	protected $_client;

/**
 * @var Google_AnalyticsService|Google_Service_Analytics
 */
	protected $_service;

/**
 * Get the authorization URL to redirect to.
 *
 * @return string
 */
	public function getAuthUrl() {
		return $this->_client->createAuthUrl();
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

/**
 * @param $userId
 *
 * @return GoogleAnalyticsAuthContainer
 */
	public function getAuthContainer($userId) {
		$this->_setTokenOnClient($userId);
		$authContainer = new GoogleAnalyticsAuthContainer();
		$authContainer->client = $this->_client;
		$authContainer->service = $this->_service;
		return $authContainer;
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
		$webProperties = $this->_service->management_webproperties->listManagementWebproperties("~all");
		return $this->_saveUser($webProperties['username'], $oauthTokens, $this->_getPlatformId());
	}

/**
 * @return int
 */
	protected abstract function _getPlatformId();

	/**
	 * @param $userId
	 */
	public function _setTokenOnClient($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		} elseif (strtotime($oauthTokens['OauthToken']['token_expires']) < (time() + 600)
			|| Configure::read('debug') >= 1
		) {
			$this->_client->refreshToken($oauthTokens['OauthToken']['refresh_token']);
			$token = json_decode($this->_client->getAccessToken());
			$this->MediaPlatformUser->updateTokenInDatabase($oauthTokens['OauthToken']['id'], $token->access_token,
				date('Y-m-d H:i:s', ($token->created + $token->expires_in)));
		} else {
			$token = json_encode(array(
				'access_token' => $oauthTokens['OauthToken']['access_token'],
				'expires_in' => 3600,
				'created' => strtotime($oauthTokens['OauthToken']['token_expires']) - 3600
			));

			$this->_client->setAccessToken($token);
		}
	}
}