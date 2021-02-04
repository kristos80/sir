<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Configuration;

use Kristos80\Sir\Traits\PropertySetterPattern;

final class PdoSettings extends PropertySetterPattern {

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
}