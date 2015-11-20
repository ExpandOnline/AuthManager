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
	const CAMPAIGN_ENDPOINT = 2;

/**
 * @var array
 */
	public static $wsdlEndPoints = [
		BingAdsApi::CUSTOMER_ENDPOINT
			=> 'https://clientcenter.api.bingads.microsoft.com/Api/CustomerManagement/%s/CustomerManagementService.svc?singleWsdl',
		BingAdsApi::CAMPAIGN_ENDPOINT
			=> 'https://campaign.api.bingads.microsoft.com/Api/Advertiser/CampaignManagement/%s/CampaignManagementService.svc?singleWsdl'
	];

}