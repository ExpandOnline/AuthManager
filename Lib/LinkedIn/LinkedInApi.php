<?php


use GuzzleHttp\Client;

class LinkedInApi {

	/**
	 * @var GuzzleHttp\Client
	 */
	protected $_client;

	/**
	 * With trailing slash
	 *
	 * @var string
	 */
	protected $_apiEndpoint = 'https://api.linkedin.com/%s/';

	/**
	 * @var string
	 */
	protected $_apiVersion = 'v1';

	/**
	 * @var array
	 */
	protected $_defaultQueryStrings = [
		'format' => 'json'
	];

	/**
	 * LinkedInApi constructor.
	 */
	public function __construct($token) {
		$this->_client = new Client([
			'base_uri' => sprintf($this->_apiEndpoint, $this->_apiVersion),
			'headers' => [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . $token
			]
		]);
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function addDefaultQueryString($name, $value) {
		$this->_defaultQueryStrings[$name] = $value;
	}


	/**
	 * @param LinkedInApiRequest $request
	 *
	 * @return mixed
	 */
	public function get(LinkedInApiRequest $request) {
		return json_decode($this->_client->get($request->getEndPoint(), [
			'query' => array_merge($this->_defaultQueryStrings, $request->getOptions())
		])->getBody()->getContents(), true);
	}
}