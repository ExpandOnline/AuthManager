<?php

class S1437 extends CakeMigration {

	/**
	 * Migration description
	 *
	 * @var string
	 */
	public $description = 'S143_7';

	/**
	 * Actions to be performed
	 *
	 * @var array $migration
	 */
	public $migration = array(
		'up' => array(),
		'down' => array(),
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
			return $mediaPlatform->save([
				'name' => 'DoubleClick Search Ads',
				'authentication_type_id' => 1
			]);
		}
		return true;
	}
}
