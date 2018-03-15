<?php
class S123171 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'S123_17_1';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'auth_manager_user_credentials' => array(
					'app_token' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1', 'after' => 'password'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'auth_manager_user_credentials' => array('app_token'),
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
		return true;
	}
}
