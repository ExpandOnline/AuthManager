<?php

/**
 * Class BingAdsApiWrapper
 */
class BingAdsApiWrapper {

/**
 * @var null|ClientProxyFactory
 */
	protected $_clientProxyFactory = null;

/**
 * @var array
 */
	protected $_clientProxies = [];

/**
 * @param ClientProxyFactory $clientProxyFactory
 */
	public function __construct(ClientProxyFactory $clientProxyFactory) {
		$this->_clientProxyFactory = $clientProxyFactory;
	}

/**
 * @param null $userId
 *
 * @return mixed
 */
	public function getUser($userId = null) {
		$clientProxy = $this->_getClientProxy(BingAdsApi::CUSTOMER_ENDPOINT, BingAdsApi::VERSION_9);
		$request = new \BingAds\v9\CustomerManagement\GetUserRequest();
		$request->UserId = $userId;

		return $this->_returnIfPropertyMissing(
			$clientProxy->GetService()->GetUser($request),
			'User'
		);
	}

/**
 * @param null $customerId
 * @param null $onlyParentAccounts
 *
 * @return mixed
 */
	public function getAccounts($customerId = null, $onlyParentAccounts = null) {
		$clientProxy = $this->_getClientProxy(BingAdsApi::CUSTOMER_ENDPOINT, BingAdsApi::VERSION_9);
		$request = new \BingAds\v9\CustomerManagement\GetAccountsInfoRequest();
		$request->CustomerId = $customerId;
		$request->OnlyParentAccounts = $onlyParentAccounts;

		return $this->_returnIfPropertyMissing(
			$clientProxy->GetService()->GetAccountsInfo($request)->AccountsInfo,
			'AccountInfo'
		);
	}

/**
 * @param $id
 *
 * @return mixed
 */
	public function getAccount($id) {
		$clientProxy = $this->_getClientProxy(BingAdsApi::CUSTOMER_ENDPOINT, BingAdsApi::VERSION_9);
		$request = new \BingAds\v9\CustomerManagement\GetAccountRequest();
		$request->AccountId = $id;

		return $this->_returnIfPropertyMissing(
			$clientProxy->GetService()->GetAccount($request),
			'Account'
		);
	}

/**
 * @param $id
 * @param $campaignType
 *
 * @return mixed
 */
	public function getCampaign($id, $campaignType) {
		$clientProxy = $this->_getClientProxyWithAccountId(
			BingAdsApi::CAMPAIGN_ENDPOINT,
			$id
		);
		$request = new \BingAds\v10\CampaignManagement\GetCampaignsByAccountIdRequest();
		$request->AccountId = $id;
		$request->CampaignType = $campaignType;

		return $this->_returnIfPropertyMissing(
			$clientProxy->GetService()->GetCampaignsByAccountId($request)->Campaigns,
			'Campaign'
		);
	}

/**
 * @param $report
 * @param $accountId
 * @param $reportType
 *
 * @return array|bool
 */
	public function submitReport($report, $accountId, $reportType) {
		$clientProxy = $this->_getClientProxyWithAccountId(
			BingAdsApi::REPORTING_ENDPOINT,
			$accountId,
			BingAdsApi::VERSION_9
		);
		$report = new SoapVar(
			$report, SOAP_ENC_OBJECT, $reportType, $clientProxy->GetNamespace()
		);
		$request = new \BingAds\v9\Reporting\SubmitGenerateReportRequest();
		$request->ReportRequest = $report;

		return $this->_returnIfPropertyMissing(
			$clientProxy->GetService()->SubmitGenerateReport($request),
			'ReportRequestId',
			false
		);
	}

/**
 * @param $reportRequestId
 * @param $accountId
 *
 * @return array|bool
 */
	public function pollReportRequest($reportRequestId, $accountId) {
		$clientProxy = $this->_getClientProxyWithAccountId(
			BingAdsApi::REPORTING_ENDPOINT,
			$accountId,
			BingAdsApi::VERSION_9
		);
		$request = new \BingAds\v9\Reporting\PollGenerateReportRequest();
		$request->ReportRequestId = $reportRequestId;

		return $this->_returnIfPropertyMissing(
			$clientProxy->GetService()->PollGenerateReport($request),
			'ReportRequestStatus',
			false
		);
	}

/**
 * @param $object
 * @param $propertyName
 * @param $returnIfEmpty
 *
 * @return array
 */
	protected function _returnIfPropertyMissing($object, $propertyName, $returnIfEmpty = []) {
		if (!property_exists($object, $propertyName)) {
			return $returnIfEmpty;
		}

		return $object->{$propertyName};
	}

/**
 * @param        $endPoint
 * @param string $apiVersion
 *
 * @return \BingAds\v10\Proxy\ClientProxy|\BingAds\v9\Proxy\ClientProxy
 */
	protected function _getClientProxy($endPoint, $apiVersion = BingAdsApi::VERSION_10) {
		if (isset($this->_clientProxies[$apiVersion][$endPoint])) {
			return $this->_clientProxies[$apiVersion][$endPoint];
		}

		return $this->_clientProxies[$apiVersion][$endPoint]
			= $this->_clientProxyFactory->createClientProxy($endPoint, $apiVersion);
	}

/**
 * @param        $endPoint
 * @param        $accountId
 * @param string $apiVersion
 *
 * @return \BingAds\v10\Proxy\ClientProxy|\BingAds\v9\Proxy\ClientProxy
 */
	protected function _getClientProxyWithAccountId($endPoint, $accountId, $apiVersion = BingAdsApi::VERSION_10) {
		if (isset($this->_clientProxies[$apiVersion][$endPoint][$accountId])) {
			return $this->_clientProxies[$apiVersion][$endPoint][$accountId];
		}

		return $this->_clientProxies[$apiVersion][$endPoint][$accountId]
			= $this->_clientProxyFactory->createClientProxyWithAccountId($endPoint, $accountId, $apiVersion);
	}

}