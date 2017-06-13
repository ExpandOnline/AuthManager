<?php
App::uses('AuthContainer', 'AuthManager.Lib');

class PagespeedAuthContainer extends AuthContainer {

	/**
	 * @var Google_Service_Pagespeedonline
	 */
	public $service;

	public function setService(Google_Service_Pagespeedonline $service){
		$this->service = $service;
	}

	public function getService() {
		return $this->service;
	}
}