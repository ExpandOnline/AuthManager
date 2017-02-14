<?php
App::uses('BaseApiScope', 'CakePHPUtil.Lib/Api/Scopes');

class MediaPlatformUsersApiScope extends BaseApiScope {
	/**
	 * @return string
	 */
	public function getName() {
		return 'auth.users';
	}
}