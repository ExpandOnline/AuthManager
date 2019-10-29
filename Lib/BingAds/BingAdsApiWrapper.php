<?php

use Microsoft\BingAds\Auth\ServiceClientType;
use Microsoft\BingAds\V13\CampaignManagement\GetCampaignsByAccountIdRequest;
use Microsoft\BingAds\V13\CustomerManagement\GetAccountRequest;
use Microsoft\BingAds\V13\CustomerManagement\GetAccountsInfoRequest;
use Microsoft\BingAds\V13\CustomerManagement\GetUserRequest;
use Microsoft\BingAds\V13\Reporting\PollGenerateReportRequest;
use Microsoft\BingAds\V13\Reporting\SubmitGenerateReportRequest;

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
	 * @var bool
	 */
	protected $_useCache = true;

/**
 * @param ClientProxyFactory $clientProxyFactory
 */
	public function __construct(ClientProxyFactory $clientProxyFactory) {
		$this->_clientProxyFactory = $clientProxyFactory;
	}

	/**
	 * @param null $userId
	 *
	 * @return array
	 */
	public function getUser($userId = null) {
		$clientProxy = $this->_getClientProxy(ServiceClientType::CustomerManagementVersion12);
		$request = new GetUserRequest();
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
	 * @return array
	 */
	public function getAccounts($customerId = null, $onlyParentAccounts = null) {
		$clientProxy = $this->_getClientProxy(ServiceClientType::CustomerManagementVersion12);
		$request = new GetAccountsInfoRequest();
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
	 * @return array
	 */
	public function getAccount($id) {
		$clientProxy = $this->_getClientProxy(ServiceClientType::CustomerManagementVersion12);
		$request = new GetAccountRequest();
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
	 * @return array
	 */
	public function getCampaign($id, $campaignType) {
		$clientProxy = $this->_getClientProxyWithAccountId(
			ServiceClientType::CampaignManagementVersion12,
			$id
		);
		$request = new GetCampaignsByAccountIdRequest();
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
	 * @return array
	 * @throws SoapFaultException
	 */
	public function submitReport($report, $accountId, $reportType) {
		$clientProxy = $this->_getClientProxyWithAccountId(
			ServiceClientType::ReportingVersion13,
			$accountId
		);
		$report = new SoapVar(
			$report, SOAP_ENC_OBJECT, $reportType, $clientProxy->GetNamespace()
		);
		$request = new SubmitGenerateReportRequest();
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
	 * @return array
	 */
	public function pollReportRequest($reportRequestId, $accountId) {
		$clientProxy = $this->_getClientProxyWithAccountId(
			ServiceClientType::ReportingVersion12,
			$accountId
		);
		$request = new PollGenerateReportRequest();
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
 *
 * @return \Microsoft\BingAds\Auth\ServiceClient
 */
	protected function _getClientProxy($endPoint) {
		if (isset($this->_clientProxies[$endPoint])) {
			return $this->_clientProxies[$endPoint];
		}

		return $this->_clientProxies[$endPoint]
			= $this->_clientProxyFactory->createClientProxy($endPoint);
	}

/**
 * @param        $endPoint
 * @param        $accountId
 *
 * @return \Microsoft\BingAds\Auth\ServiceClient
 */
	protected function _getClientProxyWithAccountId($endPoint, $accountId) {
		if (!$this->isUseCache()) {
			return $this->_clientProxyFactory->createClientProxyWithAccountId($endPoint, $accountId);
		} elseif (isset($this->_clientProxies[$endPoint][$accountId])) {
			return $this->_clientProxies[$endPoint][$accountId];
		}

		return $this->_clientProxies[$endPoint][$accountId]
			= $this->_clientProxyFactory->createClientProxyWithAccountId($endPoint, $accountId);
	}

	/**
	 * @return boolean
	 */
	public function isUseCache() {
		return $this->_useCache;
	}

	/**
	 * @param boolean $useCache
	 *
	 * @return BingAdsApiWrapper
	 */
	public function setUseCache($useCache) {
		$this->_useCache = $useCache;
		return $this;
	}

}
