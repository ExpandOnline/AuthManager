<?php
App::uses('AuthManagerController', 'AuthManager.Controller');
App::uses('MediaPlatformAuthManagerFactory', 'AuthManager.Lib');
App::uses('AuthenticationType', 'AuthManager.Model');

/**
 * Class MediaPlatformUsersController
 *
 * @property MediaPlatformUser $MediaPlatformUser
 */
class MediaPlatformUsersController extends AuthManagerController {

/**
 * Displays the table with MediaPlatformUsers.
 */
	public function index() {
		$this->set('mediaPlatformUsers', $this->MediaPlatformUser->getAllUsers());
		$this->set('mediaPlatforms', $this->MediaPlatformUser->MediaPlatform->listActive());
	}

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
		$authenticationType = $this->MediaPlatformUser->MediaPlatform->getAuthenticationType($mediaPlatformId);
		$response = false;
		switch ($authenticationType) {
			case AuthenticationType::OAUTH:
				$response = $mediaPlatformAuthManager->authenticateUser($this->request->query);
				break;
			default:
				throw new InvalidArgumentException('Unknown authentication type #' . $authenticationType);
		}
		if ($response) {
			$this->Session->setFlash(__d('AuthManager', 'Succesvol geauthenticeerd!'), 'successbox');
		} else {
			$this->Session->setFlash(__d('AuthManager', 'Er ging iets mis bij het authenticeren!'), 'errorbox');
		}
		$this->redirect(array(
			'action' => 'index'
		));
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