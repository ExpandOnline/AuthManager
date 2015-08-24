<?php
App::uses('AuthManagerAppModel', 'AuthManager.Model');

/**
 * Class OauthToken
 */
class OauthToken extends AuthManagerAppModel {

/**
 * The table is prefixed with 'auth_manager'.
 * @var string
 */
	public $useTable = 'auth_manager_oauth_tokens';

/**
 * belongsTo associations.
 * @var array
 */
	public $belongsTo = array(
		'MediaPlatformUser' => array(
			'className' => 'AuthManager.MediaPlatformUser',
			'foreignKey' => 'media_platform_user_id'
		)
	);

}