<?php
App::uses('AuthManagerController', 'AuthManager.Controller');
App::uses('MediaPlatformAuthManagerFactory', 'AuthManager.Lib');
App::uses('AuthenticationType', 'AuthManager.Model');

/**
 * Class MediaPlatformUsersController
 * This controller class handles adding new users to the AuthManager.
 * We use the AuthManager
 *
 * @property MediaPlatformUser $MediaPlatformUser
 */
class MediaPlatformUsersController extends AuthManagerAppController {

/**
 * Authorize an account, which can be either a new or existing account.
 */
	public function addUser() {
		if (!$this->_addUserDataComplete()) {
			throw new InvalidArgumentException('Missing data to add an user!');
		}
		$mediaPlatformId = $this->request->data['MediaPlatform']['id'];
		/**
		 * @var MediaPlatformAuthManager $mediaPlatformAuthManager
		 */
		$mediaPlatformAuthManager = $this->_getAuthManager($mediaPlatformId);
		$this->_saveReferrer();
		$this->redirect($mediaPlatformAuthManager->getAuthUrl());
	}

/**
 * Does the POST data have all data required to add an user?
 *
 * @return bool
 */
	protected function _addUserDataComplete() {
		return array_key_exists('MediaPlatform', $this->request->data)
			&& array_key_exists('id', $this->request->data['MediaPlatform']);
	}

/**
 * Callback from the authentication URL.
 *
 * @param $mediaPlatformId
 *
 * @throws Exception
 */
	public function callback($mediaPlatformId) {
		/**
		 * @var MediaPlatformAuthManager $mediaPlatformAuthManager
		 */
		$mediaPlatformAuthManager = $this->_getAuthManager($mediaPlatformId);
		if ($mediaPlatformAuthManager->authenticateUser($this->request)) {
			$this->Session->setFlash(__d('AuthManager', 'Successfully authenticated!'), 'successbox');
		} else {
			$this->Session->setFlash(__d('AuthManager', 'There went something wrong authenticating you!'), 'errorbox');
		}
		$this->_redirectToLastSavedReferrer();
	}

/**
 * @param $mediaPlatformId
 *
 * @return MediaPlatformAuthManager
 */
	protected function _getAuthManager($mediaPlatformId) {
		$mediaPlatformAuthManagerFactory = new MediaPlatformAuthManagerFactory();
		return $mediaPlatformAuthManagerFactory->createAuthManager($mediaPlatformId);
	}

}