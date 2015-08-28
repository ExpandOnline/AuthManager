<?php
App::uses('AuthContainer', 'AuthManager.Lib');

/**
 * Class FacebookAdsAuthContainer
 */
class FacebookAdsAuthContainer extends AuthContainer {

/**
 * @var Facebook
 */
	public $facebookSdk;

/**
 * @var \FacebookAds\Api
 */
	public $facebookAds;

}