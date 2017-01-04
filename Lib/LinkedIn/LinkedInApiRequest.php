<?php

/**
 * Class LinkedInApiRequest
 */
class LinkedInApiRequest {

	/**
	 * @var array
	 */
	protected $_options = [];

	/**
	 * @var string
	 */
	protected $_endPoint = '';

	/**
	 * @return array
	 */
	public function getOptions() {
		return $this->_options;
	}

	/**
	 * @param array $options
	 *
	 * @return LinkedInApiRequest
	 */
	public function setOptions($options) {
		$this->_options = $options;

		return $this;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function addOption($name, $value) {
		$this->_options[$name] = $value;
	}

	/**
	 * @return string
	 */
	public function getEndPoint() {
		return $this->_endPoint;
	}

	/**
	 * @param string $endPoint
	 *
	 * @return LinkedInApiRequest
	 */
	public function setEndPoint($endPoint) {
		$this->_endPoint = $endPoint;

		return $this;
	}

}