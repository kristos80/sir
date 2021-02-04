<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Configuration;

use Kristos80\Sir\Traits\PropertySetterPattern;

final class DataConfiguration extends PropertySetterPattern {

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
}