<?php
App::uses('AuthContainer', 'AuthManager.Lib');

/**
 * Class GoogleAuthContainer
 */
class GoogleAuthContainer extends AuthContainer {

	/**
	 * @var Google_Client
	 */
	public $client;

	/**
	 * @var Google_AnalyticsService|Google_Service_Analytics|Google_Service_Dfareporting
	 */
	public $service;

	/** @var  GoogleAuthManager */
	protected $_manager;

	public function setManager(GoogleAuthManager $manager) {
		$this->_manager = $manager;
	}

	public function refresh() {
		if (is_null($this->_manager)) {
			throw new BadMethodCallException('Trying to refresh a Google Auth Container, but no AuthManager was passed via ->setManager so no refresh is possible.');
		}
		$newInstance = $this->_newInstance();
		$this->client->setAccessToken($newInstance->client->getAccessToken());
	}

	protected function _newInstance() {
		return $this->_manager->getAuthContainer($this->userId);
	}

	public function getService($name) {
		return new $name($this->client);
	}
}