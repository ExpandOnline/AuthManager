<?php
App::uses('InstagramAuthManager', 'AuthManager.Lib/Instagram');

/**
 * Class InstagramAuthManagerTest
 */
class InstagramAuthManagerTest extends CakeTestCase {

	/**
	 * @var array
	 */
	public $fixtures = array(
		'plugin.AuthManager.MediaPlatformUser',
		'plugin.AuthManager.OAuthToken'
	);

	/**
	 *
	 */
	public function testAuthenticateUser() {
		$MediaPlatformUser = $this->getMockForModel('AuthManager.MediaPlatformUser', null);

		$manager = $this->getMock('InstagramAuthManager', array(
			'_getAccessToken'
		), array(), '', false);
		$manager->MediaPlatformUser = $MediaPlatformUser;
		$accessToken = (object) [
			'access_token' => 'heyo',
			'user' => (object) [
				'username' => 'my username'
			]
		];
		$manager->expects($this->once())->method('_getAccessToken')->will($this->returnValue($accessToken));

		$request = new Object();
		$request->query = array('code' => 'xyz');
		$manager->authenticateUser($request);
		$user = $MediaPlatformUser->find('first', array(
			'contain' => array(
				'OauthToken'
			),
			'order' => 'MediaPlatformUser.id DESC'
		));

		$this->assertEquals(MediaPlatform::INSTAGRAM, $user['MediaPlatformUser']['media_platform_id']);
		$this->assertEquals('my username', $user['MediaPlatformUser']['username']);
		$this->assertEquals('heyo', $user['OauthToken']['access_token']);
	}

}