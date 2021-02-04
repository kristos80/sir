<?php
declare(strict_types = 1);

namespace Kristos80\Sir;

class PdoSettings {

	/**
	 * @var string
	 */
	public $hostname = '127.0.0.1';

	/**
	 * @var string
	 */
	public $database = 'test';

	/**
	 * @var string
	 */
	public $username = 'root';

	/**
	 * @var string
	 */
	public $password = '';

	public function __construct(array $pdoSettings) {
		foreach ($pdoSettings as $property => $value) {
			property_exists($this, $property) ? $this->{$property} = $value : NULL;
		}
	}
}