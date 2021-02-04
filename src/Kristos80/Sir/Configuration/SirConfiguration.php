<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Configuration;

use Kristos80\Sir\Traits\PropertySetterPattern;
use Kristos80\Sir\Traits\ExecuteOnConstructTrait;

final class SirConfiguration extends PropertySetterPattern {
	use ExecuteOnConstructTrait;

	/**
	 * @var \PDO
	 */
	public $pdo;

	/**
	 * @var PdoSettings
	 */
	public $pdoSettings;

	/**
	 * @var string
	 */
	public $databaseType = 'mysql';

	protected function construct(): void {
		! is_a($this->pdoSettings, '\\Kristos80\\Sir\\Configuration\\PdoSettings') && ! is_a($this->pdo, '\\PDO') ? $this->pdoSettings = new PdoSettings(
			is_array($this->pdoSettings) ? $this->pdoSettings : []) : NULL;
	}
}