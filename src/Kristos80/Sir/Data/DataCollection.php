<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Data;

use Kristos80\Sir\Traits\PropertySetterPattern;

final class DataCollection extends PropertySetterPattern {

	public string $name = '';

	public string $parentColumnId = '';

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
}