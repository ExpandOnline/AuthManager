<?php

/**
 * Class MediaPlatformUserFixture
 */
class MediaPlatformUserFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('model' => 'AuthManager.MediaPlatformUser');

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'media_platform_id' => 3,
			'username' => 'test@test.com'
		),
		array(
			'id' => '2',
			'media_platform_id' => 3,
			'username' => 'test@test.com'
		),
		array(
			'id' => '3',
			'media_platform_id' => 8,
			'username' => 'test@linkedin.com'
		),
		array(
			'id' => '4',
			'media_platform_id' => 11,
			'username' => 'test@linkedin.com'
		)
	);

}
