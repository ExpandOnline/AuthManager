<?php
App::uses('AuthenticationType', 'AuthManager.Model');
class S11345Dcm extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'S113_45_dcm';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
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
		if ($direction === 'up') {
			$mediaPlatform = ClassRegistry::init('AuthManager.MediaPlatform');
			$mediaPlatform->create();
			return $mediaPlatform->save([
				'id' => MediaPlatform::DOUBLE_CLICK_DCM,
				'name' => 'DoubleClick Campaign Manager',
				'authentication_type_id' => 1
			]);
		}
		return true;
	}
}
