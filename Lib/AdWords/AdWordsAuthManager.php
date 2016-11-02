<?php
App::uses('UpdatedGoogleAuthManager', 'AuthManager.Lib/Google');

/**
 * Class AdWordsAuthManager
 */
class AdWordsAuthManager extends UpdatedGoogleAuthManager {

	/**
	 * @var string
	 */
	protected $_configFile;

	/**
	 * Get the username for the authenticated user.
	 *
	 * @return mixed
	 */
	protected function _getUserName() {
		$service = new Google_Service_Plus($this->_client);
		$user = $service->people->get('me');
		return $user['displayName'];
	}

	/**
	 * Set the google service.
	 */
	protected function _setGoogleService() {
		$this->_service = new AdWordsUser();
	}

	/**
	 * @return int
	 */
	protected function _getPlatformId() {
		return MediaPlatform::ADWORDS;
	}

	/**
	 * @return array
	 */
	protected function _getScopes() {
		return array(
			AdWordsUser::OAUTH2_SCOPE,
			Google_Service_Oauth2::USERINFO_EMAIL
		);
	}

	/**
	 * @return string
	 */
	protected function _getConfigFilePath() {
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'adWords.json';
	}

	/**
	 * @param $userId
	 *
	 * @return GoogleAuthContainer
	 */
	public function getAuthContainer($userId) {
		$authContainer = parent::getAuthContainer($userId);
		$oauthInfo = $authContainer->service->GetOAuth2Info();
		$oauthInfo['access_token'] = $authContainer->client->getOAuth2Service()->getAccessToken();
		$oauthInfo['refresh_token'] = $authContainer->client->getOAuth2Service()->getRefreshToken();
		$authContainer->service->SetOAuth2Info($oauthInfo);
		return $authContainer;
	}


}