<?php
App::uses('AppModel', 'Model');

class BankAccount extends AppModel
{
	public $name = 'BankAccount';
	public $useTable = 'bank_accounts';
	public $whitelist = array('name', 'bank', 'agency', 'account', 'document');
	public $actsAs = array('Containable');
}
