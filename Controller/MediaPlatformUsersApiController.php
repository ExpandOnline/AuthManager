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

	/**
	 * @return string
	 */
	public function index() {
		$mediaPlatformId = $this->request->query('media_platform');
		$users = $this->MediaPlatformUser->getUsers($mediaPlatformId);

		$response = [];
		foreach ($users as $user) {
			$response[] = [
				'id' => $user['MediaPlatformUser']['id'],
				'name' => $user['MediaPlatformUser']['username'],
				'media_platform_id' => $user['MediaPlatformUser']['media_platform_id'],
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