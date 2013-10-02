<?php
App::uses('AppModel', 'Model');

class Photo extends AppModel
{
	public $name = 'Photo';
	public $useTable = 'files';
	public $whitelist = array('filename', 'dir', 'mimetype', 'filesize');
	
	public $actsAs = array(
		'Containable',
		'MeioUpload.MeioUpload' => array(
			'filename' => array(
				'dir' => 'files{DS}photo',
				'uploadName' => 'hash',
				'allowedMime' => array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'),
				'allowedExt' => array('.jpg', '.jpeg', '.png', '.gif'),
			)
		)
	);
	
	public $validate = array(
	    'filename' => array(
	    	'HttpPost' => array(
				'rule' => array('uploadCheckHttpPost'),
				'check' => false
			)
		)
	);
}
