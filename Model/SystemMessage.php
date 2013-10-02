<?php
App::uses('AppModel', 'Model');

class SystemMessage extends AppModel
{
	public $name = 'SystemMessage';
	public $useTable = 'system_messages';
	public $actsAs = array('Containable');
}
