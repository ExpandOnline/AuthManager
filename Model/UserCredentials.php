<?php
App::uses('AuthManagerAppModel', 'AuthManager.Model');

/**
 * Class OauthToken
 */
class UserCredentials extends AuthManagerAppModel {

	/**
	 * The table is prefixed with 'auth_manager'.
	 * @var string
	 */
	public $useTable = 'auth_manager_user_credentials';

	/**
	 * belongsTo associations.
	 * @var array
	 */
	public $belongsTo = [
		'MediaPlatformUser' => [
			'className' => 'AuthManager.MediaPlatformUser',
			'foreignKey' => 'media_platform_user_id'
		]
	];

	/**
	 * @param      $mediaPlatformUserId
	 * @param      $username
	 * @param      $password
	 *
	 * @param null $appToken
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function saveEncrypted($mediaPlatformUserId, $username, $password, $appToken = null) {
		$this->create();
		return $this->save([
			'media_platform_user_id' => $mediaPlatformUserId,
			'username' => $username,
			'password' => openssl_encrypt(
				$password,
				Configure::read('UserCredentials.encryptionMethod'),
				Configure::read('UserCredentials.key')
			),
			'app_token' => $appToken,
		]);
	}

	/**
	 * @param $mediaPlatformUserId
	 *
	 * @return array|null
	 */
	public function getCredentials($mediaPlatformUserId) {
		$userCredentials = $this->find('first', [
			'conditions' => [
				'media_platform_user_id' => $mediaPlatformUserId
			]
		]);
		$userCredentials['UserCredentials']['password'] = openssl_decrypt(
			$userCredentials['UserCredentials']['password'],
			Configure::read('UserCredentials.encryptionMethod'),
			Configure::read('UserCredentials.key')
		);
		
		return $userCredentials;
	}

}