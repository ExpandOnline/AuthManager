<?php
use Facebook\Facebook;
use Facebook\Exceptions;
use FacebookAds\Api;

App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('FacebookAdsAuthContainer','AuthManager.Lib/Facebook');

/**
 * Class FacebookAdsAuthManager
 */
class FacebookAdsAuthManager extends MediaPlatformAuthManager {

/**
 * @var \Facebook\Facebook
 */
	protected $_facebook;

/**
 * Setup the API.
 */
	public function __construct() {
		parent::__construct();
		Configure::load('AuthManager.API/FacebookAds');
		$this->_setFacebook();
	}

/**
 * Set the facebook account.
 */
	protected function _setFacebook() {
		$this->_facebook = new Facebook(array(
			'app_id' => Configure::read('FacebookAds.app_id'),
			'app_secret' =>  Configure::read('FacebookAds.app_secret'),
			'default_graph_version' => Configure::read('FacebookAds.version')
		));
	}

/**
 * Get the authentication url to add an user.
 *
 * @return string
 */
	public function getAuthUrl() {
		$redirectLoginHelper = $this->_facebook->getRedirectLoginHelper();
		$permissions = array(
			'ads_read',
			'email'
		);
		return $redirectLoginHelper->getLoginUrl(
			Router::url(array(
				'plugin' => 'auth_manager',
				'controller' => 'media_platform_users',
				'action' => 'callback',
				MediaPlatform::FACEBOOK_ADS
			), true),
			$permissions
		);
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
		$accessToken = $this->_getAccessToken();
		if ($accessToken === false) {
			return false;
		}
		$username = $this->_getUsername($accessToken);
		return $this->_saveUser($username, $accessToken);
	}

/**
 * @return bool|\Facebook\Authentication\AccessToken|null
 * @throws Exceptions\FacebookSDKException
 */
	protected function _getAccessToken() {
		$redirectLoginHelper = $this->_facebook->getRedirectLoginHelper();
		try {
			$accessToken = $redirectLoginHelper->getAccessToken();
		} catch(FacebookResponseException $e) {
			CakeLog::write('AuthManager', 'Graph returned an error: ' . $e->getMessage());
			return false;
		} catch(FacebookSDKException $e) {
			CakeLog::write('AuthManager', 'Facebook SDK returned an error: ' . $e->getMessage());
			return false;
		}

		$oAuth2Client = $this->_facebook->getOAuth2Client();
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		try {
			$tokenMetadata->validateAppId(Configure::read('FacebookAds.app_id'));
			$tokenMetadata->validateExpiration();
		} catch (FacebookSDKException $e) {
			CakeLog::write('AuthManager', 'Facebook SDK returned an error: ' . $e->getMessage());
			return false;
		}

		if (!$accessToken->isLongLived()) {
			try {
				$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
			} catch (FacebookSDKException $e) {
				CakeLog::write('AuthManager', 'Error getting long-lived access token: ' . $e->getMessage());
				return false;
			}
		}

		return $accessToken;
	}

/**
 * Get email address or name based on access token.
 *
 * @param AccessToken $accessToken
 *
 * @return mixed
 */
	protected function _getUsername($accessToken) {
		$oAuth2Client = $this->_facebook->getOAuth2Client();
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		$user = $this->_facebook->get('/' . $tokenMetadata->getUserId() . '?fields=name,email', $accessToken)->getGraphUser();
		return $user->getField('email') !== null ? $user->getField('email') : $user->getField('name');
	}

/**
 * @param string $username
 * @param string $accessToken
 *
 * @return mixed
 * @throws Exception
 */
	protected function _saveUser($username, $accessToken) {
		$saveData = array(
			'MediaPlatformUser' => array(
				'username' => $username,
				'media_platform_id' => MediaPlatform::FACEBOOK_ADS
			),
			'OauthToken' => array(
				'access_token' => $accessToken
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

		$facebookAuthContainer = new FacebookAdsAuthContainer();
		$this->_facebook->setDefaultAccessToken($oauthTokens['OauthToken']['access_token']);
		$facebookAuthContainer->facebookSdk = $this->_facebook;
		$facebookAuthContainer->facebookAds = Api::init(Configure::read('FacebookAds.app_id'),
			Configure::read('FacebookAds.app_secret'),
			$oauthTokens['OauthToken']['access_token']
		);

		return $facebookAuthContainer;
	}

}