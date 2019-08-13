<?php
App::uses('TwitterAdsAuthContainer','AuthManager.Lib/TwitterAds');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');

/**
 * Class TwitterAdsAuthManager
 */
class TwitterAdsAuthManager extends MediaPlatformAuthManager {

	/**
	 * @var \Codebird\Codebird
	 */
	private $codebird;

	/**
	 * TwitterAdsAuthManager constructor.
	 */
	public function __construct() {
		parent::__construct();
		Configure::load('AuthManager.API/TwitterAds');
		\Codebird\Codebird::setConsumerKey(
			Configure::read('TwitterAds.consumer_key'),
			Configure::read('TwitterAds.consumer_secret')
		);
		$this->codebird = \Codebird\Codebird::getInstance();
	}

	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		$tokens = $this->codebird->oauth_requestToken([
			'oauth_callback' => $this->_getCallbackUrl(MediaPlatform::TWITTER_ADS)
		]);
		CakeSession::write('oauth_token_secret', $tokens->oauth_token_secret);
		$this->codebird->setToken($tokens->oauth_token, $tokens->oauth_token_secret);
		return $this->codebird->oauth_authorize(true);
	}

	/**
	 * Handles the request when being returned to the AuthManager plugin.
	 *
	 * @param CakeRequest $request
	 *
	 * @return bool
	 */
	public function authenticateUser($request) {
		if (!array_key_exists('oauth_token', $request->query) || !array_key_exists('oauth_verifier', $request->query)) {
			return false;
		}
		$this->codebird->setToken($request->query['oauth_token'], CakeSession::read('oauth_token_secret'));
		$reply = $this->codebird->oauth_accessToken([
			'oauth_verifier' => $request->query['oauth_verifier']
		]);
		if ($reply->httpstatus != 200) {
			return false;
		}
		$agency = $request->query['agency'];
		return $this->_saveUser(
			$reply->screen_name,
			$agency,
			$reply,
			MediaPlatform::TWITTER_ADS
		);
	}

	/**
	 * @param $userId
	 *
	 * @return TwitterAdsAuthContainer
	 */
	public function getAuthContainer($userId) {
		$oauthTokens = $this->MediaPlatformUser->getOauthTokens($userId);
		if (empty($oauthTokens)) {
			throw new NotFoundException('Could not find the oauth tokens for MediaPlatformUser #' . $userId . '.');
		}

		$twitterAdsAuthContainer = new TwitterAdsAuthContainer();
		$this->codebird->setToken(
			$oauthTokens['OauthToken']['access_token'],
			$oauthTokens['OauthToken']['refresh_token']
		);
		$twitterAdsAuthContainer->codebird = $this->codebird;

		return $twitterAdsAuthContainer;
	}

	/**
	 * @param $username
	 * @param $agency
	 * @param $tokens
	 * @param $mediaPlatform
	 *
	 * @return mixed
	 */
	protected function _saveUser($username, $agency, $tokens, $mediaPlatform) {
		$saveData = array(
			'MediaPlatformUser' => array(
				'username' => $username,
				'username' => $agency,
				'media_platform_id' => $mediaPlatform
			),
			'OauthToken' => array(
				'access_token' => $tokens->oauth_token,
				'refresh_token' => $tokens->oauth_token_secret,
			)
		);

		return $this->MediaPlatformUser->saveOauthUser($saveData);
	}

}
