**WIP**   
**Not to be used in production**
```
<?php
use Kristos80\Sir\Sir;
use Kristos80\Sir\SirConfiguration;

require_once __DIR__ . '/vendor/autoload.php';

$sir = new Sir(
	new SirConfiguration(
		[
			'pdoSettings' => [
				'hostname' => '127.0.0.1',
				'username' => 'root',
				'password' => '',
				'database' => 'midas_2',
			]
		]));

$sirProp = Sir::SIR;
$tableProp = Sir::TABLE;
$searchColProp = Sir::SEARCH_COLUMN;
$depOnParentProp = Sir::DEPENDENT_ON_PARENT;
$parentColIdProp = Sir::PARENT_COLUMN_ID;
$recordsProp = Sir::RECORDS;
$modeProp = Sir::MODE;

$store = (object) [];
$store->{$sirProp} = (object) [];
$store->{$sirProp}->{$tableProp} = 'store';
$store->{$sirProp}->{$searchColProp} = 'uid';

$store->type = 'magento';
$store->name = 'Myikona.gr';
$store->desc = 'Shop Description';
$store->uid = 'mi';
$store->base_api_url = 'https://myikona.gr/wp-api/json';
$store->processsing_offset = 1;

$productsDeps = $depOnParentProp . '_products';
$store->{$productsDeps} = (object) [];
$store->{$productsDeps}->{$parentColIdProp} = 'store_id';
$store->{$productsDeps}->{$recordsProp} = [
	(object) [
		$sirProp => [
			$tableProp => 'product',
			$searchColProp => 'uid',
			$modeProp => Sir::MODE_INSERT_OR_UPDATE,
		],
		'name' => 'Fabulous product 2',
		'sku' => 'fab-product',
		'uid' => 'fab-product',
		'product_type_id' => (object) [
			$sirProp => [
				$tableProp => 'product_type',
				$searchColProp => 'label',
			],
			'label' => 'photobook',
		],
		'parent' => 0,
	]
];

echo json_encode($sir->sync($store));
```
```
{
	type: "magento",
	name: "Myikona.gr",
	desc: "Shop Description",
	uid: "mi",
	base_api_url: "https://myikona.gr/wp-api/json",
	processsing_offset: 1,
	id: 1,
	products: [{
		name: "Fabulous product 2",
		sku: "fab-product",
		uid: "fab-product",
		product_type_id: {
			label: "photobook",
			id: 1
		},
		parent: 0,
		store_id: 1,
		id: 1
	}]
}
```
