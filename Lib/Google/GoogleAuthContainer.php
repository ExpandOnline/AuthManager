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
	 * @var Google_AnalyticsService|Google_Service_Analytics
	 */
	public $service;

}