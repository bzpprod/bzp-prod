<?php
/**
 * FileFixture
 *
 */
class FileFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'filename' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'directory' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'mimetype' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'filesize' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'hash' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_date' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'deleted' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'model' => array('column' => 'model', 'unique' => 0), 'foreign_key' => array('column' => 'foreign_key', 'unique' => 0), 'hash' => array('column' => 'hash', 'unique' => 0), 'deleted' => array('column' => 'deleted', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'model' => 'Lorem ipsum dolor sit amet',
			'foreign_key' => 1,
			'filename' => 'Lorem ipsum dolor sit amet',
			'directory' => 'Lorem ipsum dolor sit amet',
			'mimetype' => 'Lorem ipsum dolor sit amet',
			'filesize' => 1,
			'hash' => 'Lorem ipsum dolor sit amet',
			'created_date' => '2012-05-14 23:57:34',
			'deleted' => 1
		),
	);
}
