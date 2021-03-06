<?php
App::uses('SearchConsoleAuthManager', 'AuthManager.Lib/SearchConsole');

/**
 * Class SearchConsoleAuthManagerTest
 */
class SearchConsoleAuthManagerTest extends CakeTestCase {

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

		$manager = $this->getMock('SearchConsoleAuthManager', array(
			'_getOauthTokens',
			'_getUserName'
		), array(), '', false);
		$manager->MediaPlatformUser = $MediaPlatformUser;
		$manager->expects($this->once())->method('_getOauthTokens')->will($this->returnValue(array(
			'access_token' => 'access_token_xyz',
			'refresh_token' => 'refresh_token_xyz',
			'created' => strtotime('now'),
			'expires_in' => 3600
		)));

		$manager->expects($this->once())->method('_getUserName')->will($this->returnValue('Sven Witteveen'));

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

		$this->assertEquals(MediaPlatform::SEARCH_CONSOLE, $user['MediaPlatformUser']['media_platform_id']);
		$this->assertEquals('Sven Witteveen', $user['MediaPlatformUser']['username']);
		$this->assertEquals('refresh_token_xyz', $user['OauthToken']['refresh_token']);
		$this->assertEquals('access_token_xyz', $user['OauthToken']['access_token']);
	}

	/**
	 *
	 */
	public function testSetGoogleClient() {
		$wmtAuthManager = new SearchConsoleAuthManager();
		$client = $this->getProtected($wmtAuthManager, '_client');
		/**
		 * @var Google_Client $client
		 */
		$this->assertEquals(array(
			'https://www.googleapis.com/auth/webmasters.readonly',
			'https://www.googleapis.com/auth/userinfo.email'
		), $client->getScopes());
	}

	/**
	 *
	 */
	public function testSetGoogleService() {
		$wmtAuthManager = new SearchConsoleAuthManager();
		$service = $this->getProtected($wmtAuthManager, '_service');
		/**
		 * @var Google_Service_Webmasters $service
		 */
		$this->assertEquals('webmasters', $service->serviceName);
	}

	/**
	 * @param Object $object
	 * @param String $propertyName
	 *
	 * @return mixed Property value
	 */
	public function getProtected($object, $propertyName) {
		$refl = new ReflectionClass($object);
		$property = $refl->getProperty($propertyName);
		$property->setAccessible(true);
		return $property->getValue($object);
	}
}
