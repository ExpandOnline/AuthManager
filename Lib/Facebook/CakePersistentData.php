<?php

use Facebook\PersistentData\PersistentDataInterface;

/**
 * Class CakePersistentData
 */
class CakePersistentData implements PersistentDataInterface {

	/**
	 * Get a value from a persistent data store.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get($key) {
		return CakeSession::read($key);
	}

	/**
	 * Set a value in the persistent data store.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set($key, $value) {
		CakeSession::write($key, $value);
	}
}