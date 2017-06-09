<?php
App::uses('SelfAuthenticationAuthManager', 'AuthManager.Lib/Google');
App::uses('PagespeedAuthContainer', 'AuthManager.Lib/Pagespeed');


class PagespeedAuthManager extends SelfAuthenticationAuthManager {

	/**
	 * @param $userId
	 *
	 * @return PagespeedAuthContainer
	 */
	public function getAuthContainer($userId) {
		$client = new Google_Client();
		$client->setDeveloperKey(PAGESPEED_API_KEY);
		return (new PagespeedAuthContainer())->setService((new Google_Service_Pagespeedonline($client)));
	}


	public function getPlatformName() {
		return 'Pagespeed';
	}

	public function getPlatformType() {
		return MediaPlatform::PAGESPEED;
	}
}