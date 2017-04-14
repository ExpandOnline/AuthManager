<?php

class S9924 extends CakeMigration {

	/**
	 * Migration description
	 *
	 * @var string
	 */
	public $description = 'S99_24';

	/**
	 * Actions to be performed
	 *
	 * @var array $migration
	 */
	public $migration = [
		'up' => [],
		'down' => []
	];

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
				'id' => MediaPlatform::SALESFORCE,
				'name' => 'Salesforce',
				'authentication_type_id' => 1
			]);
		}
		return true;
	}
}
