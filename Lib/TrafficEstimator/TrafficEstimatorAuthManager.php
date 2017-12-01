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

	/**
	 * @return string
	 */
	protected function _getConfigFilePath() {
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'trafficEstimator.json';
	}

}