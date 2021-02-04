<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Data;

use Kristos80\Sir\Traits\PropertySetterPattern;
use Kristos80\Sir\Configuration\DataConfiguration;
use Kristos80\Opton\Opton;

abstract class Data extends PropertySetterPattern {

	/**
	 * @var DataConfiguration
	 */
	protected $__dataConfiguration;

	public function setDataConfiguration(DataConfiguration $dataConfiguration): Data {
		$this->__dataConfiguration = $dataConfiguration;

		return $this;
	}

	public function getColumns(bool $withValues = TRUE): array {
		$columns = [];
		foreach ($this as $property => $value) {
			$value_ = is_a($this, '\\Kristos80\\Sir\\Data\\Data') ?: $value;
			$this->isValidColumn($property) ? $columns[$property] = $value_ : NULL;
		}

		return $withValues ? $columns : array_keys($columns);
	}

	public function isValidColumn(string $property, $value): bool {
		return TRUE;
	}

	public function getIdValue(): ?int {
		$idValue = Opton::get($this->__dataConfiguration->idColumn, $this);

		return $idValue ? (int) $idValue : NULL;
	}
}