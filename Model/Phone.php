<?php
App::uses('AppModel', 'Model');

class Phone extends AppModel
{
	public $name = 'Phone';
	public $useTable = 'phones';
	public $whitelist = array('number');
	public $actsAs = array('Containable');
	
	public $validate = array(
		'number' => array(
			'allowEmpty' => true,
			'required' => true
		)
	);
}
