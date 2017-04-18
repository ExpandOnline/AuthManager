<?php
use GuzzleHttp\Client;
use Stevenmaguire\OAuth2\Client\Provider\Salesforce;

/**
 * Class SalesforceAPI
 */
class SalesforceAPI {

	/**
	 * @var Salesforce
	 */
	protected $oauth;

	/**
	 * SalesforceAPI constructor.
	 *
	 * @param Salesforce $salesforce
	 * @param            $refreshToken
	 */
	public function __construct(Salesforce $salesforce, $refreshToken) {
		$this->oauth = $salesforce;
		$this->client = new Client([
			'base_uri' => 'https://eu11.lightning.force.com/services/data/v39.0/',
			'headers' => [
				'Authorization' => 'Bearer ' . $this->refreshTokenAndGetAccessToken($refreshToken)
			]
		]);
	}

	/**
	 * @return string
	 */
	protected function refreshTokenAndGetAccessToken($refreshToken) {
		return $this->oauth->getAccessToken('refresh_token', [
			'refresh_token' => $refreshToken
		])->getToken();
	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		return call_user_func_array([$this->client, $name], $arguments);
	}

}