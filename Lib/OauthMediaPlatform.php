<?php

/**
 * Interface OauthMediaPlatform
 */
interface OauthMediaPlatform {

/**
 * @param $data
 *
 * @return bool
 */
	public function authenticateUser($data);
}