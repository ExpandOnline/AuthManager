<?php
App::uses('Controller', 'Controller');

/**
 * Class AuthManagerController
 */
class AuthManagerAppController extends Controller {

/**
 * Save the referrer in the session.
 */
	protected function _saveReferrer() {
		$this->Session->write('AuthManager.referrer', $this->referer());
	}

/**
 * Pop the last saved referrer from the session and redirect to it.
 * Falls back to '/' when none set.
 */
	protected function _redirectToLastSavedReferrer() {
		$referrer = $this->Session->read('AuthManager.referrer') === null
			? '/'
			: $this->Session->read('AuthManager.referrer');
		$this->Session->delete('AuthManager.referrer');
		$this->redirect($referrer);
	}

}
