<?php
App::uses('AuthContainer', 'AuthManager.Lib');

/**
 * Class DropboxAuthContainer
 */
class DropboxAuthContainer extends AuthContainer {

	/**
	 * @var \Dropbox\Dropbox
	 */
	public $dropbox;
}