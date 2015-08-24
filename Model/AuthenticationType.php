<?php
App::uses('AuthManagerAppModel', 'AuthManager.Model');

/**
 * Class AuthenticationType
 */
class AuthenticationType extends AuthManagerAppModel {

/**
 * The table is prefixed with 'auth_manager'.
 * @var string
 */
	public $useTable = 'auth_manager_authentication_types';

/**
 * belongsTo associations.
 * @var array
 */
	public $hasMany = array(
		'MediaPlatform' => array(
			'className' => 'AuthManager.MediaPlatform',
		)
	);

/**
 * Implemented authentication types.
 */
	const OAUTH = 1;

}