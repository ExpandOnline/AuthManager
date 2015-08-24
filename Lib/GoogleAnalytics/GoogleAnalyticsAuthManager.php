<?php
App::uses('OauthMediaPlatform','AuthManager.Lib');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');

/**
 * Class GoogleAnalyticsAuthManager
 */
abstract class GoogleAnalyticsAuthManager extends MediaPlatformAuthManager implements OauthMediaPlatform {

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

}