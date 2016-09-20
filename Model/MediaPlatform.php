<?php
App::uses('AuthManagerAppModel', 'AuthManager.Model');

/**
 * Class MediaPlatform
 *
 * @property MediaPlatformUser $MediaPlatformUser
 */
class MediaPlatform extends AuthManagerAppModel {


/**
 * Implemented media platforms.
 */
	const GOOGLE_ANALYTICS_READONLY = 1;
	const GOOGLE_ANALYTICS_READWRITE = 2;
	const FACEBOOK_ADS = 3;
	const BITLY = 4;
	const SEARCH_CONSOLE = 5;
	const TAG_MANAGER = 6;
	const BING_ADS = 7;
	const LINKED_IN_ADS = 8;
	const DOUBLE_CLICK = 9;
	const COOSTO = 10;
	const INSTAGRAM = 11;

/**
 * The table is prefixed with 'auth_manager'.
 * @var string
 */
	public $useTable = 'auth_manager_media_platforms';

/**
 * hasMany associations.
 * @var array
 */
	public $hasMany = array(
		'MediaPlatformUser' => array(
			'className' => 'AuthManager.MediaPlatformUser',
			'foreignKey' => 'media_platform_id',
			'dependent' => true,
		)
	);

/**
 * belongsTo associations.
 * @var array
 */
	public $belongsTo = array(
		'AuthenticationType' => array(
			'className' => 'AuthManager.AuthenticationType',
			'foreignKey' => 'authentication_type_id',
			'dependent' => true,
		)
	);


/**
 * @return array|null
 */
	public function listActive() {
		return $this->find('list', array(
			'fields' => array(
				'id',
				'name'
			),
			'order' => 'id ASC'
		));
	}

/**
 * @param $id
 *
 * @return string
 */
	public function getAuthenticationType($id) {
		return $this->field('authentication_type_id', array(
			'id' => $id
		));
	}

	/**
	 * @param $id
	 *
	 * @return string
	 */
	public function getName($id) {
		return $this->field('name', [
			'id' => $id
		]);
	}

}