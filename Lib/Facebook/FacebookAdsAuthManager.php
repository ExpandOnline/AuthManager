<?php

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\Exceptions;
use FacebookAds\Api;

App::uses('FacebookAdsAuthContainer','AuthManager.Lib/Facebook');
App::uses('CakePersistentData','AuthManager.Lib/Facebook');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');

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
			'default_graph_version' => Configure::read('FacebookAds.version'),
			'persistent_data_handler' => new CakePersistentData(),
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
			'email',
			'business_management',
			'ads_management'
		);
		return $redirectLoginHelper->getLoginUrl(
			$this->_getCallbackUrl(MediaPlatform::FACEBOOK_ADS),
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
		return $this->_saveUser($username, $accessToken, MediaPlatform::FACEBOOK_ADS);
	}

/**
 * @param                                      $username
 * @param \Facebook\Authentication\AccessToken $accessToken
 * @param                                      $mediaPlatformId
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
				'access_token' => $accessToken->getValue(),
				'token_expires' => $this->_getExpirationDate($accessToken)
			)
		);

		return $this->MediaPlatformUser->saveOauthUser($saveData);
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
		$this->_sendEventIfTokenExpiresInTwoWeeks($userId, $oauthTokens['OauthToken']['token_expires']);

		return $facebookAuthContainer;
	}


/**
 * @param int $userId
 * @param string $tokenExpiresAt
 *
 * @return bool
 */
	protected function _sendEventIfTokenExpiresInTwoWeeks($userId, $tokenExpiresAt) {
		if (strtotime($tokenExpiresAt) > strtotime('+2 weeks')) {
			return false;
		}
		$event = new CakeEvent('AuthManager.FacebookAdsAuthManager.tokenExpiresInTwoWeeks', $this, array(
			'media_platform_user_id' => $userId
		));
		CakeEventManager::instance()->dispatch($event);

		return true;
	}

	/**
	 * @param $accessToken
	 *
	 * @return mixed
	 */
	protected function _getExpirationDate($accessToken) {
		$expirationDate = $accessToken->getExpiresAt();
		// If getExpiresAt is null, it means the token will never expire!
		if (is_null($expirationDate)) {
			$expirationDate = new DateTime('+10 years');
		}

		return $expirationDate->format('Y-m-d H:i:s');
	}

}