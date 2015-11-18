<?php
App::uses('BingAdsApi', 'AuthManager.Lib/BingAds');

/**
 * Class ClientProxyFactory
 */
class ClientProxyFactory {

/**
 * @var string
 */
	protected $_apiVersion = BingAdsApi::VERSION_10;

/**
 * @var string
 */
	protected $_accessToken;

/**
 * @var string
 */
	protected $_developerToken;

/**
 * @param $endPoint
 *
 * @return \BingAds\v10\Proxy\ClientProxy|\BingAds\v9\Proxy\ClientProxy
 */
	public function createClientProxy($endPoint) {
		$wsdl = $this->_buildWsdl($endPoint);
		if ($this->_apiVersion == BingAdsApi::VERSION_9) {
			return \BingAds\v9\Proxy\ClientProxy::ConstructWithCredentials(
				$wsdl,
				null,
				null,
				$this->_developerToken,
				$this->_accessToken
			);
		} elseif ($this->_apiVersion == BingAdsApi::VERSION_10) {
			return \BingAds\v10\Proxy\ClientProxy::ConstructWithCredentials(
				$wsdl,
				null,
				null,
				$this->_developerToken,
				$this->_accessToken
			);
		}
	}

/**
 * @param $endPoint
 *
 * @return string
 */
	protected function _buildWsdl($endPoint) {
		return sprintf(BingAdsApi::$wsdlEndPoints[$endPoint], $this->_apiVersion);
	}

/**
 * @return string
 */
	public function getApiVersion() {
		return $this->_apiVersion;
	}

/**
 * @param string $apiVersion
 *
 * @return ClientProxyFactory
 */
	public function setApiVersion($apiVersion) {
		$this->_apiVersion = $apiVersion;

		return $this;
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