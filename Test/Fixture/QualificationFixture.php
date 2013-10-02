<?php
/**
 * QualificationFixture
 *
 */
class QualificationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'transaction_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'qualified_user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'qualification' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2),
		'hash' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_date' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'finished_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'finished' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
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
			'user_id' => 1,
			'transaction_id' => 1,
			'qualified_user_id' => 1,
			'qualification' => 1,
			'hash' => 'Lorem ipsum dolor sit amet',
			'created_date' => '2012-05-14 23:58:41',
			'finished_date' => '2012-05-14 23:58:41',
			'finished' => 1
		),
	);
}
