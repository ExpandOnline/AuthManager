<?php
App::uses('FacebookAdsAuthManager', 'AuthManager.Lib/Facebook');

/**
 * Class FacebookAdsAuthManagerTest
 */
class FacebookAdsAuthManagerTest extends CakeTestCase {

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

		$manager = $this->getMock('FacebookAdsAuthManager', array(
			'_getAccessToken',
			'_getUsername'
		), array(), '', false);
		$manager->MediaPlatformUser = $MediaPlatformUser;
		$accessToken = new \Facebook\Authentication\AccessToken('testAccessToken', strtotime('2015-10-27 11:26:00'));
		$manager->expects($this->once())->method('_getAccessToken')->will($this->returnValue($accessToken));
		$manager->expects($this->once())->method('_getUserName')->will($this->returnValue('Michael van Tricht'));

		$request = new Object();
		$request->query = array('code' => 'xyz', 'agency' => 'DMNL');
		$manager->authenticateUser($request);
		$user = $MediaPlatformUser->find('first', array(
			'conditions' => array(
				'MediaPlatformUser.id' => $MediaPlatformUser->getLastInsertID()
			),
			'contain' => array(
				'OauthToken'
			)
		));

		$this->assertEquals(MediaPlatform::FACEBOOK_ADS, $user['MediaPlatformUser']['media_platform_id']);
		$this->assertEquals('Michael van Tricht', $user['MediaPlatformUser']['username']);
		$this->assertEquals('2015-10-27 11:26:00', $user['OauthToken']['token_expires']);
		$this->assertEquals('testAccessToken', $user['OauthToken']['access_token']);
	}

/**
 *
 */
	public function testSendEventIfTokenExpiresInTwoWeeks() {
		$oldEventManager = CakeEventManager::instance();
		CakeEventManager::instance(new CakeEventManager());
		$manager = $this->getMock('FacebookAdsAuthManager', null, array(), '', false);
		$this->assertTrue($manager->testProtected('_sendEventIfTokenExpiresInTwoWeeks', array(
			1,
			date('Y-m-d H:i:s')
		)));
		$this->assertFalse($manager->testProtected('_sendEventIfTokenExpiresInTwoWeeks', array(
			1,
			date('Y-m-d H:i:s', strtotime('+3 weeks'))
		)));
		$this->assertTrue($manager->testProtected('_sendEventIfTokenExpiresInTwoWeeks', array(
			1,
			date('Y-m-d H:i:s', strtotime('+2 weeks'))
		)));
		CakeEventManager::instance($oldEventManager);
	}

}
