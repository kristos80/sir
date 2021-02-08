<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Data;

use Kristos80\Sir\Traits\PropertySetterPattern;
use Kristos80\Sir\Configuration\SirConfiguration;
use Kristos80\Sir\Configuration\NamingSettings;
use Kristos80\Opton\Opton;
use Kristos80\Sir\Configuration\Constants;

final class DataCollection extends PropertySetterPattern {

	public string $name = '';

	public ?string $parentColumnId = '';

	/**
	 * @var []Data
	 */
	public $data = [];

	public function addData(Data $data): DataCollection {
		$this->data[] = $data;

		return $this;
	}

	public function getData(): array {
		return $this->data;
	}

	public function export(): array {
		$export = [];
		foreach ($this->data as $data) {
			$export[] = $data->export();
		}

		return $export;
	}

	public function setUp(SirConfiguration $sirConfiguration, Data $parentData): void {
		! $this->parentColumnId && $this->parentColumnId = $this->getParentColumnId($sirConfiguration, $parentData);
		! $this->name && $this->name = Opton::get('0', $this->data)->_table . '_collection';
	}

	protected function getParentColumnId(SirConfiguration $sirConfiguration, Data $parentData): ?string {
		$parentColumnId = NULL;

		switch ($sirConfiguration->namingSettings->collectionsFK) {
			case Constants::COLLECTIONS_FK_FROM_ID:
				$parentColumnId = $parentData->_table . '_' . $parentData->_idColumn;
				break;
			case Constants::COLLECTIONS_FK_FROM_TABLE:
				$parentColumnId = $parentData->_table;
				break;
		}

		return $parentColumnId;
	}
}