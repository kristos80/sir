<?php
declare(strict_types = 1);

namespace Kristos80\Sir;

class ParentDependency {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $parentColumnId;

	/**
	 * @var []
	 */
	public $records;

	public function __construct(array $configuration) {
		foreach ($configuration as $property => $value) {
			property_exists($this, $property) ? $this->{$property} = $value : NULL;
		}
	}
}