<?php
declare(strict_types = 1);

namespace Kristos80\Sir\Traits;

trait ExecuteOnConstructTrait {

	public function __construct() {
		parent::__construct(...func_get_args());
		$this->construct();
	}

	protected function construct(): void {
		echo 'HELLO';
	}
}