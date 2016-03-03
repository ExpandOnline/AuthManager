<?php
App::uses('GoogleAuthManager','AuthManager.Lib/Google');

/**
 * Class UpdatedGoogleAuthManager
 */
abstract class UpdatedGoogleAuthManager extends GoogleAuthManager {

	/**
	 * Get OAUTH tokens based on the given code.
	 *
	 * @param $code
	 *
	 * @return array
	 */
	protected function _getOauthTokens($code) {
		return $this->_client->authenticate($code);
	}

	/**
	 * @param $oauthTokens
	 */
	protected function _refreshTokens($oauthTokens) {
		$this->_client->refreshToken($oauthTokens['OauthToken']['refresh_token']);
		$token = $this->_client->getAccessToken();
		$this->MediaPlatformUser->updateTokenInDatabase($oauthTokens['OauthToken']['id'], $token->access_token,
			date('Y-m-d H:i:s', ($token->created + $token->expires_in)));
	}

}