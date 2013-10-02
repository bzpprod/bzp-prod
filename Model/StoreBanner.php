<?php
App::uses('Photo', 'Model');

class StoreBanner extends Photo
{
	public $name = 'StoreBanner';
	public $modelAttribute = 'StoreBanner';
	
	public $actsAs = array(
		'Containable',
		'MeioUpload.MeioUpload' => array(
			'filename' => array(
				'dir' => 'files{DS}store{DS}banner',
				'uploadName' => 'hash',
				'allowedMime' => array('image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'),
				'allowedExt' => array('.jpg', '.jpeg', '.png', '.gif'),
				'fixedAspectRatio' => 'C',
				'thumbnailQuality' => 100,
				'thumbsizes' => array(
					'extralarge'  => array('width' => 823, 'height' => 136)
				)
			)
		)
	);
	
	public $belongsTo = array(
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'foreign_key'
		)
	);
}
