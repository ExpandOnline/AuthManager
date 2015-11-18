<?php

/**
 * Class BingAdsApi
 */
class BingAdsApi {

/**
 * Versions
 */
	const VERSION_9 = 'v9';
	const VERSION_10 = 'v10';

/**
 * Endpoints
 */
	const CUSTOMER_ENDPOINT = 1;

/**
 * @var array
 */
	public static $wsdlEndPoints = [
		BingAdsApi::CUSTOMER_ENDPOINT
			=> 'https://clientcenter.api.bingads.microsoft.com/Api/CustomerManagement/%s/CustomerManagementService.svc?singleWsdl'
	];

}