<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('SalesforceAuthContainer','AuthManager.Lib/Salesforce');
App::uses('SalesforceAPI','AuthManager.Lib/Salesforce');

use ExpandOnline\Salesforce\Api;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;

/**
 * Class SalesforceAuthManager
 */
class SalesforceAuthManager extends MediaPlatformAuthManager {

	/**
	 * @var Salesforce
	 */
	protected $salesforce;

	/**
	 * SalesforceAuthManager constructor.
	 */
	public function __construct() {
		parent::__construct();
		Configure::load('AuthManager.API/Salesforce');
		$this->salesforce = new Salesforce([
			'clientId'          => Configure::read('Salesforce.client_id'),
			'clientSecret'      => Configure::read('Salesforce.client_secret'),
			'redirectUri'       => $this->_getCallbackUrl(MediaPlatform::SALESFORCE)
		]);
		if (Configure::read('AuthManager.live') !== true) {
			$this->salesforce->setDomain('https://test.salesforce.com');
		}
	}

	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		return $this->salesforce->getAuthorizationUrl();
	}

	/**
	 * Handles the request when being returned to the AuthManager plugin.
	 *
	 * @param CakeRequest $request
	 *
	 * @return bool
	 */
	public function authenticateUser($request) {
		if (!array_key_exists('code', $request->query)) {
			return false;
		}
		$accessToken = $this->salesforce->getAccessToken('authorization_code', [
			'code' => $request->query['code']
		]);
		$owner = $this->salesforce->getResourceOwner($accessToken);
		$agency = $request->query['agency'];
		return $this->_saveUser($owner->toArray()['username'], $agency, $accessToken, MediaPlatform::SALESFORCE);
	}

	/**
	 * @param $username
	 * @param $agency
	 * @param $accessToken
	 * @param $mediaPlatformId
	 *
	 * @return mixed
	 */
	protected function _saveUser($username, $agency, $accessToken, $mediaPlatformId) {
		$saveData = array(
			'MediaPlatformUser' => array(
				'username' => $username,
				'agency' => $agency,
				'media_platform_id' => $mediaPlatformId
			),
			'OauthToken' => array(
				'access_token' => $accessToken->getToken(),
				'refresh_token' => $accessToken->getRefreshToken()
			)
		);

		return $this->MediaPlatformUser->saveOauthUser($saveData);
	}

	/**
	 * @param $userId
	 *
	 * @return SalesforceAuthContainer
	 */
	public function getAuthContainer($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		}
		$salesforceAuthContainer = new SalesforceAuthContainer();
		$salesforce = $this->salesforce;
		$refreshToken = $oauthTokens['OauthToken']['refresh_token'];
		$token = $salesforce->getAccessToken('refresh_token', [
			'refresh_token' => $refreshToken
		]);
		$salesforceAuthContainer->salesforce = new Api($token->getValues()['instance_url'], $token->getToken());
		return $salesforceAuthContainer;
	}

}
