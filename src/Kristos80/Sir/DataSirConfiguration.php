<?php
declare(strict_types = 1);

namespace Kristos80\Sir;

class DataSirConfiguration {

	/**
	 * @var string
	 */
	public $table;

	/**
	 * @var string
	 */
	public $searchColumn;

	/**
	 * @var string
	 */
	public $idColumn = 'id';

	public function __construct(array $configuration = []) {
		foreach ($configuration as $property => $value) {
			property_exists($this, $property) ? $this->{$property} = $value : NULL;
		}
	}
}