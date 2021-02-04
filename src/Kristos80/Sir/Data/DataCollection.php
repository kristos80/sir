<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Data;

use Kristos80\Sir\Traits\PropertySetterPattern;

abstract class DataCollection extends PropertySetterPattern {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var []Data
	 */
	public $data;
}