<?php

/**
 * Class S6821
 */
class S6821 extends CakeMigration {

	/**
	 * Migration description
	 *
	 * @var string
	 */
	public $description = 'S68_21';

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
			$MediaPlatform = ClassRegistry::init('AuthManager.MediaPlatform');

			return $MediaPlatform->save([
				'id' => 9,
				'name' => 'DoubleClick'
			]);
		}

		return true;
	}
}
