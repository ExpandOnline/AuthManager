<?php
App::import('Vendor','google/apiclient/src/Google/autoload');
App::uses('GoogleAuthManager','AuthManager.Lib/Google');

/**
 * Class DoubleClickAuthManager
 */
class DoubleClickAuthManager extends GoogleAuthManager {

/**
 * @var string
 */
	protected $_configFile;

/**
 * Get the username for the authenticated user.
 * @return mixed
 */
	protected function _getUserName() {
		$googleOauth = new Google_Service_Oauth2($this->_client);
		return $googleOauth->userinfo->get()->email;
	}

/**
 * Set the google service.
 */
	protected function _setGoogleService() {
		$this->_service = new Google_Service_Dfareporting($this->_client);
	}

/**
 * @return int
 */
	protected function _getPlatformId() {
		return MediaPlatform::DOUBLE_CLICK;
	}

/**
 * @return array
 */
	protected function _getScopes() {
		return array(
			'https://www.googleapis.com/auth/dfareporting',
			'https://www.googleapis.com/auth/dfatrafficking',
			'https://www.googleapis.com/auth/userinfo.email',
		);
	}

/**
 * @return string
 */
	protected function _getConfigFilePath() {
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'doubleClick.json';
	}

}