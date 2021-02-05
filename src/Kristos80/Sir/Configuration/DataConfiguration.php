<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Configuration;

use Kristos80\Sir\Traits\PropertySetterPattern;

final class DataConfiguration extends PropertySetterPattern {

	public string $table;

	public string $searchColumn;

	public string $idColumn = 'id';

	public string $dataMode = Constants::DATA_MODE_INSERT;
}