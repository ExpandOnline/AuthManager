<?php
App::uses('AuthenticationType', 'AuthManager.Model');
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
			$userCredentials = ClassRegistry::init('AuthManager.UserCredentials');
			$mediaPlatform->delete([
				'id' => 18
			]);
			$mediaPlatform->id = MediaPlatform::LINKED_IN_ADS;
			$mediaPlatform->saveField('authentication_type_id', AuthenticationType::USER_CREDENTIALS);
			$userCredentials->saveEncrypted(
				9,
				'V8XvFzlv5Q',
				null
			);
		}
		return true;
	}
}
