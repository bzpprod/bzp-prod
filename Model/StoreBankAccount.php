<?php
App::uses('BankAccount', 'Model');

class StoreBankAccount extends BankAccount
{
	public $name = 'StoreBankAccount';
	public $modelAttribute = 'Store';
}
