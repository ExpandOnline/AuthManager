<?php

use Microsoft\BingAds\Auth\ApiEnvironment;
use Microsoft\BingAds\Auth\AuthorizationData;
use Microsoft\BingAds\Auth\ServiceClient;

App::uses('BingAdsApi', 'AuthManager.Lib/BingAds');

/**
 * Class ClientProxyFactory
 */
class ClientProxyFactory {

	/**
	 * @var string
	 */
	protected $_accessToken;

	/**
	 * @var string
	 */
	protected $_developerToken;

	/**
	 * @param $serviceClientType
	 *
	 * @return ServiceClient
	 */
	public function createClientProxy($serviceClientType) {
		return new ServiceClient($serviceClientType, $this->_createAuthorization(), ApiEnvironment::Production);
	}

	/**
	 * @param $serviceClientType
	 * @param $accountId
	 *
	 * @return ServiceClient
	 */
	public function createClientProxyWithAccountId($serviceClientType, $accountId) {
		$authorizationData = $this->_createAuthorization();
		$authorizationData->withAccountId($accountId);
		return new ServiceClient($serviceClientType, $authorizationData, ApiEnvironment::Production);
	}

	/**
	 * @return AuthorizationData
	 */
	protected function _createAuthorization() {
		$authenticationData = (new \Microsoft\BingAds\Auth\OAuthWebAuthCodeGrant())
			->withOAuthTokens((object) ['AccessToken' => $this->_accessToken]);
		return (new AuthorizationData())
			->withAuthentication($authenticationData)
			->withDeveloperToken($this->_developerToken);
	}

	/**
	 * @return string
	 */
	public function getAccessToken() {
		return $this->_accessToken;
	}

	/**
	 * @param string $accessToken
	 *
	 * @return ClientProxyFactory
	 */
	public function setAccessToken($accessToken) {
		$this->_accessToken = $accessToken;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDeveloperToken() {
		return $this->_developerToken;
	}

	/**
	 * @param string $developerToken
	 *
	 * @return ClientProxyFactory
	 */
	public function setDeveloperToken($developerToken) {
		$this->_developerToken = $developerToken;

		return $this;
	}


}