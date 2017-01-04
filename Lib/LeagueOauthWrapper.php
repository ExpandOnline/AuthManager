<?php


class LeagueOauthWrapper {

	public function __construct(\League\OAuth2\Client\Provider\AbstractProvider $provider) {
		$this->_provider = $provider;
	}

	/**
	 * @param $code
	 *
	 * @return \League\OAuth2\Client\Token\AccessToken|boolean
	 */
	public function getAccessToken($code) {
		if (empty($code)) {
			return false;
		}
		try {
			return $this->_provider->getAccessToken('authorization_code', [
				'code' => $code
			]);
		} catch (Exception $e) {
			return false;
		}
	}


	public function getSaveData($username, $token, $mediaPlatform) {
		return [
			'MediaPlatformUser' => [
				'username' => $username,
				'media_platform_id' => $mediaPlatform
			],
			'OauthToken' => [
				'access_token' => $token->getToken(),
				'refresh_token' => $token->getRefreshToken(),
				'token_expires' => date('Y-m-d H:i:s', $token->getExpires()),
			]
		];
	}


	/**
	 * @param $token
	 *
	 * @return mixed
	 *
	 */
	public function refreshToken($token, MediaPlatformUser $mediaPlatformUser) {
		$accessToken = $this->_provider->getAccessToken('refresh_token', [
			'refresh_token' => $token['OauthToken']['refresh_token']
		]);

		return $mediaPlatformUser->updateTokenInDatabase(
			$token['OauthToken']['id'],
			$accessToken->getToken(),
			date('Y-m-d H:i:s', $accessToken->getExpires())
		);
	}
}