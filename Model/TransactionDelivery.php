<?php
App::uses('Delivery', 'Model');

class TransactionDelivery extends Delivery
{
	public $name = 'TransactionDelivery';
	public $modelAttribute = 'Transaction';
}
