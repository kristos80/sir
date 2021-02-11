<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Data;

use Kristos80\Sir\Traits\PropertySetterPattern;
use Kristos80\Sir\Configuration\DataConfiguration;
use Kristos80\Opton\Opton;
use Kristos80\Sir\Configuration\Constants;
use Jawira\CaseConverter\Convert;
use Kristos80\Sir\Configuration\SirConfiguration;

abstract class Data extends PropertySetterPattern {

	public string $_table = '';

	public string $_searchColumn = '';

	public string $_idColumn = '';

	public string $_mode = Constants::DATA_MODE_INSERT;

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

	public function setUp(SirConfiguration $sirConfiguration): void {
		! $this->_table && $this->_table = $this->tableFromClass();
		! $this->_searchColumn && $this->_searchColumn = $sirConfiguration->namingSettings->defaultSearchColumn;
		$this->_idColumn = $sirConfiguration->namingSettings->defaultIdColumn;
	}

	protected function tableFromClass(): string {
		return (new Convert(get_class($this)))->toSnake();
	}

	public function sync(array $data): Data {
		$configuration = $this->getConfiguration();
		$idColumn = $configuration->idColumn;

		if (in_array($idColumn, array_keys($data))) {
			$this->{$idColumn} = $data[$idColumn];
		}

		foreach ($data as $property => $value) {
			property_exists($this, $property) && ! is_a($this->{$property}, Data::class) &&
				$this->{$property} = $value;
		}

		return $this;
	}

	public function getConfiguration(): DataConfiguration {
		if (is_a($this->_configuration, DataConfiguration::class)) {
			return $this->_configuration;
		}

		$this->_configuration = new DataConfiguration([
			'table' => $this->_table,
			'searchColumn' => $this->_searchColumn,
			'idColumn' => $this->_idColumn,
			'mode' => $this->_mode,
		]);

		return $this->_configuration;
	}

	public function getColumns(bool $withValues = TRUE, bool $forExport = FALSE): array {
		$columns = [];
		foreach ($this as $property => $value) {
			$value_ = is_a($value, Data::class) ? (! $forExport ? $value->getIdValue() : $value->export()) : $value;
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

	public function addCollectionFromArray(array $data, string $name, ?string $parentColumnId = NULL): Data {
		$this->_dataCollections[] = new DataCollection([
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
		$debug = Opton::get('SIR_DEBUG', $_ENV);
		if ($debug) {
			return json_decode(json_encode($this));
		}

		foreach ($this->_dataCollections as $dataCollection) {
			// property_exists($this, $collectionName = $dataCollection->name) &&
			$this->{$dataCollection->name} = $dataCollection->export();
		}

		return json_decode(json_encode($this->getColumns(TRUE, TRUE)));
	}
}
