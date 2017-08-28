<?php
class S10838Supermetrics extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'S108_38_supermetrics';

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
				'id' => MediaPlatform::SM_LINKED_IN_ADS,
				'name' => 'SuperMetrics',
				'authentication_type_id' => 2
			]);
		}
		return true;
	}
}
