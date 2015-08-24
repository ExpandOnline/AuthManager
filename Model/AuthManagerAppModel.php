<?php
App::uses('AppModel', 'Model');

/**
 * Class AuthManagerAppModel
 */
class AuthManagerAppModel extends AppModel {

/**
 * @var int
 */
	public $recursive = -1;

/**
 * @param       $methodName
 * @param array $args
 *
 * @return mixed
 */
	public function testProtected($methodName, array $args) {
		return call_user_func_array(array($this, $methodName), $args);
	}

}
