<?php

use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\Auth\OAuth2;

App::uses('UpdatedGoogleAuthManager', 'AuthManager.Lib/Google');
App::uses('AdWordsAuthContainer', 'AuthManager.Lib/AdWords');

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
		$this->_service = new AdWordsServices();
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
			'https://www.googleapis.com/auth/adwords',
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
	 * @return AdWordsAuthContainer
	 */
	public function getAuthContainer($userId) {
		$this->_setTokenOnClient($userId);
		$authContainer = new AdWordsAuthContainer();
		$authContainer->client = $this->_client;
		$authContainer->service = $this->_service;
		$authContainer->userId = $userId;
		$authContainer->setManager($this);
		$oauth2 = new OAuth2([
			'clientId' => $authContainer->client->getClientId(),
			'clientSecret' => $authContainer->client->getClientSecret(),
		]);
		$oauth2->setAccessToken($authContainer->client->getAccessToken()['access_token']);
		$authContainer->sessionBuilder = (new AdWordsSessionBuilder())
			->withOAuth2Credential($oauth2)
			->withDeveloperToken(ADWORDS_DEVELOPER_TOKEN);
		return $authContainer;
	}


}