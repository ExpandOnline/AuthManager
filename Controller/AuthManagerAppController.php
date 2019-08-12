<?php
App::uses('Controller', 'Controller');

/**
 * Class AuthManagerController
 */
class AuthManagerAppController extends Controller {

	/**
	 * @var array
	 */
	public $components = [
		'CakePHPUtil.ContainerBuilder'
	];

	/**
	 * Save the referrer in the session.
	 */
	protected function _saveReferrer() {
		$this->Session->write('AuthManager.referrer', $this->referer());
	}

	/**
	 * Pop the last saved referrer from the session and redirect to it.
	 * Falls back to '/' when none set.
	 *
	 * @param null $hash
	 */
	protected function _redirectToLastSavedReferrer($hash = null, $time = null, $id = null) {
		$url = '/';

		if ($this->Session->read('AuthManager.referrer')) {
			$url = $this->Session->read('AuthManager.referrer');
			if ($hash && $time && $id) {
				$url = $this->addQueryParam($url, 'code', $hash);
				$url = $this->addQueryParam($url, 't', $time);
				$url = $this->addQueryParam($url, 'id', $id);
			}
		}

		$this->Session->delete('AuthManager.referrer');
		$this->redirect($url);
	}

	protected function addQueryParam($url, $key, $value) {
		$query = parse_url($url, PHP_URL_QUERY);

		if ($query) {
			return $url . '&' . $key . '=' . $value;
		}

		return $url . '?' . $key . '=' . $value;
	}

	protected function getContainer(): \Symfony\Component\DependencyInjection\ContainerInterface {
		return $this->ContainerBuilder->getContainer();
	}

	/**
	 * Save Agency in the session.
	 */
	protected function _saveAgency($agency){
		$this->Session->write('AuthManager.agency', $agency);
	}

	/**
	 * Get Agency out of the session.
	 */
	protected function _getAgency(){
		$agency = $this->Session->read('AuthManager.agency');
		return $agency;
	}
}
