<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Data;

use Kristos80\Sir\Traits\PropertySetterPattern;
use Kristos80\Sir\Configuration\DataConfiguration;
use Kristos80\Opton\Opton;

abstract class Data extends PropertySetterPattern {

	public string $_table = '';

	public string $_searchColumn = '';

	public string $_idColumn = 'id';

	protected ?DataConfiguration $_configuration = NULL;

	/**
	 * @var []DataCollection
	 */
	public array $_dataCollections = [];

	public function __construct(array $propertySetters) {
		parent::__construct($propertySetters);

		foreach ($propertySetters as $key => $propertySetter) {
			$collectionPrefix = '_collection';
			if (substr($key, 0, strlen($collectionPrefix)) === $collectionPrefix) {
				$dataCollectionName = $propertySetter->name;
				$dataCollectionName = $dataCollectionName ?? substr($key, strlen($collectionPrefix) + 1);
				$propertySetter->name = $dataCollectionName;
				$this->_dataCollections[] = $propertySetter;
			}
		}
	}

	public function getConfiguration(): DataConfiguration {
		if (is_a($this->_configuration, DataConfiguration::class)) {
			return $this->_configuration;
		}

		$this->_configuration = new DataConfiguration(
			[
				'table' => $this->_table,
				'searchColumn' => $this->_searchColumn,
				'idColumn' => $this->_idColumn,
			]);

		return $this->_configuration;
	}

	public function getColumns(bool $withValues = TRUE): array {
		$columns = [];
		foreach ($this as $property => $value) {
			$value_ = is_a($value, Data::class) ? $value->getIdValue() : $value;
			$this->isValidColumn($property) && ($columns[$property] = $value_);
		}

		return $withValues ? $columns : array_keys($columns);
	}

	public function isValidColumn(string $property): bool {
		return substr($property, 0, 1) !== '_';
	}

	public function getIdValue(): ?int {
		$idValue = Opton::get($this->_configuration->idColumn, $this);

		return $idValue ?? (int) $idValue;
	}

	public function addCollection(DataCollection $dataCollection): Data {
		if (is_a($dataCollection, DataCollection::class)) {
			$this->_dataCollections[] = $dataCollection;
		}

		return $this;
	}

	public function addCollectionFromArray(array $data, string $name, string $parentColumnId): Data {
		$this->_dataCollections[] = new DataCollection(
			[
				'data' => $data,
				'name' => $name,
				'parentColumnId' => $parentColumnId,
			]);

		return $this;
	}

	public function addToCollection(Data $data, string $name): Data {
		foreach ($this->_dataCollections as $key => $dataCollection) {
			$dataCollection->name === $name && $this->_dataCollections[$key]->addData($data);
		}

		return $this;
	}

	public function getDataCollections(): array {
		return $this->_dataCollections;
	}

	public function export(): \stdClass {
		foreach ($this->_dataCollections as $dataCollection) {
			property_exists($this, $collectionName = $dataCollection->name) && $this->{$collectionName} = $dataCollection->export();
		}

		return json_decode(json_encode($this->getColumns(TRUE)));
	}
}
