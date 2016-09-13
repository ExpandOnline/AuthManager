<?php
App::uses('SoapFaultException', 'AuthManager.Lib/Soap');

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
	 * @throws SoapFaultException
	 */
	public function getUser($userId = null) {
		$clientProxy = $this->_getClientProxy(BingAdsApi::CUSTOMER_ENDPOINT, BingAdsApi::VERSION_9);
		$request = new \BingAds\v9\CustomerManagement\GetUserRequest();
		$request->UserId = $userId;

		try {
			return $this->_returnIfPropertyMissing(
				$clientProxy->GetService()->GetUser($request),
				'User'
			);
		} catch (SoapFault $e) {
			throw SoapFaultException::createBySoapFault($e);
		}
	}

	/**
	 * @param null $customerId
	 * @param null $onlyParentAccounts
	 *
	 * @return array
	 * @throws SoapFaultException
	 */
	public function getAccounts($customerId = null, $onlyParentAccounts = null) {
		$clientProxy = $this->_getClientProxy(BingAdsApi::CUSTOMER_ENDPOINT, BingAdsApi::VERSION_9);
		$request = new \BingAds\v9\CustomerManagement\GetAccountsInfoRequest();
		$request->CustomerId = $customerId;
		$request->OnlyParentAccounts = $onlyParentAccounts;

		try {
			return $this->_returnIfPropertyMissing(
				$clientProxy->GetService()->GetAccountsInfo($request)->AccountsInfo,
				'AccountInfo'
			);
		} catch (SoapFault $e) {
			throw SoapFaultException::createBySoapFault($e);
		}
	}

	/**
	 * @param $id
	 *
	 * @return array
	 * @throws SoapFaultException
	 */
	public function getAccount($id) {
		$clientProxy = $this->_getClientProxy(BingAdsApi::CUSTOMER_ENDPOINT, BingAdsApi::VERSION_9);
		$request = new \BingAds\v9\CustomerManagement\GetAccountRequest();
		$request->AccountId = $id;

		try {
			return $this->_returnIfPropertyMissing(
				$clientProxy->GetService()->GetAccount($request),
				'Account'
			);
		} catch (SoapFault $e) {
			throw SoapFaultException::createBySoapFault($e);
		}
	}

	/**
	 * @param $id
	 * @param $campaignType
	 *
	 * @return array
	 * @throws SoapFaultException
	 */
	public function getCampaign($id, $campaignType) {
		$clientProxy = $this->_getClientProxyWithAccountId(
			BingAdsApi::CAMPAIGN_ENDPOINT,
			$id
		);
		$request = new \BingAds\v10\CampaignManagement\GetCampaignsByAccountIdRequest();
		$request->AccountId = $id;
		$request->CampaignType = $campaignType;

		try {
			return $this->_returnIfPropertyMissing(
				$clientProxy->GetService()->GetCampaignsByAccountId($request)->Campaigns,
				'Campaign'
			);
		} catch (SoapFault $e) {
			throw SoapFaultException::createBySoapFault($e);
		}
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
			BingAdsApi::REPORTING_ENDPOINT,
			$accountId,
			BingAdsApi::VERSION_9
		);
		$report = new SoapVar(
			$report, SOAP_ENC_OBJECT, $reportType, $clientProxy->GetNamespace()
		);
		$request = new \BingAds\v9\Reporting\SubmitGenerateReportRequest();
		$request->ReportRequest = $report;

		try {
			return $this->_returnIfPropertyMissing(
				$clientProxy->GetService()->SubmitGenerateReport($request),
				'ReportRequestId',
				false
			);
		} catch (SoapFault $e) {
			throw SoapFaultException::createBySoapFault($e);
		}
	}

	/**
	 * @param $reportRequestId
	 * @param $accountId
	 *
	 * @return array
	 * @throws SoapFaultException
	 */
	public function pollReportRequest($reportRequestId, $accountId) {
		$clientProxy = $this->_getClientProxyWithAccountId(
			BingAdsApi::REPORTING_ENDPOINT,
			$accountId,
			BingAdsApi::VERSION_9
		);
		$request = new \BingAds\v9\Reporting\PollGenerateReportRequest();
		$request->ReportRequestId = $reportRequestId;

		try {
			return $this->_returnIfPropertyMissing(
				$clientProxy->GetService()->PollGenerateReport($request),
				'ReportRequestStatus',
				false
			);
		} catch (SoapFault $e) {
			throw SoapFaultException::createBySoapFault($e);
		}
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
		if (!$this->isUseCache()) {
			return $this->_clientProxyFactory->createClientProxyWithAccountId($endPoint, $accountId, $apiVersion);
		} elseif (isset($this->_clientProxies[$apiVersion][$endPoint][$accountId])) {
			return $this->_clientProxies[$apiVersion][$endPoint][$accountId];
		}

		return $this->_clientProxies[$apiVersion][$endPoint][$accountId]
			= $this->_clientProxyFactory->createClientProxyWithAccountId($endPoint, $accountId, $apiVersion);
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