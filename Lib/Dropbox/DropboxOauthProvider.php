<?php

use Guzzle\Http\Exception\BadResponseException;
use League\OAuth2\Client\Exception\IDPException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Token\AccessToken;


/**
 * Class DropboxOauthProvider
 *
 * This DropboxOauthProvider is partially copied from: https://github.com/pixelfear/oauth2-dropbox
 * But sadly that repo does not support league 0.12.1, it only supports 0.8.1
 * And only dropbox API version 1
 */
class DropboxOauthProvider extends AbstractProvider {

	// TODO: Fix this class
	public $uidKey = 'account_id';

	/**
	 * @return string
	 */
	public function urlAuthorize() {
		return 'https://www.dropbox.com/oauth2/authorize';
	}

	/**
	 * @return string
	 */
	public function urlAccessToken() {
		return 'https://api.dropboxapi.com/oauth2/token';
	}

	/**
	 * @param AccessToken $token
	 *
	 * @return string
	 */
	public function urlUserDetails(\League\OAuth2\Client\Token\AccessToken $token) {
		return 'https://api.dropboxapi.com/2/users/get_account';
	}

	/**
	 * @param object      $response
	 * @param AccessToken $token
	 *
	 * @return User
	 */
	public function userDetails($response, \League\OAuth2\Client\Token\AccessToken $token) {
		$user = new User;
		$user->email = $response->email;

		return $user;
	}

	/**
	 * @param array $options
	 *
	 * @return string
	 */
	public function getAuthorizationUrl($options = array()) {
		return parent::getAuthorizationUrl(array_merge([
			'approval_prompt' => []
		], $options));
	}

	/**
	 * @param AccessToken $token
	 *
	 * @return \Guzzle\Http\EntityBodyInterface|string
	 */
	protected function fetchUserDetails(AccessToken $token) {
		$url = $this->urlUserDetails($token);

		$headers = $this->getHeaders($token);

		return $this->getAccount($url, $token->uid, $headers);
	}

	/**
	 * @param       $url
	 * @param       $userId
	 * @param array $headers
	 *
	 * @return \Guzzle\Http\EntityBodyInterface|string
	 * @throws IDPException
	 */
	protected function getAccount($url, $userId, array $headers = []) {
		try {
			$client = $this->getHttpClient();
			$client->setBaseUrl($url);

			if ($headers) {
				$client->setDefaultOption('headers', array_merge($headers, ['Content-Type' => 'application/json']));
			}

			$request = $client->createRequest('POST', null, null, json_encode([
				'account_id' => $userId
			]))->send();
			$response = $request->getBody();
		} catch (BadResponseException $e) {
			// @codeCoverageIgnoreStart
			$response = $e->getResponse()->getBody();
			$result = $this->prepareResponse($response);
			throw new IDPException($result);
			// @codeCoverageIgnoreEnd
		}

		return $response;
	}

}