<?php
App::uses('Address', 'Model');

class UserAddress extends Address
{
	public $name = 'UserAddress';
	public $modelAttribute = 'User';
}
