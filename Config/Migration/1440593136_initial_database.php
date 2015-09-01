<?php
class InitialDatabase extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'Initial_database';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'auth_manager_authentication_types' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
				'auth_manager_media_platform_users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'media_platform_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
					'username' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'auth_manager_media_platforms' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'authentication_type_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
				'auth_manager_oauth_tokens' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'media_platform_user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
					'access_token' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'refresh_token' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'token_expires' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'auth_manager_authentication_types', 'auth_manager_media_platform_users', 'auth_manager_media_platforms', 'auth_manager_oauth_tokens'
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if($direction === "up") {
			$mediaPlatform = ClassRegistry::init('AuthManager.MediaPlatform');
			$platforms[0]['MediaPlatform']['name'] = 'Analytics (readonly)';
			$platforms[0]['MediaPlatform']['authentication_type_id'] = '1';
			$platforms[0]['MediaPlatform']['created'] = '2015-08-21';
			$platforms[0]['MediaPlatform']['modified'] = '2015-08-21';

			$platforms[1]['MediaPlatform']['name'] = 'Analytics';
			$platforms[1]['MediaPlatform']['authentication_type_id'] = '1';
			$platforms[1]['MediaPlatform']['created'] = '2015-08-21';
			$platforms[1]['MediaPlatform']['modified'] = '2015-08-21';

			$platforms[2]['MediaPlatform']['name'] = 'Facebook Ads';
			$platforms[2]['MediaPlatform']['authentication_type_id'] = '1';
			$platforms[2]['created'] = '2015-08-21';
			$platforms[2]['modified'] = '2015-08-21';

			$mediaPlatform->create();
			if($mediaPlatform->saveAll($platforms)) {
				$this->callback->out('MediaPlatform table has been initialized.');
			}

			$authenticationType = ClassRegistry::init('AuthManager.AuthenticationType');
			$type['name'] = 'OAUTH2';
			$type['created'] = '2015-08-24';
			$type['modified'] = '2015-08-24';

			$authenticationType->create();
			if($authenticationType->save($type)){
				$this->callback->out('AuthenticationTypes table has been initialized');
			}


		}
		return true;
	}
}
