<?php
App::uses('AuthenticationType', 'AuthManager.Model');
class S11531TrafficEstimator extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'S11531_traffic_estimator';

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
				'id' => MediaPlatform::TRAFFIC_ESTIMATOR,
				'name' => 'Traffic Estimator (AdWords)',
				'authentication_type_id' => 1
			]);
		}
		return true;
	}
}
