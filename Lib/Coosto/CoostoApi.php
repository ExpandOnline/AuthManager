<?php
use GuzzleHttp\Client;

/**
 * Class CoostoApi
 */
class CoostoApi {

	/**
	 * @var GuzzleHttp\Client
	 */
	protected $_client;

	/**
	 * With trailing slash
	 *
	 * @var string
	 */
	protected $_apiEndpoint = 'https://in.coosto.com/api/{version}/';

	/**
	 * @var string
	 */
	protected $_apiVersion = '1';

	/**
	 * @var array
	 */
	protected $_defaultQueryStrings = [];
	
	/**
	 * CoostoApi constructor.
	 */
	public function __construct() {
		$this->_client = new Client([
			'base_url' => [
				$this->_apiEndpoint,
				[
					'version' => $this->_apiVersion
				]
			],
			'defaults' => [
				'headers' => [
					'Accept' => 'application/json'
				]
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
	 * @param CoostoApiRequest $request
	 *
	 * @return mixed
	 */
	public function get(CoostoApiRequest $request) {
		return $this->_client->get($request->getEndPoint(), [
			'query' => array_merge($this->_defaultQueryStrings, $request->getOptions())
		])->json();
	}

	/**
	 * @param CoostoApiRequest $request
	 *
	 * @return mixed
	 */
	public function post(CoostoApiRequest $request) {
		return $this->_client->post($request->getEndPoint(), [
			'json' => $request->getOptions(),
			'query' => $this->_defaultQueryStrings
		])->json();
	}

}