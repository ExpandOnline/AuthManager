<?php
App::uses('BingAdsAuthManager', 'AuthManager.Lib/BingAds');

/**
 * Class BingAdsAuthManagerTest
 */
class BingAdsAuthManagerTest extends CakeTestCase {

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

		$manager = $this->getMock('BingAdsAuthManager', array(
			'_getAccessToken',
			'_getUsername'
		), array(), '', false);
		$date = date('Y-m-d H:i:s', strtotime('+1 hour'));
		$manager->MediaPlatformUser = $MediaPlatformUser;
		$options = [
			'access_token' => '1234',
			'refresh_token' => '4321',
			'expires' => strtotime($date),
		];
		$accessToken = new League\OAuth2\Client\Token\AccessToken($options);
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

		$this->assertEquals(MediaPlatform::BING_ADS, $user['MediaPlatformUser']['media_platform_id']);
		$this->assertEquals('Michael van Tricht', $user['MediaPlatformUser']['username']);
		$this->assertEquals($date, $user['OauthToken']['token_expires']);
		$this->assertEquals('1234', $user['OauthToken']['access_token']);
		$this->assertEquals('4321', $user['OauthToken']['refresh_token']);
	}

}
