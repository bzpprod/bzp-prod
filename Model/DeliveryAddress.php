<?php
App::uses('Address', 'Model');

class DeliveryAddress extends Address
{
	public $name = 'DeliveryAddress';
	public $modelAttribute = 'Delivery';
}
