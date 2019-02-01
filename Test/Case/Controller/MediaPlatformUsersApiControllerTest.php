<?php
App::uses('JsonApiException', 'CakePHPUtil.Lib/Api/Exceptions');
App::uses('AuthManagerApiControllerTestCase', 'AuthManager.Test/Case/Controller');
App::uses('MediaPlatformUsersApiController', 'AuthManager.Controller');

/**
 * Class DataApiControllerTest
 */
class MediaPlatformUsersApiControllerTest extends AuthManagerApiControllerTestCase {

	public $fixtures = [
		'plugin.AuthManager.MediaPlatform',
		'plugin.AuthManager.MediaPlatformUser',
		'app.CakeSession'
	];

	/**
	 *
	 */
	public function setUp() {
		parent::setUp();
		$this->_mockAuth("*");
	}

	/**
	 *
	 */
	public function testReadWithInvalidId() {
		$result = json_decode($this->testAction('/AuthManager/api/1/users?media_platform=1123123123'));

		$this->assertEmpty($result->data);
	}

	/**
	 *
	 */
	public function testReadWithValidId() {
		$result = json_decode($this->testAction('/AuthManager/api/1/users?media_platform=3'), true);
		$this->assertSame(
			[
				[
					'id' => '1',
					'name' => 'test@test.com',
					'media_platform_id' => '3',
				],
				[
					'id' => '2',
					'name' => 'test@test.com',
					'media_platform_id' => '3',
				]
			],
			$result['data']
		);
	}

	/**
	 *
	 */
	public function testWithoutMediaPlatform() {
		$result = json_decode($this->testAction('/AuthManager/api/1/users'), true);
		$this->assertSame(
			[
				[
					'id' => '1',
					'name' => 'test@test.com',
					'media_platform_id' => '3',
				],
				[
					'id' => '2',
					'name' => 'test@test.com',
					'media_platform_id' => '3',
				],
				[
					'id' => '3',
					'name' => 'test@linkedin.com',
					'media_platform_id' => '8',
				],
				[
					'id' => '4',
					'name' => 'test@linkedin.com',
					'media_platform_id' => '11',
				],
			],
			$result['data']
		);
	}

	/**
	 * @return mixed
	 */
	protected function getControllerName() {
		return 'AuthManager.MediaPlatformUsersApi';
	}

	/**
	 * @return mixed
	 */
	protected function getRequiredScopes() {
		return [(new MediaPlatformUsersApiScope())->setRead()];
	}
}