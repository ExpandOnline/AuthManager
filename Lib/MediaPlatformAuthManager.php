<?php
App::uses('MediaPlatformUser','AuthManager.Model');
App::uses('MediaPlatform','AuthManager.Model');

/**
 * Class MediaPlatformAuthManager
 */
abstract class MediaPlatformAuthManager {

/**
 * @var MediaPlatformUser
 */
	public $MediaPlatformUser;

/**
 * Initiate the MediaPlatformUser account.
 */
	public function __construct() {
		$this->MediaPlatformUser = ClassRegistry::init('AuthManager.MediaPlatformUser');
	}

/**
 * @return string
 */
	public abstract function getAuthUrl();

}