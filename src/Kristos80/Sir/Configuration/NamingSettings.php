<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Configuration;

use Kristos80\Sir\Traits\PropertySetterPattern;
use Kristos80\Sir\Traits\ExecuteOnConstructTrait;

final class NamingSettings extends PropertySetterPattern {

	use ExecuteOnConstructTrait;

	public const TABLE_CASE_PASCAL = 'toPascal';

	protected const ALLOWED_TABLE_CASES = [
		self::TABLE_CASE_PASCAL,
	];

	public const COLLECTIONS_FK_FROM_TABLE = 'fromTable';

	public const COLLECTIONS_FK_FROM_ID = 'fromId';

	protected const ALLOWED_COLLECTIONS_FK = [
		self::COLLECTIONS_FK_FROM_TABLE,
		self::COLLECTIONS_FK_FROM_ID,
	];

	public string $tableCase = self::TABLE_CASE_PASCAL;

	public string $collectionsFK = self::COLLECTIONS_FK_FROM_ID;

	public ?string $defaultSearchColumn = NULL;

	public ?string $defaultIdColumn = 'id';

	protected function construct(): void {
		! in_array($this->tableCase, self::ALLOWED_TABLE_CASES) && $this->tableCase = self::TABLE_CASE_PASCAL;
		! in_array($this->collectionsFK, self::ALLOWED_COLLECTIONS_FK) &&
			$this->collectionsFK = self::COLLECTIONS_FK_FROM_ID;
	}
}