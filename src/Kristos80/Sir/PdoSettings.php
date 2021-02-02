<?php
declare(strict_types = 1);

namespace Kristos80\Sir;

class PdoSettings {

	/**
	 * @var string
	 */
	public $hostname;

	/**
	 * @var string
	 */
	public $database;

	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string
	 */
	public $password;

	public function __construct(array $pdoSettings) {
		foreach ($pdoSettings as $property => $value) {
			property_exists($this, $property) ? $this->{$property} = $value : NULL;
		}
	}
}