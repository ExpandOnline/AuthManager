<?php

/**
 * Class AuthContainer
 */
abstract class AuthContainer {

/**
 * @var int
 */
	public $userId;

	public $mediaPlatformId;

	public function getMediaPlatformId() {
		return $this->mediaPlatformId;
	}
}