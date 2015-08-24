<?php

/**
 * Class GoogleAnalyticsAuthContainer
 */
class GoogleAnalyticsAuthContainer extends AuthContainer {

/**
 * @var Google_Client
 */
	public $client;

/**
 * @var Google_AnalyticsService|Google_Service_Analytics
 */
	public $service;

}