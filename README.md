**WIP**   
**Not to be used in production**
```PHP
<?php
use Kristos80\Sir\Configuration\SirConfiguration;
use Kristos80\Sir\Sir;
use Kristos80\Sir\Configuration\Constants;

require_once __DIR__ . '/vendor/autoload.php';

// See `classes.php` below
require_once __DIR__ . '/var/classes.php';

// Set `SIR_DEBUG` mode somehow
$_ENV['SIR_DEBUG'] = TRUE;

$sirConfiguration = [
	// Use `PDO` directly
	'pdo' => new PDO('mysql:host=localhost;dbname=midas_2', 'root'),

	// Or add settings by array
	'pdoSettings' => [
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'midas_2',
	],
];

$sir = new Sir(new SirConfiguration($sirConfiguration));

$storeData = [
	'type' => 'Magento',
	'name' => 'Shop Name',
	'desc' => 'Shop Description',
	'uid' => 'my_uid',
	'base_api_url' => 'https://api.domain.com',
	'processing_offset' => 1,
	'_mode' => Constants::DATA_MODE_INSERT_UPDATE, // Constants::DATA_MODE_INSERT || Constants::DATA_MODE_INSERT_UPDATE ||
	                                                // Constants::DATA_MODE_UPDATE
];

$store = new Store($storeData);

$productType = $sir->sync(new ProductType([
	'label' => 'Mug',
]));

$productsCollection = [
	new Product(
		[
			'name' => 'Fab product',
			'sku' => 'a-fab-product',
			'uid' => 'fab-product-91a921b',
			'product_type_id' => $productType,
			'parent' => 0,
			'_mode' => Constants::DATA_MODE_INSERT_UPDATE,
		]),
	new Product(
		[
			'name' => 'Fab product 2',
			'sku' => 'a-fab-product-2',
			'uid' => 'fab-product-91a921b-2',
			'product_type_id' => $productType,
			'parent' => 0,
			'_mode' => Constants::DATA_MODE_UPDATE, // This `product` will not be created, if it doesn't exist
		]),
];

$store->addCollectionFromArray($productsCollection, 'products', 'store_id');

echo json_encode($sir->sync($store)
	->export());
```
Classes.php
```PHP
<?php
use Kristos80\Sir\Data\Data;

final class Term extends Data {

	public string $_table = 'term';

	public string $_searchColumn = 'slug';

	public int $attribute_id;

	public string $slug;

	public string $label;
}

final class Attribute extends Data {

	public string $_table = 'attribute';

	public string $_searchColumn = 'slug';

	public string $label;

	public string $slug;

	/**
	 * @var []Term
	 */
	public array $terms = [];
}

final class Store extends Data {

	public string $_table = 'store';

	public string $_searchColumn = 'uid';
		
	public string $type;

	public string $name;

	public string $desc;

	public string $uid;

	public string $base_api_url;

	public int $processing_offset;
}

final class Product extends Data {

	public string $_table = 'product';

	public string $_searchColumn = 'uid';

	public string $name;

	public string $sku;

	public string $uid;

	public $product_type_id;

	public $store_id;

	public int $parent;
}

final class ProductType extends Data {

	public string $_table = 'product_type';

	public string $_searchColumn = 'label';

	public string $label;
}
```
