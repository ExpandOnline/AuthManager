<?php

use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

App::uses('GoogleAuthContainer', 'AuthManager.Lib/Google');

/**
 * Class AdWordsAuthContainer
 */
class AdWordsAuthContainer extends GoogleAuthContainer {

	/**
	 * @var Google_Client
	 */
	public $client;

	/**
	 * @var AdWordsServices
	 */
	public $service;

	/**
	 * @var AdWordsSessionBuilder
	 */
	public $sessionBuilder;

	/**
	 * @param $name
	 * @param $clientCustomerId
	 *
	 * @return mixed
	 */
	public function getService($name, $clientCustomerId = EXPAND_LIVE_MCC_CLIENT_CUSTOMER_ID) {
		return $this->service->get($this->getSession($clientCustomerId), $name);
	}

	/**
	 * @param $clientCustomerId
	 *
	 * @return AdWordsSession|mixed
	 */
	public function getSession($clientCustomerId) {
		return $this->sessionBuilder
			->withClientCustomerId($clientCustomerId)
			->withSoapLogger(new Logger('void', [new NullHandler()]))
			->withReportDownloaderLogger(new Logger('void', [new NullHandler()]))
			->build();
	}
}