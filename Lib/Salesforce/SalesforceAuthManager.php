<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('SalesforceAuthContainer','AuthManager.Lib/Salesforce');

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
		return $this->_saveUser($owner->toArray()['username'], $accessToken, MediaPlatform::SALESFORCE);
	}

	/**
	 * @param $username
	 * @param $accessToken
	 * @param $mediaPlatformId
	 *
	 * @return mixed
	 */
	protected function _saveUser($username, $accessToken, $mediaPlatformId) {
		$saveData = array(
			'MediaPlatformUser' => array(
				'username' => $username,
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
	 * @return FacebookAdsAuthContainer
	 */
	public function getAuthContainer($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		}
		$salesforceAuthContainer = new SalesforceAuthContainer();
		$salesforceAuthContainer->salesforce = new SalesforceAPI(
			$this->salesforce,
			$oauthTokens['OauthToken']['refresh_token']
		);
		return $salesforceAuthContainer;
	}

}