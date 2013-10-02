<?php
App::uses('AppModel', 'Model');

class Delivery extends AppModel
{
	public $name = 'Delivery';
	public $useTable = 'deliveries';
	public $whitelist = array('company', 'service', 'service_code', 'price', 'tracking');
	public $actsAs = array('Containable');
	
	public $hasOne = array(
		'Address' => array(
			'className' => 'DeliveryAddress',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Address.model' => 'Delivery', 'Address.is_deleted' => false),
			'dependent' => false
		)
	);
}
