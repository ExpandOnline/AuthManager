<?php
App::uses('OauthAuthenticationType','AuthManager.Lib/AuthenticationTypes');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('GoogleAnalyticsAuthContainer','AuthManager.Lib/GoogleAnalytics');

/**
 * Class GoogleAnalyticsAuthManager
 */
abstract class GoogleAnalyticsAuthManager extends MediaPlatformAuthManager implements OauthAuthenticationType {

/**
 * @var Google_Client
 */
	protected $_client;

/**
 * @var Google_AnalyticsService|Google_Service_Analytics
 */
	protected $_service;

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
		$mediaPlatformUser = $this->MediaPlatformUser->find('first', array(
			'conditions' => array(
				'username' => $username,
				'media_platform_id' => $mediaPlatformId
			),
			'contain' => array(
				'OauthToken'
			)
		));
		if (!empty($mediaPlatformUser)) {
			$saveData['MediaPlatformUser']['id'] = $mediaPlatformUser['MediaPlatformUser']['id'];
			$saveData['OauthToken']['id'] = $mediaPlatformUser['OauthToken']['id'];
		}
		return $this->MediaPlatformUser->saveAssociated($saveData);
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
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser # ' . $userId . '.');
		} elseif (strtotime($oauthTokens['OauthToken']['token_expires']) < (time() + 60000)) {
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

		$authContainer = new GoogleAnalyticsAuthContainer();
		$authContainer->client = $this->_client;
		$authContainer->service = $this->_service;
		return $authContainer;
	}

}