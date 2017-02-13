<?php

App::uses('AuthManagerApiController', 'AuthManager.Controller');
App::uses('ApiExceptionFactory', 'CakePHPUtil.Lib/Api/Exceptions');
App::uses('JsonApiResponse', 'CakePHPUtil.Lib/Api/Response');
App::uses('MediaPlatformUsersApiScope', 'AuthManager.Lib/Api/Scopes');
/**
 * Class MediaPlatformUsersApiController
 *
 * @property MediaPlatformUser $MediaPlatformUser
 */
class MediaPlatformUsersApiController extends AuthManagerApiController {
	public $uses = ['AuthManager.MediaPlatformUser'];

	public function index() {
		$mediaPlatformId = $this->request->query('media_platform');
		$users = $this->MediaPlatformUser->listUsers($mediaPlatformId);

		$response = [];
		foreach($users as $id => $name) {
			$response[] = [
				'id' => $id,
				'name' => $name
			];
		}

		return JsonApiResponse::data($response);
	}

	/**
	 * @return MediaPlatformUsersApiScope
	 */
	protected function _getNewScopeClass() {
		return new MediaPlatformUsersApiScope();
	}
}