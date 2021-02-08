<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Configuration;

final class Constants {

	public const DATA_MODE_INSERT = 'insert';

	public const DATA_MODE_INSERT_UPDATE = 'insert_update';

	public const DATA_MODE_UPDATE = 'update';

	public const TABLE_CASE_PASCAL = 'toPascal';

	public const ALLOWED_TABLE_CASES = [
		self::TABLE_CASE_PASCAL,
	];

	public const COLLECTIONS_FK_FROM_TABLE = 'fromTable';

	public const COLLECTIONS_FK_FROM_ID = 'fromId';

	public const ALLOWED_COLLECTIONS_FK = [
		self::COLLECTIONS_FK_FROM_TABLE,
		self::COLLECTIONS_FK_FROM_ID,
	];
}