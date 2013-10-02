<?php
App::uses('AppModel', 'Model');

class Payment extends AppModel
{
	public $name = 'Payment';
	public $useTable = 'payments';
	public $actsAs = array('Containable');
}
