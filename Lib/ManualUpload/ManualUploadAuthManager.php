<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('ManualUploadAuthContainer','AuthManager.Lib/ManualUpload');

/**
 * Class ManualUploadAuthManager
 */
class ManualUploadAuthManager extends MediaPlatformAuthManager {
	public function getAuthUrl() {
		return null;
	}

	public function getAuthContainer($userId) {
		return new ManualUploadAuthContainer();
	}

	public function authenticateUser($request) {
		return true;
	}


}