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
	const DROPBOX = 12;
	const ADWORDS = 13;
	const LINKED_IN = 14;
	const TWITTER_ADS = 15;
	const SALESFORCE = 16;
	const PAGESPEED = 17;

	const PLATFORM_INFO = [
		'analytics_read' => [
			'id' => self::GOOGLE_ANALYTICS_READONLY
		],
		'analytics_read_write' => [
			'id' => self::GOOGLE_ANALYTICS_READWRITE
		],
		'facebook_ads' => [
			'id' => self::FACEBOOK_ADS
		],
		'bitly' => [
			'id' => self::BITLY
		],
		'search_console' => [
			'id' => self::SEARCH_CONSOLE
		],
		'tag_manager' => [
			'id' => self::TAG_MANAGER
		],
		'bing_ads' => [
			'id' => self::BING_ADS
		],
		'linkedin_ads' => [
			'id' => self::LINKED_IN_ADS
		],
		'doubleclick' => [
			'id' => self::DOUBLE_CLICK
		],
		'coosto' => [
			'id' => self::COOSTO
		],
		'instagram' => [
			'id' => self::INSTAGRAM
		],
		'dropbox' => [
			'id' => self::DROPBOX
		],
		'adwords' => [
			'id' => self::ADWORDS
		],
		'linkedin' => [
			'id' => self::LINKED_IN
		],
		'twitter_ads' => [
			'id' => self::TWITTER_ADS
		],
		'salesforce' => [
			'id' => self::SALESFORCE
		],
		'pagespeed' => [
			'id' => self::PAGESPEED
		]
	];

	const GROUPED_PLATFORMS = [
		self::ADWORDS => 'adwords',
		self::LINKED_IN => 'linked_in',
		self::SEARCH_CONSOLE => 'search_console'
	];

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

	public static function isGroupedPlatform($mediaplatform) {
		return array_key_exists($mediaplatform, static::GROUPED_PLATFORMS);
	}
}