<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Traits;

abstract class PropertySetterPattern {

	public function __construct(array $propertySetters = []) {
		foreach ($propertySetters as $property => $value) {
			property_exists($this, $property) ? $this->{$property} = (is_numeric($value) ? $value * 1 : $value) : NULL;
		}
	}
}