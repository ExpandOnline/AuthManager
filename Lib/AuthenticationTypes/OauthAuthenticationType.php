<?php

/**
 * Interface OauthMediaPlatform
 */
interface OauthAuthenticationType {

/**
 * @param $data
 *
 * @return bool
 */
	public function authenticateUser($data);
}