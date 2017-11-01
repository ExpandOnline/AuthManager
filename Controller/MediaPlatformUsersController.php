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

	function beforeFilter() {
		$this->Auth->allow( 'callback', 'createByPlatform' );
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
		$mediaPlatformUserId = $mediaPlatformAuthManager->authenticateUser($this->request);
		if ($mediaPlatformUserId) {
			$this->Session->setFlash(__d('AuthManager', 'Successfully authenticated!'), 'successbox');
			$this->_newUserEvent($mediaPlatformId, $mediaPlatformUserId);
		} else {
			$this->Session->setFlash(__d('AuthManager', 'There went something wrong authenticating you!'), 'errorbox');
		}
		$this->_redirectToLastSavedReferrer();
	}


	public function createByPlatform($platform) {
		if (!($platformId = MediaPlatform::PLATFORM_INFO[$platform]['id'] ?? false)) {
			throw new UnknownMediaPlatformException(
				"Platform '{$platform}' does not exist. Available platforms: "
				. implode(", ", array_keys(MediaPlatform::PLATFORM_INFO))
			);
		}

		$this->Session->write('AuthManager.referrer', $this->request->query['redirect_url']);

		$url = $this->_getAuthManager($platformId)->getAuthUrl();
		$this->redirect($url);
	}


	/**
	 * @param $mediaPlatformId
	 * @param $mediaPlatformUserId
	 */
	protected function _newUserEvent($mediaPlatformId, $mediaPlatformUserId) {
		$event = new CakeEvent('AuthManager.MediaPlatformUser.new', $this, array(
			'media_platform_id' => $mediaPlatformId,
			'media_platform_user_id' => $mediaPlatformUserId
		));
		CakeEventManager::instance()->dispatch($event);
	}

	/**
	 * @param $mediaPlatformId
	 *
	 * @return MediaPlatformAuthManager
	 */
	protected function _getAuthManager($mediaPlatformId) {
		return $this->getContainer()->get('authmanager.media_platform.auth_manager_factory')->createAuthManager($mediaPlatformId);
	}

}