<?php
App::uses('AppModel', 'Model');

class EmailQueue extends AppModel
{
	public $name = 'EmailQueue';
	public $useTable = 'emails_queue';
	public $actsAs = array('Containable');
}
