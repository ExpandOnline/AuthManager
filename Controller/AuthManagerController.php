<?php
App::uses('AppController', 'Controller');

/**
 * Class AuthManagerController
 */
class AuthManagerController extends AppController {

/**
 * @var array
 */
	public $helpers = array(
		'TableHelper.Table' => array(
			'createOptions' => array(
				'class' => 'table table-striped table-hover table-condensed'
			)
		),
		'Form'
	);

}
