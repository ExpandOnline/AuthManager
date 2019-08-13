<?php
class S15845 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'S158_45';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'auth_manager_media_platform_users' => array(
					'agency' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'modified'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'auth_manager_media_platform_users' => array('agency'),
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
