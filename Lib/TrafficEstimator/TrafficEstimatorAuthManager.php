<?php
App::uses('AdWordsAuthManager', 'AuthManager.Lib/AdWords');

/**
 * Class AdWordsAuthManager
 */
class TrafficEstimatorAuthManager extends AdWordsAuthManager {


	/**
	 * @return int
	 */
	protected function _getPlatformId() {
		return MediaPlatform::TRAFFIC_ESTIMATOR;
	}

}