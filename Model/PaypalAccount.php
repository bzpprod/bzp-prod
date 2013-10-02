<?php
App::uses('AppModel', 'Model');

class PaypalAccount extends AppModel
{
	public $name = 'PaypalAccount';
	public $useTable = 'paypal_accounts';
	public $whitelist = array('email');
	public $actsAs = array('Containable');
	
	public $validate = array(
		'email' => array(
			'rule' => array('email', false),
			'message' => 'PaypalAccount.validate.email',
			'allowEmpty' => true,
			'required' => true
		)
	);
}
