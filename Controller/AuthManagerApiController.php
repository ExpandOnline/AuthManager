<?php

App::uses('Controller', 'Controller');
App::uses('ApiExceptionFactory', 'CakePHPUtil.Lib/Api/Exceptions');
App::uses('JsonApiResponse', 'CakePHPUtil.Lib/Api/Response');
App::uses('AuthManagerApiScope', 'AuthManager.Lib/Api/Scopes');
App::uses('ApiScopeValidator', 'CakePHPUtil.Lib/Api/Scopes');

/**
 * Class CIAApiController
 *
 */
class AuthManagerApiController extends Controller {

	/**
	 * @var array
	 */
	protected $_scopesForAction = [];

	/**
	 * @var array
	 */
	public $components = [
		'Auth' => [
			'authenticate' => ['CakePHPUtil.ApiQuery'],
			'authorize' => ['Controller']
		]
	];

	/**
	 * This will always run before every action
	 */
	public function beforeFilter() {
		// Cake tries to store sessions for user. We don't want this!
		AuthComponent::$sessionKey = false;

		// Don't use the default Cake renderer since we're returning JSON
		$this->autoRender = false;
		$this->response->type('json');
	}

	/**
	 * @param $user
	 *
	 * @return bool
	 * @throws InvalidApiAuthorizationException
	 */
	public function isAuthorized($user) {
		if (!$this->_checkScopes($this->_getRequiredScopesForAction($this->request->params['action']))) {
			throw ApiExceptionFactory::invalidAuthorizationException('Token is missing required scope(s)');
		}
		return true;
	}

	/**
	 * @param $requiredScopes
	 *
	 * @return bool
	 */
	protected function _checkScopes($requiredScopes) {
		return ApiScopeValidator::hasScopes($this->Auth->user()['token']->getRawScopes(), $requiredScopes);
	}

	/**
	 * @param $action
	 *
	 * @return array
	 */
	protected function _getRequiredScopesForAction($action) {

		$requiredScopes = [];

		if (array_key_exists($action, $this->_scopesForAction)) {
			foreach ($this->_scopesForAction as $requiredScope) {
				$requiredScopes[] = $requiredScope;
			}
		} else {
			$requiredScope = $this->_getNewScopeClass();
			$requiredScopeVarFunc = 'set' . ($action === 'index' ? 'Read' : ucfirst($action));
			$requiredScopes[] = $requiredScope->{$requiredScopeVarFunc}();
		}

		return $requiredScopes;
	}

	/**
	 * @return AuthManagerApiScope
	 */
	protected function _getNewScopeClass() {
		return new AuthManagerApiScope();
	}

}