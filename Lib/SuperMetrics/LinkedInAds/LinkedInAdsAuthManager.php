<?php
App::uses('SuperMetricsAuthManager','AuthManager.Lib/SuperMetrics');

/**
 * Class LinkedInAdsAuthManager
 */
class LinkedInAdsAuthManager extends SuperMetricsAuthManager {

	/**
	 * @return mixed
	 */
	protected function getMediaPlatform() {
		return MediaPlatform::LINKED_IN_ADS;
	}
}