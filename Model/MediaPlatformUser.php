<?php
App::uses('AuthManagerAppModel', 'AuthManager.Model');

/**
 * Class MediaPlatformUser
 *
 * @property MediaPlatform $MediaPlatform
 */
class MediaPlatformUser extends AuthManagerAppModel {

/**
 * The table is prefixed with 'auth_manager'.
 * @var string
 */
	public $useTable = 'auth_manager_media_platform_users';

/**
 * belongsTo associations.
 * @var array
 */
	public $belongsTo = array(
		'MediaPlatform' => array(
			'className' => 'AuthManager.MediaPlatform',
			'foreignKey' => 'media_platform_id',
		)
	);

/**
 * hasOne associations.
 * @var array
 */
	public $hasOne = array(
		'OauthToken' => array(
			'className' => 'AuthManager.OauthToken',
			'foreignKey' => 'media_platform_user_id'
		)
	);

/**
 * @param array $contain
 *
 * @return array|null
 */
	public function getAllUsers($contain = array()) {
		if (empty($contain)) {
			$contain = array(
				'MediaPlatform.name'
			);
		}
		return $this->find('all', array(
			'contain' => $contain,
			'order' => $this->alias . '.id ASC'
		));
	}

}