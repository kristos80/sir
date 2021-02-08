<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Configuration;

use Kristos80\Sir\Traits\PropertySetterPattern;
use Kristos80\Sir\Traits\ExecuteOnConstructTrait;

final class NamingSettings extends PropertySetterPattern {

	use ExecuteOnConstructTrait;

	public string $tableCase = Constants::TABLE_CASE_PASCAL;

	public string $collectionsFK = Constants::COLLECTIONS_FK_FROM_ID;

	public ?string $defaultSearchColumn = NULL;

	public ?string $defaultIdColumn = 'id';

	protected function construct(): void {
		! in_array($this->tableCase, Constants::ALLOWED_TABLE_CASES) &&
			$this->tableCase = Constants::TABLE_CASE_PASCAL;
		! in_array($this->collectionsFK, Constants::ALLOWED_COLLECTIONS_FK) &&
			$this->collectionsFK = Constants::COLLECTIONS_FK_FROM_ID;
	}
}