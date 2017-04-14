<?php
App::uses('AuthContainer', 'AuthManager.Lib');

/**
 * Class SalesforceAuthContainer
 */
class SalesforceAuthContainer extends AuthContainer {

	/**
	 * @var SalesforceAPI
	 */
	public $salesforce;

}