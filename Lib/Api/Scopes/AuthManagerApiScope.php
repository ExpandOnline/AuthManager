<?php
App::uses('BaseApiScope', 'CakePHPUtil.Lib/Api/Scopes');

class AuthManagerApiScope extends BaseApiScope {
	/**
	 * @return string
	 */
	public function getName() {
		return 'auth';
	}
}