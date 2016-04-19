<?php

class S7323 extends CakeMigration {

	/**
	 * Migration description
	 *
	 * @var string
	 */
	public $description = 'S73_23';

	/**
	 * Actions to be performed
	 *
	 * @var array $migration
	 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'auth_manager_user_credentials' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'primary'),
					'media_platform_user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true),
					'username' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'password' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'auth_manager_user_credentials'
			),
		),
	);

	/**
	 * Before migration callback
	 *
	 * @param string $direction Direction of migration process (up or down)
	 *
	 * @return bool Should process continue
	 */
	public function before($direction) {
		return true;
	}

	/**
	 * After migration callback
	 *
	 * @param string $direction Direction of migration process (up or down)
	 *
	 * @return bool Should process continue
	 */
	public function after($direction) {
		if ($direction === 'up') {
			$mediaPlatform = ClassRegistry::init('AuthManager.MediaPlatform');
			$mediaPlatform->create();
			$res = $mediaPlatform->save([
				'name' => 'Coosto',
				'authentication_type_id' => 3
			]);
			$authenticationType = ClassRegistry::init('AuthManager.AuthenticationType');
			$authenticationType->create();
			$res2 = $authenticationType->save([
				'name' => 'LinkedIn Ads 1-user only',
			]);
			$authenticationType = ClassRegistry::init('AuthManager.AuthenticationType');
			$authenticationType->create();
			$res3 = $authenticationType->save([
				'name' => 'Username credentials',
			]);
			return $res && $res2 && $res3;
		}
		return true;
	}
}
