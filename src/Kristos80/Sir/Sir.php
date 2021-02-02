<?php
declare(strict_types = 1);

namespace Kristos80\Sir;

use Kristos80\Opton\Opton;
use Aura\SqlQuery\QueryFactory;

class Sir {

	/**
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * @var PdoSettings
	 */
	protected $pdoSettings;

	/**
	 * @var string
	 */
	protected $databaseType;

	/**
	 * @var QueryFactory
	 */
	protected $queryFactory;

	public const SIR = '__sir';

	public const TABLE = 'table';

	public const SEARCH_COLUMN = 'searchColumn';

	public const DEPENDENT_ON_PARENT = '__dependentOnParent';

	public const ID_COLUMN = 'idColumn';

	public function __construct(?SirConfiguration $sirConfiguration = NULL) {
		if ($sirConfiguration) {
			($pdoSettings = Opton::get('pdoSettings', $sirConfiguration)) ? $this->setPdoSettings($pdoSettings) : NULL;
			($databaseType = Opton::get('databaseType', $sirConfiguration)) ? $this->setDatabaseType($databaseType) : NULL;
		}
	}

	public function setPdo(\PDO $pdo): Sir {
		$this->pdo = $pdo;

		return $this;
	}

	public function getPdo(): \PDO {
		return $this->pdo ?: $this->pdo = new \PDO(
			sprintf('mysql:host=%s;dbname=%s', $this->pdoSettings->hostname, $this->pdoSettings->database),
			$this->pdoSettings->username, $this->pdoSettings->password);
	}

	public function setPdoSettings(PdoSettings $pdoSettings): Sir {
		$this->pdoSettings = $pdoSettings;

		return $this;
	}

	public function setDatabaseType(string $databaseType): Sir {
		$this->databaseType = $databaseType;

		return $this;
	}

	public function getQueryFactory(): QueryFactory {
		return $this->queryFactory ?: $this->queryFactory = new QueryFactory($this->databaseType);
	}

	public function sync(\stdClass $data, ?string $dependentColumn = NULL, ?int $parentId = NULL) {
		$data = $data ?: $this->data;

		$parentId ? $data->{$dependentColumn} = $parentId : NULL;

		$parentDependents = [];
		foreach ($data as $key => $value) {
			$data->{$key} = $this->shouldSync($key, $value) ? $this->sync($value) : $value;
			$parentDependents = $this->extractDependents($key, $value, $parentDependents);
		}

		$dataSirConfiguration = $this->getDataSirConfiguration($data);
		if ($dataSirConfiguration && ! Opton::get($dataSirConfiguration->idColumn, $data)) {
			$select = $this->getQueryFactory()
				->newSelect()
				->cols([
				'*'
			])
				->from($dataSirConfiguration->table)
				->where($dataSirConfiguration->searchColumn . ' = :' . $dataSirConfiguration->searchColumn)
				->bindValues([
				$dataSirConfiguration->searchColumn => Opton::get($dataSirConfiguration->searchColumn, $data),
			]);

			$sth = $this->getPdo()
				->prepare($select->getStatement());
			$sth->execute($select->getBindValues());
			$record = $sth->fetch(\PDO::FETCH_OBJ);

			$record ? $data->{$dataSirConfiguration->idColumn} = (int) $record->{$dataSirConfiguration->idColumn} : NULL;

			if (! $record) {
				$insert = $this->getQueryFactory()
					->newInsert()
					->into($dataSirConfiguration->table)
					->cols($this->normalizeDataColumns($data, $dataSirConfiguration->idColumn, TRUE))
					->bindValues($this->normalizeDataColumns($data, $dataSirConfiguration->idColumn));

				$sth = $this->getPdo()
					->prepare($insert->getStatement());
				$sth->execute($insert->getBindValues());

				$idColumn = $insert->getLastInsertIdName($dataSirConfiguration->idColumn);
				$data->{$dataSirConfiguration->idColumn} = (int) $this->getPdo()
					->lastInsertId($idColumn);
			}
		}

		$data = $this->syncParentDependents($parentDependents, $data, $dataSirConfiguration->idColumn);

		unset($data->{self::SIR});

		return $data;
	}

	public function normalizeDataColumns(\stdClass $data, string $idColumn, bool $onlyColumns = FALSE): array {
		$columns = [];
		foreach ($data as $key => $value) {
			if (! is_object($value) && $key !== self::SIR) {
				$columns[$key] = $value;
			} elseif (is_object($value) && $key !== self::SIR) {
				($id = Opton::get($idColumn, $value)) ? $columns[$key] = $id : NULL;
			}
		}

		return $onlyColumns ? array_keys($columns) : $columns;
	}

	public function getDataSirConfiguration(\stdClass $data): ?DataSirConfiguration {
		$sir = Opton::get(self::SIR, $data);
		if ($sir) {
			$table = Opton::get(self::TABLE, $sir);
			$searchColumn = Opton::get(self::SEARCH_COLUMN, $sir);
			$idColumn = Opton::get(self::ID_COLUMN, $sir, 'id');
		}

		return $sir ? new DataSirConfiguration(
			[
				self::TABLE => $table,
				self::SEARCH_COLUMN => $searchColumn,
				self::ID_COLUMN => $idColumn,
			]) : NULL;
	}

	public function syncParentDependents(array $parentDependents, \stdClass $data, string $columnId): \stdClass {
		if (! empty($parentDependents)) {
			foreach ($parentDependents as $parentDependency) {
				/**
				 * @var ParentDependency $parentDependency
				 */
				$parentDependency;

				foreach ($parentDependency->records as $dependentData) {
					$data->{$parentDependency->name}[] = $this->sync($dependentData, $parentDependency->parentColumnId,
						(int) Opton::get($columnId, $data));
				}

				unset($data->{self::DEPENDENT_ON_PARENT . '_' . $parentDependency->name});
			}
		}

		return $data;
	}

	public function shouldSync($key, $value): bool {
		return is_object($value) && $key !== self::SIR && ! $this->parentDependentKey($key);
	}

	public function extractDependents($key, $value, $parentDependants): array {
		if ($parentDependentName = $this->parentDependentKey($key)) {
			$value->name = $parentDependentName;
			$parentDependants[] = new ParentDependency((array) $value);
		}

		return $parentDependants;
	}

	public function parentDependentKey(string $key): ?string {
		$parentDependentLabel = strtolower(self::DEPENDENT_ON_PARENT);
		$key = strtolower($key);
		if (substr($key, 0, strlen($parentDependentLabel)) === $parentDependentLabel) {
			return substr($key, strlen($parentDependentLabel) + 1);
		}

		return NULL;
	}
}