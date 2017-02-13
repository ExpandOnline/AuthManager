<?php

App::uses('AppControllerTest', 'Test/Case/Controller');
App::uses('ApiToken', 'CakePHPUtil.Lib/Api');

/**
 * Class MockApiAuthControllerTestCase
 */
abstract class AuthManagerApiControllerTestCase extends ControllerTestCase {

	/**
	 * @param string $accountId
	 */
	protected function _mockAuth($accountId = "*") {
		$this->controller = $this->generate($this->getControllerName(), array(
			'components' => array(
				'Auth' => array('user')
			)
		));
		$token = new ApiToken($accountId);
		$token->setScopes($this->getRequiredScopes());
		$this->controller->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnValue([
				'token' => $token
			]));
	}

	/**
	 * @return mixed
	 */
	protected abstract function getControllerName();

	/**
	 * @return mixed
	 */
	protected abstract function getRequiredScopes();
}