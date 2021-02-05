<?php
declare(strict_types = 1);

namespace Kristos80\Sir;

use Kristos80\Sir\Configuration\SirConfiguration;
use Kristos80\Sir\Data\Data;
use Kristos80\Opton\Opton;
use Aura\SqlQuery\QueryFactory;
use Kristos80\Sir\Data\DataCollection;
use Kristos80\Sir\Configuration\Constants;

final class Sir {

	private SirConfiguration $configuration;

	private ?QueryFactory $queryFactory = NULL;

	public function __construct(SirConfiguration $sirConfiguration) {
		$this->configuration = $sirConfiguration;
	}

	public function sync(Data $data, ?string $parentColumn = NULL, ?int $parentId = NULL): Data {
		$parentId ? $data->{$parentColumn} = $parentId : NULL;

		foreach ($data as $key => $value) {
			$data->{$key} = is_a($value, Data::class) ? $this->sync($value) : $value;
		}

		$dataConfiguration = $data->getConfiguration();
		if (! Opton::get($dataConfiguration->idColumn, $data)) {
			($record = $this->getRecord($data)) && $data->sync((array) $record);
		}

		return $this->syncCollections($data);
	}

	private function getPdo(): \PDO {
		return $this->configuration->getPdo();
	}

	private function getRecord(Data $data, string $mode = 'select', int $recursion = 0): ?\stdClass {
		if ($mode === 'select' && $recursion > 1) {
			return NULL;
		}

		$dataConfiguration = $data->getConfiguration();

		$select = $this->getQueryFactory()
			->newSelect()
			->cols([
			'*',
		])
			->from($dataConfiguration->table)
			->where($dataConfiguration->searchColumn . ' = :' . $dataConfiguration->searchColumn)
			->bindValues(
			[
				$dataConfiguration->searchColumn => Opton::get($dataConfiguration->searchColumn, $data),
			]);

		$sth = $this->configuration->getPdo()
			->prepare($select->getStatement());
		$sth->execute($select->getBindValues());

		$this->throwSqlErrorIfNeeded($sth);

		$record = $sth->fetch(\PDO::FETCH_OBJ);

		if ($record && $mode !== 'update') {
			switch ($dataConfiguration->mode) {
				case Constants::DATA_MODE_INSERT_UPDATE:
				case Constants::DATA_MODE_UPDATE:
					$this->updateRecord($data);
					return $this->getRecord($data, 'update');
					break;
			}
		} elseif ($mode !== 'update') {
			switch ($dataConfiguration->mode) {
				case Constants::DATA_MODE_INSERT:
				case Constants::DATA_MODE_INSERT_UPDATE:
					$this->insertRecord($data);
					break;
			}
		}

		return $record ? $record : $this->getRecord($data, 'select', ++ $recursion);
	}

	private function insertRecord(Data $data): void {
		$dataConfiguration = $data->getConfiguration();

		$insert = $this->getQueryFactory()
			->newInsert()
			->into($dataConfiguration->table)
			->cols($data->getColumns(FALSE))
			->bindValues($data->getColumns());

		$sth = $this->configuration->getPdo()
			->prepare($insert->getStatement());
		$sth->execute($insert->getBindValues());

		$this->throwSqlErrorIfNeeded($sth);
	}

	private function updateRecord(Data $data): void {
		$dataConfiguration = $data->getConfiguration();

		$update = $this->getQueryFactory()
			->newUpdate()
			->table($dataConfiguration->table)
			->cols($data->getColumns())
			->where($dataConfiguration->searchColumn . ' = :' . $dataConfiguration->searchColumn);

		$sth = $this->configuration->getPdo()
			->prepare($update->getStatement());
		$sth->execute($update->getBindValues());

		$this->throwSqlErrorIfNeeded($sth);
	}

	private function syncCollections(Data $data): Data {
		$dataConfiguration = $data->getConfiguration();
		foreach ($data->_dataCollections as $key => $dataCollection) {
			$data->_dataCollections[$key] = $this->syncCollection($dataCollection,
				$data->{$dataConfiguration->idColumn});
		}

		return $data;
	}

	private function syncCollection(DataCollection $dataCollection, int $parentId): DataCollection {
		foreach ($dataCollection->data as $key => $data) {
			$dataCollection->data[$key] = $this->sync($data, $dataCollection->parentColumnId, $parentId);
		}

		return $dataCollection;
	}

	private function getQueryFactory(): QueryFactory {
		return $this->queryFactory ?? ($this->queryFactory = new QueryFactory($this->configuration->databaseType));
	}

	private function throwSqlErrorIfNeeded(\PDOStatement $sth): void {
		if ($sth->errorCode() !== '00000') {
			throw new \Exception(print_r($sth->errorInfo(), TRUE));
		}
	}
}
