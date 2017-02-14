<?php
App::uses('AuthManagerAppModel', 'AuthManager.Model');

/**
 * Class MediaPlatformUser
 *
 * @property MediaPlatform $MediaPlatform
 * @property OauthToken $OauthToken
 */
class MediaPlatformUser extends AuthManagerAppModel {

/**
 * The table is prefixed with 'auth_manager'.
 * @var string
 */
	public $useTable = 'auth_manager_media_platform_users';

/**
 * belongsTo associations.
 * @var array
 */
	public $belongsTo = array(
		'MediaPlatform' => array(
			'className' => 'AuthManager.MediaPlatform',
			'foreignKey' => 'media_platform_id',
		)
	);

/**
 * hasOne associations.
 * @var array
 */
	public $hasOne = array(
		'OauthToken' => array(
			'className' => 'AuthManager.OauthToken',
			'foreignKey' => 'media_platform_user_id'
		)
	);

/**
 * @param array $conditions
 * @param array $contain
 *
 * @return array|null
 */
	public function getAllUsers($conditions = array(), $contain = array()) {
		if (empty($contain)) {
			$contain = array(
				'MediaPlatform.name'
			);
		}
		return $this->find('all', array(
			'contain' => $contain,
			'conditions' => $conditions,
			'order' => $this->alias . '.id ASC'
		));
	}

/**
 * @param $id
 *
 * @return mixed
 */
	public function getOauthTokens($id) {
		return $this->OauthToken->find('first', array(
			'conditions' => array(
				'media_platform_user_id' => $id
			)
		));
	}

/**
 * @param int $oauthTokenId
 * @param string $accessToken
 * @param string $tokenExpires
 *
 * @return mixed
 * @throws Exception
 */
	public function updateTokenInDatabase($oauthTokenId, $accessToken, $tokenExpires) {
		return $this->OauthToken->updateTokenInDatabase($oauthTokenId, $accessToken, $tokenExpires);
	}

/**
 * @param $id
 *
 * @return string
 */
	public function getMediaPlatformId($id) {
		return $this->field('media_platform_id', array(
			'id' => $id
		));
	}

/**
 * @param $mediaPlatformId
 *
 * @return array|null
 */
	public function listUsers($mediaPlatformId) {
		return $this->find('list', [
			'conditions' => !empty($mediaPlatformId) ? ['media_platform_id' => $mediaPlatformId] : null,
			'fields' => [
				'id',
				'username'
			]
		]);
	}

/**
 * Inserts or updates a user with oauth tokens.
 *
 * @param array $data
 *
 * @return mixed
 */
	public function saveOauthUser($data) {
		$mediaPlatformUser = $this->find('first', array(
			'conditions' => array(
				'username' => $data['MediaPlatformUser']['username'],
				'media_platform_id' => $data['MediaPlatformUser']['media_platform_id']
			),
			'contain' => array(
				'OauthToken'
			)
		));
		if (!empty($mediaPlatformUser)) {
			$data['MediaPlatformUser']['id'] = $mediaPlatformUser['MediaPlatformUser']['id'];
			$data['OauthToken']['id'] = $mediaPlatformUser['OauthToken']['id'];
		}
		return $this->saveAssociated($data)
			? (empty($mediaPlatformUser)
				? $this->getLastInsertID()
				: $data['MediaPlatformUser']['id'])
			: false;
	}

/**
 * @param $mediaPlatformId
 *
 * @return array|null
 */
	public function getFirstOfMediaPlatform($mediaPlatformId) {
		return $this->find('first', array(
			'conditions' => array(
				'media_platform_id' => $mediaPlatformId
			)
		));
	}

}