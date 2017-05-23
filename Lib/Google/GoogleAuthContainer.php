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
	private $_manager;

	public function setManager(GoogleAuthManager $manager) {
		$this->_manager = $manager;
	}

	public function refresh() {
		$newInstance = $this->_newInstance();
		$this->client->setAccessToken($newInstance->client->getAccessToken());
	}

	private function _newInstance() {
		return $this->_manager->getAuthContainer($this->userId);
	}
}