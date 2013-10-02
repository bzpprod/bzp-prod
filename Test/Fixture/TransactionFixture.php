<?php
/**
 * TransactionFixture
 *
 */
class TransactionFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'product_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'store_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'buyer_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'quantity' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'price' => array('type' => 'float', 'null' => false, 'default' => NULL, 'length' => '10,2'),
		'total_price' => array('type' => 'float', 'null' => false, 'default' => NULL, 'length' => '10,2'),
		'hash' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_date' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'finished_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'exchanged' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'finished' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'canceled' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'product_id' => 1,
			'store_id' => 1,
			'buyer_id' => 1,
			'quantity' => 1,
			'price' => 1,
			'total_price' => 1,
			'hash' => 'Lorem ipsum dolor sit amet',
			'created_date' => '2012-05-14 23:59:38',
			'finished_date' => '2012-05-14 23:59:38',
			'exchanged' => 1,
			'finished' => 1,
			'canceled' => 1
		),
	);
}
