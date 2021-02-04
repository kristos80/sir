<?php
declare(strict_types = 1);

namespace Kristos80\Sir;

abstract class SirData {

	/**
	 * @var string
	 */
	protected $__table = '';

	public function __construct(array $data = []) {
		$data ? $this->setData($data) : NULL;
	}

	public function setData(array $data) {
		foreach ($data as $property => $value) {
			property_exists($this, $property) ? $this->{$property} = $value : NULL;
		}
	}
	
	public function setSirConfiguration(){
		
	}
}