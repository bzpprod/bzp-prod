<?php
App::uses('AppModel', 'Model');

class Address extends AppModel
{
	public $name = 'Address';
	public $useTable = 'addresses';
	public $whitelist = array('addressee', 'address', 'address_line2', 'district', 'state', 'city', 'zipcode', 'is_default');
	public $actsAs = array('Containable');
	
	public $validate = array(
		'zipcode' => array(
			'rule' => '/^([0-9]{5})(-[0-9]{3})?$/i',
			'message' => 'Address.validate.zipcode',
			'allowEmpty' => false,
			'required' => true
		),
		
		'address' => array(
			'rule' => array('minLength', 5),
			'message' => 'Address.validate.address',
			'allowEmpty' => true,
			'required' => true
		),
		
		'district' => array(
			'rule' => array('minLength', 3),
			'message' => 'Address.validate.district',
			'allowEmpty' => true,
			'required' => true
		),
		
		'city' => array(
			'rule' => array('minLength', 3),
			'message' => 'Address.validate.city',
			'allowEmpty' => true,
			'required' => true
		),
		
		'state' => array(
			'rule' => array('minLength', 2),
			'message' => 'Address.validate.state',
			'allowEmpty' => true,
			'required' => true
		)
	);
}
