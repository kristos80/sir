<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Configuration;

use Kristos80\Sir\Traits\PropertySetterPattern;

final class PdoSettings extends PropertySetterPattern {

	public string $hostname = '127.0.0.1';

	public string $database = 'test';

	public string $username = 'root';

	public string $password = '';
}