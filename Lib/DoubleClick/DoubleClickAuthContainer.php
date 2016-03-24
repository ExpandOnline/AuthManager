<?php
App::uses('GoogleAuthContainer', 'AuthManager.Lib/Google');

/**
 * Class DoubleClickAuthContainer
 */
class DoubleClickAuthContainer extends GoogleAuthContainer {

	/**
	 * @var Google_Service_DoubleClickBidManager
	 */
	public $dbmService;

}