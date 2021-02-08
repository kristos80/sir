<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Configuration;

use Kristos80\Sir\Traits\PropertySetterPattern;
use Kristos80\Sir\Traits\ExecuteOnConstructTrait;

final class SirConfiguration extends PropertySetterPattern {
	use ExecuteOnConstructTrait;

	public \PDO $pdo;

	/**
	 * @var PdoSettings
	 */
	public $pdoSettings;

	public string $databaseType = 'mysql';

	/**
	 * @var NamingSettings
	 */
	public $namingSettings = [];

	protected function construct(): void {
		! is_a($this->pdoSettings, PdoSettings::class) && ! is_a($this->pdo, \PDO::class) ? $this->pdoSettings = new PdoSettings(
			is_array($this->pdoSettings) ? $this->pdoSettings : []) : NULL;

		! is_a($this->namingSettings, NamingSettings::class) &&
			$this->namingSettings = new NamingSettings($this->namingSettings);
	}

	public function getPdo(): \PDO {
		return $this->pdo ?: $this->pdo = new \PDO(
			sprintf('mysql:host=%s;dbname=%s', $this->pdoSettings->hostname, $this->pdoSettings->database),
			$this->pdoSettings->username, $this->pdoSettings->password);
	}
}