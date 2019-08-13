<?php

/**
 * Class MediaPlatformUserTest
 *
 * @property MediaPlatformUser $MediaPlatformUser
 */
class MediaPlatformUserTest extends CakeTestCase {

	/**
	 * @var array
	 */
	public $fixtures = array(
		'plugin.AuthManager.MediaPlatformUser',
		'plugin.AuthManager.OAuthToken',
	);

	/**
	 *
	 */
	public function setUp() {
		parent::setUp();
		$this->MediaPlatformUser = ClassRegistry::init('AuthManager.MediaPlatformUser');
	}

	/**
	 *
	 */
	public function tearDown() {
		unset($this->MediaPlatformUser);
		parent::tearDown();
	}

	/**
	 *
	 */
	public function testSaveOauthUser() {
		$cases = [
			[
				'data' => [
					'MediaPlatformUser' => [
						'username' => 'testing',
						'media_platform_id' => MediaPlatform::FACEBOOK_ADS,
						'agency' => 'DMNL'
					]
				],
				'expected' => $this->MediaPlatformUser->find('count') + 1
			],
			[
				'data' => [
					'MediaPlatformUser' => [
						'username' => 'testing',
						'media_platform_id' => MediaPlatform::FACEBOOK_ADS,
						'agency' => 'BA'
					]
				],
				'expected' => $this->MediaPlatformUser->find('count') + 1
			]
		];

		foreach ($cases as $case) {
			$this->assertEquals($case['expected'], $this->MediaPlatformUser->saveOauthUser($case['data']));
		}
	}

	/**
	 *
	 */
	public function testGetFirstOfMediaPlatform() {
		$cases = [
			[
				'id' => MediaPlatform::FACEBOOK_ADS,
				'expected_id' => '1'
			],
			[
				'id' => MediaPlatform::INSTAGRAM,
				'expected_id' => '4'
			],
		];
		foreach ($cases as $case) {
			$user = $this->MediaPlatformUser->getFirstOfMediaPlatform($case['id']);
			$this->assertEquals($case['expected_id'], $user['MediaPlatformUser']['id']);
		}
	}

	/**
	 *
	 */
	public function testListUsers() {
		$cases = [
			[
				'id' => MediaPlatform::FACEBOOK_ADS,
				'expected_count' => 2
			],
			[
				'id' => MediaPlatform::INSTAGRAM,
				'expected_count' => 1
			],
			[
				'id' => MediaPlatform::LINKED_IN_ADS,
				'expected_count' => 1
			],
			[
				'id' => MediaPlatform::COOSTO,
				'expected_count' => 0
			],
		];
		foreach ($cases as $case) {
			$list = $this->MediaPlatformUser->listUsers($case['id']);
			$this->assertCount($case['expected_count'], $list);
		}
	}

}
