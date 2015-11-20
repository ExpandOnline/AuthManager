<?php
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

	protected $_clientProxies = [
		BingAdsApi::VERSION_9 => '\BingAds\v9\Proxy\ClientProxy',
		BingAdsApi::VERSION_10 => '\BingAds\v10\Proxy\ClientProxy',
	];

/**
 * @param        $endPoint
 * @param string $apiVersion
 *
 * @return \BingAds\v10\Proxy\ClientProxy|\BingAds\v9\Proxy\ClientProxy
 */
	public function createClientProxy($endPoint, $apiVersion = BingAdsApi::VERSION_10) {
		$wsdl = $this->_buildWsdl($endPoint, $apiVersion);
		return forward_static_call(array($this->_clientProxies[$apiVersion], 'ConstructWithCredentials'),
			$wsdl,
			null,
			null,
			$this->_developerToken,
			$this->_accessToken
		);
	}

/**
 * @param        $endPoint
 * @param        $accountId
 * @param string $apiVersion
 *
 * @return \BingAds\v10\Proxy\ClientProxy|\BingAds\v9\Proxy\ClientProxy
 */
	public function createClientProxyWithAccountId($endPoint, $accountId, $apiVersion = BingAdsApi::VERSION_10) {
		$wsdl = $this->_buildWsdl($endPoint, $apiVersion);
		return forward_static_call(array($this->_clientProxies[$apiVersion], 'ConstructWithAccountId'),
			$wsdl,
			null,
			null,
			$this->_developerToken,
			$accountId,
			$this->_accessToken
		);
	}

/**
 * @param $endPoint
 * @param $apiVersion
 *
 * @return string
 */
	protected function _buildWsdl($endPoint, $apiVersion) {
		return sprintf(BingAdsApi::$wsdlEndPoints[$endPoint], $apiVersion);
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