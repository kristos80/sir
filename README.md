**WIP**   
**Not to be used in production**
```
<?php
use Kristos80\Sir\Configuration\SirConfiguration;
use Kristos80\Sir\Sir;
use Kristos80\Sir\Configuration\Constants;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/var/classes.php';

$sirConfiguration = [
	'pdo' => new PDO('mysql:host=localhost;dbname=midas_2', 'root'),
	'pdoSettings44' => [
		'hostname' => 'localhost',
	],
];

$sir = new Sir(new SirConfiguration($sirConfiguration));

$storeData = [
	'type' => 'Magento',
	'name' => 'Myikona.gr',
	'desc' => 'Shop Description',
	'uid' => 'mi344482233323333',
	'base_api_url' => 'https://www.myikona.gr23222',
	'processing_offset' => 12,
	'_mode' => Constants::DATA_MODE_INSERT_UPDATE,
];

$store = new Store($storeData);

$productType = $sir->sync(new ProductType([
	'label' => 'Mug4822',
]));

$productsCollection = [
	new Product(
		[
			'name' => 'Fab3',
			'sku' => 'fab-product-3',
			'uid' => 'fab-product-58622',
			'product_type_id' => $productType,
			'parent' => 0,
			'_mode' => Constants::DATA_MODE_INSERT_UPDATE,
		]),
];

$store->addCollectionFromArray($productsCollection, 'products', 'store_id');

echo json_encode($sir->sync($store)->export());
