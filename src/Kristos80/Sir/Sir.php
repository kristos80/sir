<?php
declare(strict_types = 1);

namespace Kristos80\Sir;

use Kristos80\Sir\Configuration\SirConfiguration;
use Kristos80\Sir\Data\Data;
use Kristos80\Opton\Opton;
use Aura\SqlQuery\QueryFactory;
use Kristos80\Sir\Data\DataCollection;

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
			($record = $this->getRecord($data)) &&
				$data->{$dataConfiguration->idColumn} = (int) $record->{$dataConfiguration->idColumn};

			! $record && ($newId = $this->insertRecord($data)) && $data->{$dataConfiguration->idColumn} = $newId;
		}

		return $this->syncCollections($data);
	}

	private function getPdo(): \PDO {
		return $this->configuration->getPdo();
	}

	private function getRecord(Data $data): ?\stdClass {
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

		return ($record = $sth->fetch(\PDO::FETCH_OBJ)) ? $record : NULL;
	}

	private function insertRecord(Data $data): ?int {
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

		$idColumn = $insert->getLastInsertIdName($dataConfiguration->idColumn);

		return ($newId = $this->getPdo()
			->lastInsertId($idColumn)) ? (int) $newId : NULL;
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
