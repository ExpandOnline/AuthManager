<?php


class MockLeague extends \League\OAuth2\Client\Provider\AbstractProvider {

	public function setAccessToken($token) {
		$this->accessToken = $token;
	}

	public function getAccessToken($grant, array $options = []) {
		return $this->accessToken;
	}


	/**
	 * Returns the base URL for authorizing a client.
	 *
	 * Eg. https://oauth.service.com/authorize
	 *
	 * @return string
	 */
	public function getBaseAuthorizationUrl() {
		// TODO: Implement getBaseAuthorizationUrl() method.
	}

	/**
	 * Returns the base URL for requesting an access token.
	 *
	 * Eg. https://oauth.service.com/token
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function getBaseAccessTokenUrl(array $params) {
		// TODO: Implement getBaseAccessTokenUrl() method.
	}

	/**
	 * Returns the URL for requesting the resource owner's details.
	 *
	 * @param \League\OAuth2\Client\Token\AccessToken $token
	 *
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl(\League\OAuth2\Client\Token\AccessToken $token) {
		// TODO: Implement getResourceOwnerDetailsUrl() method.
	}

	/**
	 * Returns the default scopes used by this provider.
	 *
	 * This should only be the scopes that are required to request the details
	 * of the resource owner, rather than all the available scopes.
	 *
	 * @return array
	 */
	protected function getDefaultScopes() {
		// TODO: Implement getDefaultScopes() method.
	}

	/**
	 * Checks a provider response for errors.
	 *
	 * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
	 *
	 * @param  \Psr\Http\Message\ResponseInterface $response
	 * @param  array|string                        $data Parsed response data
	 *
	 * @return void
	 */
	protected function checkResponse(\Psr\Http\Message\ResponseInterface $response, $data) {
		// TODO: Implement checkResponse() method.
	}

	/**
	 * Generates a resource owner object from a successful resource owner
	 * details request.
	 *
	 * @param  array                                   $response
	 * @param  \League\OAuth2\Client\Token\AccessToken $token
	 *
	 * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
	 */
	protected function createResourceOwner(array $response, \League\OAuth2\Client\Token\AccessToken $token) {
		// TODO: Implement createResourceOwner() method.
	}
}