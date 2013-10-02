<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

// Not extending AppModel because of the findbyid
class LogSearch extends Model
{
	public $name = __CLASS__;
	public $useTable = 'logSearch';
	public $actsAs = array('Containable');
	public $primaryKey = 'slug';
	public $logEnabled = false;
	
	public $_schema = array(
						'slug' => array(
										'type' => 'string',
										'length' => 255
									),
						'created' => array(
										'type' => 'datetime',
										'length' => 19
									),
						'active' => array(
									'type' => 'boolean',
									'length' => 1
									)
					);
}
