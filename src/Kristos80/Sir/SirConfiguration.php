<?php
declare(strict_types = 1);

namespace Kristos80\Sir;

class SirConfiguration {

	/**
	 * @var \PDO
	 */
	public $pdo;

	/**
	 * @var array
	 */
	public $pdoSettings;

	/**
	 * @var string
	 */
	public $databaseType = 'mysql';

	public function __construct(array $configuration = []) {
		foreach ($configuration as $property => $value) {
			property_exists($this, $property) ? $this->{$property} = $value : NULL;
			if ($property === 'pdoSettings') {
				$this->{$property} = new PdoSettings($value);
			}
		}
	}
}