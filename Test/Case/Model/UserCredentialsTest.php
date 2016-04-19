<?php

/**
 * Class UserCredentialsTest
 *
 * @property UserCredentials $UserCredentials
 */
class UserCredentialsTest extends CakeTestCase {

	/**
	 * @var array
	 */
	public $fixtures = array(
		'plugin.AuthManager.UserCredentials'
	);

	/**
	 *
	 */
	public function setUp() {
		parent::setUp();
		$this->UserCredentials = ClassRegistry::init('AuthManager.UserCredentials');
	}

	/**
	 *
	 */
	public function tearDown() {
		unset($this->UserCredentials);
		parent::tearDown();
	}

	/**
	 * 
	 */
	public function testUserCredentials() {
		$beforeCount = $this->UserCredentials->find('count');
		Configure::write('UserCredentials.key', 'mysecretkey');
		$savedRow = $this->UserCredentials->saveEncrypted(10, 'username', 'password');
		$this->assertEquals($beforeCount + 1, $this->UserCredentials->find('count'));
		$this->assertNotEquals('password', $savedRow['UserCredentials']['password']);
		$this->assertNotEquals('password', base64_decode($savedRow['UserCredentials']['password']));

		$userCredentialsDecrypted = $this->UserCredentials->getCredentials(10);
		$this->assertEquals('password', $userCredentialsDecrypted['UserCredentials']['password']);
	}

}