<?php

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class TwinfieldResourceOwner implements ResourceOwnerInterface
{
	/**
	 * @var array
	 */
	private $response;

	public function __construct(array $response)
	{
		$this->response = $response;
	}

	/**
	 * Returns the identifier of the authorized resource owner.
	 */
	public function getId(): string
	{
		return $this->response["sub"];
	}

	/**
	 * Return all of the owner details available as an array.
	 */
	public function toArray(): array
	{
		return $this->response;
	}
}