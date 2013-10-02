<?php
App::uses('PaypalAccount', 'Model');

class StorePaypalAccount extends PaypalAccount
{
	public $name = 'StorePaypalAccount';
	public $modelAttribute = 'Store';	
}
